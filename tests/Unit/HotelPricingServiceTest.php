<?php

namespace Tests\Unit;

use App\Models\BookingCode;
use App\Models\HotelPackage;
use App\Models\HotelPrice;
use App\Models\HotelPromo;
use App\Models\HotelRoom;
use App\Models\Hotels;
use App\Models\Orders;
use App\Services\Hotels\HotelPricingService;
use App\Services\Hotels\HotelNormalPriceOverlapValidator;
use App\ViewModels\Hotels\HotelDetailViewModel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class HotelPricingServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!$this->usingSafeSqliteMemoryDatabase()) {
            $this->markTestSkipped('Hotel pricing tests require sqlite :memory: to avoid touching active data.');
        }

        Cache::flush();
        Carbon::setTestNow('2026-07-27 12:00:00');
        $this->prepareSchema();
        $this->seedReferenceRows();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_normal_rate_uses_authoritative_nightly_database_rates(): void
    {
        HotelPrice::create([
            'hotels_id' => 1,
            'rooms_id' => 1,
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-01',
            'contract_rate' => 1500000,
            'markup' => 20,
            'kick_back' => 5,
            'author' => 1,
        ]);
        HotelPrice::create([
            'hotels_id' => 1,
            'rooms_id' => 1,
            'start_date' => '2026-08-02',
            'end_date' => '2026-08-02',
            'contract_rate' => 3000000,
            'markup' => 30,
            'kick_back' => 7,
            'author' => 1,
        ]);

        $pricing = app(HotelPricingService::class)->calculateNormalRate([
            'hotel_id' => 1,
            'room_id' => 1,
            'checkin' => '2026-08-01',
            'checkout' => '2026-08-03',
            'rooms' => 2,
            'user_id' => 10,
            'extra_bed_total' => 11,
            'airport_shuttle_total' => 13,
        ]);

        $this->assertSame(350, $pricing['price_pax']);
        $this->assertSame(700, $pricing['normal_price']);
        $this->assertSame(600, $pricing['contract_rate_total']);
        $this->assertSame(100, $pricing['markup_total']);
        $this->assertSame(0, $pricing['tax_total']);
        $this->assertSame(24, $pricing['kick_back_total']);
        $this->assertSame(687, $pricing['price_total']);
        $this->assertSame(700, $pricing['grand_total']);
        $this->assertSame(
            $pricing['normal_price'],
            collect($pricing['nightly_breakdown'])->sum('published_rate') * $pricing['rooms']
        );
        $this->assertArrayHasKey('contract_rate_usd', $pricing['nightly_breakdown'][0]);
        $this->assertArrayHasKey('markup_usd', $pricing['nightly_breakdown'][0]);
        $this->assertArrayHasKey('tax_usd', $pricing['nightly_breakdown'][0]);
    }

    public function test_rounding_uses_whole_usd_ceiling_without_cumulative_drift(): void
    {
        DB::table('usd_rates')->where('id', 1)->update(['rate' => 14000.50]);
        DB::table('taxes')->where('id', 1)->update(['tax' => 11.5]);

        HotelPrice::create([
            'hotels_id' => 1,
            'rooms_id' => 1,
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-03',
            'contract_rate' => 100000,
            'markup' => 2,
            'kick_back' => 0,
            'author' => 1,
        ]);

        $pricing = app(HotelPricingService::class)->calculateNormalRate([
            'hotel_id' => 1,
            'room_id' => 1,
            'checkin' => '2026-08-01',
            'checkout' => '2026-08-04',
            'rooms' => 2,
            'user_id' => 10,
        ]);

        $this->assertSame(3, $pricing['nights']);
        $this->assertSame(72, $pricing['normal_price']);
        $this->assertSame(48, $pricing['contract_rate_total']);
        $this->assertSame(12, $pricing['markup_total']);
        $this->assertSame(12, $pricing['tax_total']);
        $this->assertSame(72, collect($pricing['nightly_breakdown'])->sum('published_rate') * $pricing['rooms']);
        $this->assertSame($pricing['grand_total'], $pricing['price_total']);
    }

    public function test_rate_breakdown_matches_frontend_contract_for_package_and_kickback(): void
    {
        $breakdown = app(HotelPricingService::class)->rateBreakdown(
            1500000,
            20,
            (object) ['rate' => 15000],
            (object) ['tax' => 10],
            2,
            10
        );

        $this->assertSame(3000000.0, $breakdown['effective_contract_rate_idr']);
        $this->assertSame(200, $breakdown['contract_rate_usd']);
        $this->assertSame(20, $breakdown['markup_usd']);
        $this->assertSame(220, $breakdown['subtotal_usd']);
        $this->assertSame(22, $breakdown['tax_usd']);
        $this->assertSame(242, $breakdown['published_rate']);
        $this->assertSame(232, $breakdown['net_rate']);
    }

    public function test_normal_price_overlap_is_rejected_at_crud_boundary(): void
    {
        HotelPrice::create([
            'hotels_id' => 1,
            'rooms_id' => 1,
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-10',
            'contract_rate' => 1500000,
            'markup' => 20,
            'kick_back' => 0,
            'author' => 1,
        ]);

        $this->expectException(ValidationException::class);

        DB::transaction(fn () => app(HotelNormalPriceOverlapValidator::class)->ensureAvailable(
            1,
            1,
            '2026-08-10',
            '2026-08-15'
        ));
    }

    public function test_backend_price_rows_show_published_net_and_legacy_conflict_consistently(): void
    {
        $room = new HotelRoom(['hotels_id' => 1, 'rooms' => 'Suite']);
        $room->id = 1;
        $prices = collect([
            new HotelPrice([
                'hotels_id' => 1,
                'rooms_id' => 1,
                'start_date' => '2026-08-01',
                'end_date' => '2026-08-10',
                'contract_rate' => 1500000,
                'markup' => 20,
                'kick_back' => 10,
            ]),
            new HotelPrice([
                'hotels_id' => 1,
                'rooms_id' => 1,
                'start_date' => '2026-08-10',
                'end_date' => '2026-08-20',
                'contract_rate' => 1500000,
                'markup' => 20,
                'kick_back' => 10,
            ]),
        ]);

        foreach ($prices as $index => $price) {
            $price->id = $index + 1;
            $price->setRelation('rooms', $room);
        }

        $viewModel = new HotelDetailViewModel(
            hotel: new Hotels(['name' => 'Hotel One', 'status' => 'Active']),
            rooms: collect([$room]),
            normalPrices: $prices,
            promos: collect(),
            packages: collect(),
            additionalCharges: collect(),
            contracts: collect(),
            usdRate: (object) ['rate' => 15000],
            tax: (object) ['tax' => 10],
            now: '2026-08-05',
            latestPrice: null,
            author: null,
            pricingService: app(HotelPricingService::class),
        );

        $rows = $viewModel->normalPriceRows();

        $this->assertSame([132, 132], $rows->pluck('published_rate')->all());
        $this->assertSame([122, 122], $rows->pluck('net_rate')->all());
        $this->assertSame(['Conflict', 'Conflict'], $rows->pluck('status_label')->all());
        $this->assertSame(['danger', 'danger'], $rows->pluck('status_tone')->all());
    }

    public function test_normal_rate_rejects_missing_nightly_rate(): void
    {
        HotelPrice::create([
            'hotels_id' => 1,
            'rooms_id' => 1,
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-01',
            'contract_rate' => 1500000,
            'markup' => 20,
            'kick_back' => 5,
            'author' => 1,
        ]);

        $this->expectException(ValidationException::class);

        app(HotelPricingService::class)->calculateNormalRate([
            'hotel_id' => 1,
            'room_id' => 1,
            'checkin' => '2026-08-01',
            'checkout' => '2026-08-03',
            'rooms' => 1,
            'user_id' => 10,
        ]);
    }

    public function test_order_pricing_fails_closed_when_usd_rate_is_invalid(): void
    {
        DB::table('usd_rates')->where('id', 1)->update(['rate' => 0]);

        $this->expectException(ValidationException::class);

        app(HotelPricingService::class)->calculateNormalRate([
            'hotel_id' => 1,
            'room_id' => 1,
            'checkin' => '2026-08-01',
            'checkout' => '2026-08-02',
            'rooms' => 1,
            'user_id' => 10,
        ]);
    }

    public function test_normal_rate_rejects_overlapping_nightly_rates(): void
    {
        foreach ([1, 2] as $id) {
            HotelPrice::create([
                'hotels_id' => 1,
                'rooms_id' => 1,
                'start_date' => '2026-08-01',
                'end_date' => '2026-08-01',
                'contract_rate' => 1500000 + $id,
                'markup' => 20,
                'kick_back' => 5,
                'author' => 1,
            ]);
        }

        $this->expectException(ValidationException::class);

        app(HotelPricingService::class)->calculateNormalRate([
            'hotel_id' => 1,
            'room_id' => 1,
            'checkin' => '2026-08-01',
            'checkout' => '2026-08-02',
            'rooms' => 1,
            'user_id' => 10,
        ]);
    }

    public function test_promo_rate_rejects_promo_from_other_room_or_hotel(): void
    {
        HotelPromo::create([
            'hotels_id' => 2,
            'rooms_id' => 2,
            'name' => 'Wrong Promo',
            'book_periode_start' => '2026-07-01',
            'book_periode_end' => '2026-08-31',
            'periode_start' => '2026-08-01',
            'periode_end' => '2026-08-31',
            'minimum_stay' => 1,
            'contract_rate' => 750000,
            'markup' => 10,
            'email_status' => false,
            'send_to_specific_email' => false,
            'specific_email' => '',
            'status' => 'Active',
            'author' => 1,
        ]);

        $this->expectException(ValidationException::class);

        app(HotelPricingService::class)->calculatePromoRate([
            'hotel_id' => 1,
            'room_id' => 1,
            'promo_ids' => [1],
            'checkin' => '2026-08-01',
            'checkout' => '2026-08-02',
            'rooms' => 1,
            'user_id' => 10,
        ]);
    }

    public function test_package_rate_rejects_wrong_duration(): void
    {
        HotelPackage::create([
            'hotels_id' => 1,
            'rooms_id' => 1,
            'name' => 'Two Nights',
            'duration' => 2,
            'stay_period_start' => '2026-08-01',
            'stay_period_end' => '2026-08-31',
            'contract_rate' => 3000000,
            'markup' => 30,
            'status' => 'Active',
            'author' => 1,
        ]);

        $this->expectException(ValidationException::class);

        app(HotelPricingService::class)->calculatePackageRate([
            'hotel_id' => 1,
            'room_id' => 1,
            'package_id' => 1,
            'checkin' => '2026-08-01',
            'checkout' => '2026-08-02',
            'rooms' => 1,
            'user_id' => 10,
        ]);
    }

    public function test_booking_code_discount_is_clamped_and_cannot_be_reused_by_same_user(): void
    {
        BookingCode::create([
            'name' => 'Summer',
            'code' => 'SUMMER',
            'discounts' => 999,
            'amount' => 5,
            'used' => 0,
            'author' => 1,
            'expired_date' => '2026-12-31',
            'status' => 'Active',
        ]);

        [$bookingCode, $discount] = app(HotelPricingService::class)->resolveBookingCodeForOrder('summer', 10, 120);
        $this->assertSame('SUMMER', $bookingCode->code);
        $this->assertSame(120, $discount);

        Orders::create([
            'orderno' => 'ORD-USED',
            'user_id' => 10,
            'sales_agent' => 10,
            'name' => 'Agent',
            'email' => 'agent@example.test',
            'servicename' => 'Hotel',
            'service' => 'Hotel',
            'status' => 'Pending',
            'bookingcode' => 'SUMMER',
        ]);

        [$bookingCode, $discount] = app(HotelPricingService::class)->resolveBookingCodeForOrder('SUMMER', 10, 120);
        $this->assertNull($bookingCode);
        $this->assertSame(0, $discount);
    }

    private function usingSafeSqliteMemoryDatabase(): bool
    {
        return config('database.default') === 'sqlite'
            && config('database.connections.sqlite.database') === ':memory:';
    }

    private function prepareSchema(): void
    {
        foreach ([
            'orders',
            'booking_codes',
            'promotions',
            'hotel_packages',
            'hotel_promos',
            'hotel_prices',
            'hotel_rooms',
            'hotels',
            'taxes',
            'usd_rates',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::create('usd_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('rate', 14, 2)->default(1);
            $table->decimal('sell', 14, 2)->default(1);
            $table->decimal('buy', 14, 2)->default(1);
            $table->decimal('difference', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->decimal('tax', 8, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('region')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotels_id')->nullable();
            $table->string('rooms')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotels_id')->nullable();
            $table->unsignedBigInteger('rooms_id')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('markup')->nullable();
            $table->integer('kick_back')->nullable();
            $table->integer('contract_rate');
            $table->integer('author');
            $table->timestamps();
        });

        Schema::create('hotel_promos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotels_id')->nullable();
            $table->unsignedBigInteger('rooms_id')->nullable();
            $table->string('name');
            $table->date('book_periode_start');
            $table->date('book_periode_end');
            $table->date('periode_start');
            $table->date('periode_end');
            $table->integer('minimum_stay');
            $table->integer('contract_rate');
            $table->integer('markup');
            $table->boolean('email_status')->default(false);
            $table->boolean('send_to_specific_email')->default(false);
            $table->longText('specific_email')->nullable();
            $table->string('status');
            $table->integer('author');
            $table->longText('benefits')->nullable();
            $table->longText('include')->nullable();
            $table->longText('additional_info')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotels_id')->nullable();
            $table->unsignedBigInteger('rooms_id')->nullable();
            $table->string('name');
            $table->string('duration');
            $table->date('stay_period_start');
            $table->date('stay_period_end');
            $table->integer('contract_rate');
            $table->integer('markup');
            $table->string('status');
            $table->integer('author');
            $table->longText('benefits')->nullable();
            $table->longText('include')->nullable();
            $table->longText('additional_info')->nullable();
            $table->timestamps();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->integer('author_id')->default(1);
            $table->string('name');
            $table->string('discounts');
            $table->string('periode_start');
            $table->string('periode_end');
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('booking_codes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('discounts');
            $table->integer('amount');
            $table->integer('used');
            $table->integer('author');
            $table->date('expired_date');
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('orderno')->nullable();
            $table->string('confirmation_order')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('sales_agent')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('servicename')->nullable();
            $table->string('service')->nullable();
            $table->string('status')->nullable();
            $table->text('bookingcode')->nullable();
            $table->text('bookingcode_disc')->nullable();
            $table->timestamps();
        });
    }

    private function seedReferenceRows(): void
    {
        DB::table('usd_rates')->insert(['id' => 1, 'name' => 'USD', 'rate' => 15000, 'sell' => 15000, 'buy' => 15000, 'difference' => 0]);
        DB::table('taxes')->insert(['id' => 1, 'tax' => 0]);
        DB::table('hotels')->insert(['id' => 1, 'name' => 'Hotel One', 'code' => 'H1', 'region' => 'Bali', 'status' => 'Active']);
        DB::table('hotels')->insert(['id' => 2, 'name' => 'Hotel Two', 'code' => 'H2', 'region' => 'Bali', 'status' => 'Active']);
        DB::table('hotel_rooms')->insert(['id' => 1, 'hotels_id' => 1, 'rooms' => 'Suite']);
        DB::table('hotel_rooms')->insert(['id' => 2, 'hotels_id' => 2, 'rooms' => 'Other Suite']);
    }
}
