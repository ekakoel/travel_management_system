<?php

namespace Tests\Feature\Pricing;

use App\Models\OrderPricingSnapshot;
use App\Models\Orders;
use App\Models\Tours;
use App\Services\Pricing\OrderPricingSnapshotWriter;
use App\Services\Pricing\OrderPricingSnapshotReader;
use App\Services\Tours\TourPackagePricingService;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Exceptions\PricingException;
use LogicException;
use Tests\TestCase;

class TourPackageOrderSnapshotTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:') {
            $this->fail('Tour pricing snapshot tests require SQLite :memory:.');
        }

        Carbon::setTestNow('2026-07-29 12:00:00');
        CarbonImmutable::setTestNow('2026-07-29 12:00:00');
        $this->createSchema();
        $this->seedPricing();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_authoritative_quote_can_be_committed_as_immutable_order_snapshot(): void
    {
        $tour = Tours::findOrFail(1);
        $quote = app(TourPackagePricingService::class)->quote(
            $tour,
            2,
            Carbon::parse('2026-08-10'),
            1,
            null,
            null,
            1
        );
        $order = Orders::create([
            'user_id' => 1,
            'orderno' => 'TST260729A',
            'confirmation_order' => '',
            'service' => Orders::PUBLIC_TOUR_SERVICE,
            'service_id' => 1,
            'final_price' => '181.50',
        ]);

        $snapshot = app(OrderPricingSnapshotWriter::class)
            ->commit($order, $quote, 1);
        $order->refresh();

        $this->assertSame($snapshot->id, $order->pricing_snapshot_id);
        $this->assertSame($quote->finalTotalIdr(), $order->final_total_idr);
        $this->assertSame($quote->finalTotalUsdMinor(), $order->final_total_usd_minor);
        $this->assertSame($quote->finalTotalUsdMinor(), $snapshot->final_total_usd_minor);
        $this->assertSame($snapshot->id, $order->activePricingSnapshot->id);

        DB::table('usd_rates')->where('id', 1)->update(['sell' => '20000']);
        DB::table('tax_policies')->where('id', 1)->update(['percentage_scaled' => 2_000_000]);
        $invoiceValues = app(OrderPricingSnapshotReader::class)->invoiceValues($order);

        $this->assertSame('166.65', $invoiceValues['total_usd']);
        $this->assertSame(2_666_400, $invoiceValues['total_idr']);
        $this->assertSame('16000.000000', $invoiceValues['rate_usd']);

        $this->expectException(LogicException::class);
        $snapshot->update(['reason' => 'mutation is forbidden']);
    }

    public function test_legacy_tour_fallback_uses_only_stored_order_values_and_logs_warning(): void
    {
        Log::spy();
        $order = Orders::create([
            'user_id' => 1,
            'orderno' => 'LEGACY-TOUR',
            'confirmation_order' => '',
            'service' => Orders::PUBLIC_TOUR_SERVICE,
            'service_id' => 1,
            'final_price' => '90.75',
            'price_total' => '100.00',
            'price_pax' => '50.00',
            'usd_rate' => '16000',
        ]);

        DB::table('usd_rates')->where('id', 1)->update(['sell' => '25000']);
        $values = app(OrderPricingSnapshotReader::class)->historicalValues($order);

        $this->assertSame('90.75', $values['total_usd']);
        $this->assertSame(1_452_000, $values['total_idr']);
        $this->assertSame('16000.000000', $values['rate_usd']);
        $this->assertTrue($values['legacy_fallback']);
        Log::shouldHaveReceived('warning')
            ->once()
            ->with('Legacy Tour Package pricing fallback used.', \Mockery::type('array'));
    }

    public function test_new_tour_without_snapshot_fails_closed_and_other_services_are_rejected(): void
    {
        $newTour = Orders::create([
            'user_id' => 1,
            'orderno' => 'NEW-NO-SNAPSHOT',
            'confirmation_order' => '',
            'service' => Orders::PUBLIC_TOUR_SERVICE,
            'service_id' => 1,
            'final_price' => '90.75',
            'usd_rate' => '16000',
            'pricing_version' => 'tour-pricing-v1',
        ]);

        try {
            app(OrderPricingSnapshotReader::class)->historicalValues($newTour);
            $this->fail('New Tour orders without a snapshot must fail closed.');
        } catch (PricingException $exception) {
            $this->assertSame('PRICING_ORDER_SNAPSHOT_MISSING', $exception->pricingCode);
        }

        $accommodation = Orders::create([
            'user_id' => 1,
            'orderno' => 'NON-TOUR',
            'confirmation_order' => '',
            'service' => 'Hotel',
            'service_id' => 1,
            'final_price' => '90.75',
            'usd_rate' => '16000',
        ]);

        $this->expectException(PricingException::class);
        app(OrderPricingSnapshotReader::class)->historicalValues($accommodation);
    }

    private function createSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
        });
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('tour_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_id');
            $table->unsignedInteger('min_qty');
            $table->unsignedInteger('max_qty');
            $table->unsignedBigInteger('contract_rate_idr');
            $table->string('markup_amount', 32);
            $table->string('markup_type', 16)->nullable();
            $table->char('markup_currency', 3);
            $table->string('markup_source');
            $table->dateTime('markup_verified_at');
            $table->unsignedBigInteger('markup_verified_by');
            $table->string('pricing_data_status');
            $table->date('valid_from');
            $table->date('valid_until');
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('usd_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rate');
            $table->string('sell');
            $table->dateTime('retrieved_at', 6);
            $table->string('retrieval_source');
            $table->timestamps();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('orderno');
            $table->string('confirmation_order')->default('');
            $table->string('service');
            $table->unsignedBigInteger('service_id');
            $table->decimal('final_price', 20, 2)->nullable();
            $table->decimal('price_total', 20, 2)->nullable();
            $table->decimal('price_pax', 20, 2)->nullable();
            $table->decimal('discounts', 20, 2)->nullable();
            $table->decimal('bookingcode_disc', 20, 2)->nullable();
            $table->decimal('additional_service_total_price', 20, 2)->nullable();
            $table->decimal('usd_rate', 20, 6)->nullable();
            $table->string('pricing_version')->nullable();
            $table->unsignedBigInteger('pricing_snapshot_id')->nullable();
            $table->char('base_currency', 3)->nullable();
            $table->char('display_currency', 3)->nullable();
            $table->unsignedBigInteger('final_total_idr')->nullable();
            $table->unsignedBigInteger('final_total_usd_minor')->nullable();
            $table->dateTime('pricing_calculated_at', 6)->nullable();
            $table->timestamps();
        });
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->string('pricing_data_status');
            $table->string('service_scope');
            $table->string('discount_type');
            $table->string('discount_value');
            $table->string('discount_currency')->nullable();
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable();
        });
        Schema::create('booking_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('status');
            $table->string('pricing_data_status');
            $table->string('service_scope');
            $table->string('discount_type');
            $table->string('discount_value');
            $table->string('discount_currency')->nullable();
            $table->unsignedInteger('used')->default(0);
            $table->unsignedInteger('amount')->default(1);
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable();
        });

        (require database_path('migrations/2026_07_29_170200_create_tax_policies_table.php'))->up();
        (require database_path('migrations/2026_07_29_170300_create_order_pricing_snapshots_table.php'))->up();
    }

    private function seedPricing(): void
    {
        DB::table('users')->insert(['id' => 1]);
        DB::table('tours')->insert([
            'id' => 1,
            'status' => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('tour_prices')->insert([
            'id' => 1,
            'tour_id' => 1,
            'min_qty' => 2,
            'max_qty' => 4,
            'contract_rate_idr' => 1_000_000,
            'markup_amount' => '20.000000',
            'markup_currency' => 'USD',
            'markup_source' => 'finance-approved',
            'markup_verified_at' => now(),
            'markup_verified_by' => 1,
            'pricing_data_status' => 'ready',
            'valid_from' => '2026-01-01',
            'valid_until' => '2026-12-31',
            'status' => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('usd_rates')->insert([
            'id' => 1,
            'name' => 'USD',
            'rate' => '16000',
            'sell' => '16000',
            'retrieved_at' => now()->subHour(),
            'retrieval_source' => 'test-fixture',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('tax_policies')->insert([
            'id' => 1,
            'service' => 'Tour Package',
            'name' => 'Tour Tax Fixture',
            'percentage_scaled' => 1_000_000,
            'percentage_scale' => 1_000_000,
            'calculation_type' => 'exclusive',
            'taxable_base' => 'contract_plus_markup',
            'status' => 'active',
            'effective_from' => now()->subDay(),
            'approved_by' => 1,
            'approved_at' => now()->subDay(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
