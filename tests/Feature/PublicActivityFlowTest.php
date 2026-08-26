<?php

namespace Tests\Feature;

use App\Http\Middleware\LogActivityMiddleware;
use App\Http\Middleware\TrackWebsiteVisit;
use App\Http\Middleware\UserActivity;
use App\Http\Requests\UpdateActivityAdminRequest;
use App\Exceptions\PricingException;
use App\Models\Activities;
use App\Models\InvoiceAdmin;
use App\Models\Orders;
use App\Models\PaymentConfirmation;
use App\Models\Reservation;
use App\Models\User;
use App\Services\AccommodationFinancialFileService;
use App\Services\ActivityOrderLifecycleService;
use App\Services\Activities\ActivityGuestListService;
use App\Services\Activities\ActivityInventoryService;
use App\Services\Activities\ActivityPricingService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use ZipArchive;

class PublicActivityFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') {
            $this->markTestSkipped('Public Activity flow tests require sqlite :memory: to avoid touching active data.');
        }

        $this->withoutMiddleware([
            LogActivityMiddleware::class,
            TrackWebsiteVisit::class,
            UserActivity::class,
        ]);

        Cache::flush();
        Mail::fake();
        Storage::fake('private');
        Carbon::setTestNow('2026-07-27 12:00:00');
        $this->prepareSchema();
        $this->seedReferenceRows();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_activity_order_uses_authoritative_price_snapshot_and_creates_pending_reservation(): void
    {
        $user = $this->actingUser();

        $this->actingAs($user)
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                'price_total' => 1,
                'final_price' => 1,
            ]))
            ->assertRedirect(route('view.detail-order-activity', ['id' => 1]));

        $order = DB::table('orders')->where('service', 'Activity')->first();

        $this->assertSame('Pending', $order->status);
        $this->assertSame('Test Activity', $order->subservice);
        $this->assertSame('Partner A', $order->servicename);
        $this->assertSame('Hotel Nikko Bali Benoa', $order->pickup_location);
        $this->assertSame('Ubud Center', $order->dropoff_location);
        $this->assertSame('150', (string) $order->price_pax);
        $this->assertSame('300', (string) $order->price_total);
        $this->assertSame('300', (string) $order->final_price);
        $this->assertSame('Activity include snapshot', $order->include);
        $this->assertSame('Activity itinerary snapshot', $order->itinerary);
        $this->assertSame('2026-08-10 09:00:00', $order->checkin);
        $this->assertSame('2026-08-10 11:00:00', $order->checkout);
        $this->assertSame('2026-08-10 09:00:00', $order->pickup_date);
        $this->assertSame('2026-08-10 11:00:00', $order->dropoff_date);
        $this->assertStringNotContainsString('Guest leader', (string) $order->guest_detail);
        $this->assertNotNull($order->rsv_id);
        $this->assertDatabaseHas('reservations', [
            'id' => $order->rsv_id,
            'service' => 'Activity',
            'checkin' => '2026-08-10 09:00:00',
            'checkout' => '2026-08-10 11:00:00',
            'pickup_date' => '2026-08-10 09:00:00',
            'dropoff_date' => '2026-08-10 11:00:00',
            'status' => 'Pending',
        ]);
        $this->assertSame(2, DB::table('guests')->where('order_id', $order->id)->where('rsv_id', $order->rsv_id)->count());

        $this->actingAs($user)
            ->get(route('view.detail-order-activity', ['id' => $order->id]))
            ->assertOk()
            ->assertSee('Hotel Nikko Bali Benoa')
            ->assertSee('Ubud Center')
            ->assertSee('$150,00')
            ->assertSee('$300,00');
    }

    public function test_activity_timing_uses_duration_units_for_order_and_reservation_dates(): void
    {
        $user = $this->actingUser();

        $cases = [
            [
                'duration' => '15 Minutes',
                'travel_date' => '2026-08-29 09:00:00',
                'expected_end' => '2026-08-29 09:15:00',
            ],
            [
                'duration' => '1 Hour',
                'travel_date' => '2026-08-29 09:00:00',
                'expected_end' => '2026-08-29 10:00:00',
            ],
            [
                'duration' => '2 Hours',
                'travel_date' => '2026-08-29 09:00:00',
                'expected_end' => '2026-08-29 11:00:00',
            ],
            [
                'duration' => '2 Hours',
                'travel_date' => '2026-08-29 23:30:00',
                'expected_end' => '2026-08-30 01:30:00',
            ],
        ];

        foreach ($cases as $index => $case) {
            DB::table('activities')->where('id', 1)->update([
                'duration' => $case['duration'],
            ]);

            $this->actingAs($user)
                ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                    'submission_token' => 'activity-timing-'.$index,
                    'travel_date' => $case['travel_date'],
                ]))
                ->assertRedirect();

            $order = DB::table('orders')->where('service', 'Activity')->latest('id')->first();

            $this->assertSame($case['travel_date'], $order->checkin);
            $this->assertSame($case['expected_end'], $order->checkout);
            $this->assertSame($case['travel_date'], $order->pickup_date);
            $this->assertSame($case['expected_end'], $order->dropoff_date);

            $this->assertDatabaseHas('reservations', [
                'id' => $order->rsv_id,
                'checkin' => $case['travel_date'],
                'checkout' => $case['expected_end'],
                'pickup_date' => $case['travel_date'],
                'dropoff_date' => $case['expected_end'],
            ]);
        }
    }

    public function test_activity_order_detail_displays_activity_timing_as_datetime(): void
    {
        DB::table('activities')->where('id', 1)->update([
            'duration' => '15 Minutes',
        ]);
        $user = $this->actingUser();

        $this->actingAs($user)
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                'travel_date' => '2026-08-29 09:00:00',
            ]))
            ->assertRedirect(route('view.detail-order-activity', ['id' => 1]));

        $this->actingAs($user)
            ->get(route('view.detail-order-activity', ['id' => 1]))
            ->assertOk()
            ->assertSee('09:00')
            ->assertSee('09:15')
            ->assertDontSee('2026-08-30 00:00:00');
    }

    public function test_frontend_order_detail_entry_points_resolve_activity_canonical_detail(): void
    {
        $user = $this->actingUser();
        $order = $this->insertApprovedActivityOrder($user);

        $this->actingAs($user)
            ->get(route('view.detail-order', ['id' => $order->id]))
            ->assertRedirect(route('view.detail-order-activity', ['id' => $order->id]));

        $this->actingAs($user)
            ->get(route('view.detail-order-hotel', ['id' => $order->id]))
            ->assertRedirect(route('view.detail-order-activity', ['id' => $order->id]));
    }

    public function test_activity_order_detail_uses_snapshot_when_master_activity_is_missing(): void
    {
        $user = $this->actingUser();
        $order = $this->insertApprovedActivityOrder($user, [
            'subservice' => 'Historical Activity Snapshot',
            'service_type' => 'Historical Type',
            'duration' => '15 Minutes',
            'checkin' => '2026-08-29 09:00:00',
            'checkout' => '2026-08-29 09:15:00',
            'pickup_location' => 'Historical Pickup',
            'dropoff_location' => 'Historical Dropoff',
        ]);

        DB::table('activities')->where('id', $order->service_id)->delete();

        $this->actingAs($user)
            ->get(route('view.detail-order-activity', ['id' => $order->id]))
            ->assertOk()
            ->assertSee('Historical Activity Snapshot')
            ->assertSee('Historical Pickup')
            ->assertSee('Historical Dropoff')
            ->assertSee('09:00')
            ->assertSee('09:15')
            ->assertDontSee('messages.Package Highlights');
    }

    public function test_activity_order_requires_pickup_and_dropoff_locations(): void
    {
        $this->actingAs($this->actingUser())
            ->from(route('view.activity-public-detail', ['code' => 'ACT-001']))
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                'pickup_location' => '',
            ]))
            ->assertSessionHasErrors('pickup_location');

        $this->actingAs($this->actingUser(11, 'dropoff@example.test'))
            ->from(route('view.activity-public-detail', ['code' => 'ACT-001']))
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                'submission_token' => 'missing-dropoff-location',
                'dropoff_location' => '   ',
            ]))
            ->assertSessionHasErrors('dropoff_location');

        $this->assertSame(0, DB::table('orders')->where('service', 'Activity')->count());
    }

    public function test_activity_quote_uses_sell_rate_and_current_promotions_for_selected_travel_date(): void
    {
        DB::table('usd_rates')->where('name', 'USD')->update([
            'rate' => 1,
            'sell' => 15000,
            'updated_at' => now(),
        ]);
        DB::table('promotions')->insert([
            'name' => 'Current Activity Promotion',
            'discounts' => 10,
            'periode_start' => '2026-07-26',
            'periode_end' => '2026-07-28',
            'status' => 'Active',
        ]);
        $user = $this->actingUser();

        $this->actingAs($user)
            ->postJson(route('activity.quote', ['code' => 'ACT-001']), [
                'number_of_guests' => 2,
                'travel_date' => '2026-08-10 09:00:00',
            ])
            ->assertOk()
            ->assertJsonPath('price_available', true)
            ->assertJsonPath('quote.rate_side', 'sell')
            ->assertJsonPath('quote.unit_price_usd_minor', 15000)
            ->assertJsonPath('quote.gross_total_usd_minor', 30000)
            ->assertJsonPath('quote.discount_total_usd_minor', 1000)
            ->assertJsonPath('quote.final_total_usd_minor', 29000)
            ->assertJsonPath('display.unit_price_usd', '150.00')
            ->assertJsonPath('display.final_total_usd', '290.00');

        $this->actingAs($user)
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload())
            ->assertRedirect();

        $order = DB::table('orders')->where('service', 'Activity')->first();

        $this->assertSame('150', (string) $order->price_pax);
        $this->assertSame('300', (string) $order->price_total);
        $this->assertSame('290', (string) $order->final_price);
        $this->assertSame('15000', (string) $order->usd_rate);
    }

    public function test_activity_price_rounds_up_to_the_next_whole_usd(): void
    {
        DB::table('usd_rates')->where('name', 'USD')->update([
            'rate' => 100,
            'sell' => 100,
            'updated_at' => now(),
        ]);
        $user = $this->actingUser();

        foreach ([
            1100 => 1100,
            1101 => 1200,
            1120 => 1200,
            1199 => 1200,
            1200 => 1200,
        ] as $contractRate => $expectedUsdMinor) {
            DB::table('activities')->where('id', 1)->update([
                'contract_rate' => $contractRate,
                'markup' => 0,
            ]);

            $this->actingAs($user)
                ->postJson(route('activity.quote', ['code' => 'ACT-001']), [
                    'number_of_guests' => 2,
                    'travel_date' => '2026-08-10 09:00:00',
                ])
                ->assertOk()
                ->assertJsonPath('quote.rounding_policy', 'ceiling-whole-usd-v1')
                ->assertJsonPath('quote.unit_price_usd_minor', $expectedUsdMinor)
                ->assertJsonPath('quote.gross_total_usd_minor', $expectedUsdMinor * 2);
        }
    }

    public function test_activity_pricing_uses_precise_intermediate_calculation_before_whole_usd_ceiling(): void
    {
        DB::table('activities')->where('id', 1)->update([
            'contract_rate' => 10995,
            'markup' => 0,
        ]);
        DB::table('usd_rates')->where('name', 'USD')->update([
            'rate' => 1000,
            'sell' => 1000,
            'updated_at' => now(),
        ]);
        DB::table('taxes')->where('name', 'tax')->update([
            'tax' => 0.04,
        ]);

        $this->actingAs($this->actingUser())
            ->postJson(route('activity.quote', ['code' => 'ACT-001']), [
                'number_of_guests' => 2,
                'travel_date' => '2026-08-10 09:00:00',
            ])
            ->assertOk()
            ->assertJsonPath('quote.rounding_policy', 'ceiling-whole-usd-v1')
            ->assertJsonPath('quote.unit_price_usd_minor', 1100)
            ->assertJsonPath('quote.gross_total_usd_minor', 2200)
            ->assertJsonPath('display.unit_price_usd', '11.00');
    }

    public function test_activity_price_validity_uses_selected_activity_date(): void
    {
        DB::table('activities')->where('id', 1)->update([
            'validity' => '2026-08-31',
        ]);
        $user = $this->actingUser();

        foreach (['2026-08-30 09:00:00', '2026-08-31 09:00:00'] as $travelDate) {
            $this->actingAs($user)
                ->postJson(route('activity.quote', ['code' => 'ACT-001']), [
                    'number_of_guests' => 2,
                    'travel_date' => $travelDate,
                ])
                ->assertOk()
                ->assertJsonPath('price_available', true)
                ->assertJsonPath('quote.valid_until', '2026-08-31');
        }

        $this->actingAs($user)
            ->postJson(route('activity.quote', ['code' => 'ACT-001']), [
                'number_of_guests' => 2,
                'travel_date' => '2026-09-01 09:00:00',
            ])
            ->assertUnprocessable()
            ->assertJsonPath('price_available', false)
            ->assertJsonPath('code', 'ACTIVITY_PRICE_DATE_OUT_OF_VALIDITY');

        $this->actingAs($user)
            ->from(route('view.activity-public-detail', ['code' => 'ACT-001']))
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                'travel_date' => '2026-09-01 09:00:00',
                'price_total' => 1,
                'final_price' => 1,
            ]))
            ->assertSessionHasErrors('travel_date');

        $this->assertSame(0, DB::table('orders')->where('service', 'Activity')->count());
    }

    public function test_activity_booking_wizard_does_not_expose_guest_leader(): void
    {
        $this->actingAs($this->actingUser())
            ->get(route('view.activity-public-detail', ['code' => 'ACT-001']))
            ->assertOk()
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('name="guest_list"', false)
            ->assertSee('name="pickup_location"', false)
            ->assertSee('name="dropoff_location"', false)
            ->assertSee('data-activity-order-review="pickup_location"', false)
            ->assertSee('data-activity-order-review="dropoff_location"', false)
            ->assertSee('data-activity-guest-list', false)
            ->assertSee(route('activity.guest-list-template', ['format' => 'xlsx']), false)
            ->assertSee(route('activity.guest-list-template', ['format' => 'csv']), false)
            ->assertDontSee('data-activity-guest-field="is_leader"', false)
            ->assertDontSee('Guest leader', false)
            ->assertDontSee('Set leader', false);
    }

    public function test_activity_order_accepts_guest_details_without_guest_leader(): void
    {
        $user = $this->actingUser();

        $this->actingAs($user)
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                'guests' => [
                    self::guest('First Guest', '+628123456'),
                    self::guest('Second Guest'),
                ],
            ]))
            ->assertRedirect(route('view.detail-order-activity', ['id' => 1]));

        $order = DB::table('orders')->where('service', 'Activity')->first();

        $this->assertSame('First Guest', $order->pickup_name);
        $this->assertSame('+628123456', $order->pickup_phone);
        $this->assertStringContainsString('First Guest', (string) $order->guest_detail);
        $this->assertStringNotContainsString('<li>1. First Guest', (string) $order->guest_detail);
        $this->assertStringNotContainsString('Guest leader', (string) $order->guest_detail);
        $this->assertSame(2, DB::table('guests')->where('order_id', $order->id)->count());
    }

    public function test_activity_order_detail_removes_legacy_guest_manifest_manual_numbering(): void
    {
        $user = $this->actingUser();
        $order = $this->insertApprovedActivityOrder($user, [
            'id' => 88,
            'orderno' => 'ACT-LEGACY-GUESTS',
            'guest_detail' => '<p>1 guest(s) recorded in the activity guest manifest.</p><ol><li>1. Angga | Adult | Male | Phone: 125455686658</li></ol>',
        ]);

        $this->actingAs($user)
            ->get(route('view.detail-order-activity', ['id' => $order->id]))
            ->assertOk()
            ->assertSee('Angga | Adult | Male | Phone: 125455686658', false)
            ->assertDontSee('1. Angga', false);
    }

    public function test_activity_order_accepts_minimum_manual_guest_detail_up_to_ten_pax(): void
    {
        DB::table('activities')->where('id', 1)->update([
            'qty' => '10',
        ]);

        $this->actingAs($this->actingUser())
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                'number_of_guests' => 5,
                'guests' => [
                    self::guest('Primary Guest', '+628123456'),
                ],
            ]))
            ->assertRedirect(route('view.detail-order-activity', ['id' => 1]));

        $order = DB::table('orders')->where('service', 'Activity')->first();

        $this->assertSame(5, (int) $order->number_of_guests);
        $this->assertSame('Primary Guest', $order->pickup_name);
        $this->assertStringContainsString('1 guest(s) recorded', (string) $order->guest_detail);
        $this->assertSame(1, DB::table('guests')->where('order_id', $order->id)->count());
    }

    public function test_activity_order_rejects_more_manual_guest_details_than_selected_pax(): void
    {
        $this->actingAs($this->actingUser())
            ->from(route('view.activity-public-detail', ['code' => 'ACT-001']))
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                'number_of_guests' => 2,
                'guests' => [
                    self::guest('First Guest'),
                    self::guest('Second Guest'),
                    self::guest('Third Guest'),
                ],
            ]))
            ->assertSessionHasErrors('guests');

        $this->assertSame(0, DB::table('orders')->where('service', 'Activity')->count());
    }

    public function test_activity_order_requires_guest_list_upload_above_manual_threshold(): void
    {
        DB::table('activities')->where('id', 1)->update([
            'qty' => '20',
        ]);

        $this->actingAs($this->actingUser())
            ->from(route('view.activity-public-detail', ['code' => 'ACT-001']))
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                'number_of_guests' => ActivityGuestListService::MANUAL_THRESHOLD + 1,
                'guests' => [
                    self::guest('Manual Guest'),
                ],
            ]))
            ->assertSessionHasErrors('guest_list');

        $this->assertSame(0, DB::table('orders')->where('service', 'Activity')->count());
    }

    public function test_activity_order_accepts_csv_guest_list_upload_above_manual_threshold(): void
    {
        DB::table('activities')->where('id', 1)->update([
            'qty' => '20',
        ]);

        $this->actingAs($this->actingUser())
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                'number_of_guests' => 12,
                'guests' => [],
                'guest_list' => $this->csvGuestListUpload(12),
            ]))
            ->assertRedirect(route('view.detail-order-activity', ['id' => 1]));

        $order = DB::table('orders')->where('service', 'Activity')->first();

        $this->assertSame(12, (int) $order->number_of_guests);
        $this->assertSame('Guest 1', $order->pickup_name);
        $this->assertSame('+62 800 000 001', $order->pickup_phone);
        $this->assertStringContainsString('12 guest(s) recorded', (string) $order->guest_detail);
        $this->assertDatabaseHas('guests', [
            'order_id' => $order->id,
            'name' => 'Guest 1',
            'age' => 'Adult',
            'sex' => 'Male',
            'phone' => '+62 800 000 001',
        ]);
        $this->assertDatabaseHas('guests', [
            'order_id' => $order->id,
            'name' => 'Guest 2',
            'age' => 'Child',
            'sex' => 'Female',
            'phone' => null,
        ]);
        $this->assertSame(12, DB::table('guests')->where('order_id', $order->id)->count());
    }

    public function test_activity_order_accepts_xlsx_guest_list_upload_above_manual_threshold(): void
    {
        if (! class_exists(ZipArchive::class)) {
            $this->markTestSkipped('ZipArchive extension is required for XLSX guest list parsing.');
        }

        DB::table('activities')->where('id', 1)->update([
            'qty' => '20',
        ]);

        $this->actingAs($this->actingUser())
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                'number_of_guests' => 12,
                'guests' => [],
                'guest_list' => $this->xlsxGuestListUpload(12),
            ]))
            ->assertRedirect(route('view.detail-order-activity', ['id' => 1]));

        $order = DB::table('orders')->where('service', 'Activity')->first();

        $this->assertSame(12, (int) $order->number_of_guests);
        $this->assertSame(12, DB::table('guests')->where('order_id', $order->id)->count());
    }

    public function test_activity_guest_list_templates_from_system_are_parseable(): void
    {
        if (! class_exists(ZipArchive::class)) {
            $this->markTestSkipped('ZipArchive extension is required for XLSX guest list parsing.');
        }

        $guestLists = app(ActivityGuestListService::class);
        $csv = UploadedFile::fake()->createWithContent('activity-guest-list-template.csv', $guestLists->csvTemplateContent());
        $xlsxPath = tempnam(sys_get_temp_dir(), 'activity-guest-list-template-');
        file_put_contents($xlsxPath, $guestLists->xlsxTemplateContent());
        $xlsx = new UploadedFile(
            $xlsxPath,
            'activity-guest-list-template.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $this->assertCount(2, $guestLists->parseUpload($csv, 2));
        $this->assertCount(2, $guestLists->parseUpload($xlsx, 2));
    }

    public function test_activity_order_rejects_invalid_empty_malformed_and_mismatched_guest_list_uploads(): void
    {
        DB::table('activities')->where('id', 1)->update([
            'qty' => '20',
        ]);

        $cases = [
            'invalid-format' => UploadedFile::fake()->create('guest-list.pdf', 1, 'application/pdf'),
            'empty' => UploadedFile::fake()->createWithContent('guest-list.csv', "Guest Name,Age Category,Sex,Phone Number\n"),
            'malformed' => UploadedFile::fake()->createWithContent('guest-list.csv', "Name,Phone\nGuest 1,+6281\n"),
            'mismatch' => $this->csvGuestListUpload(10),
            'invalid-age' => $this->csvGuestListUpload(11, [
                7 => ['Guest 7', 'Teen', 'Male', '+62 800 000 007'],
            ]),
            'invalid-sex' => $this->csvGuestListUpload(11, [
                8 => ['Guest 8', 'Adult', 'Other', '+62 800 000 008'],
            ]),
        ];
        $user = $this->actingUser();

        foreach ($cases as $token => $file) {
            $this->actingAs($user)
                ->from(route('view.activity-public-detail', ['code' => 'ACT-001']))
                ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                    'submission_token' => 'activity-'.$token,
                    'number_of_guests' => 11,
                    'guests' => [],
                    'guest_list' => $file,
                ]))
                ->assertSessionHasErrors('guest_list');
        }

        $this->assertSame(0, DB::table('orders')->where('service', 'Activity')->count());
    }

    public function test_activity_guest_list_row_count_must_match_selected_guest_count(): void
    {
        DB::table('activities')->where('id', 1)->update([
            'qty' => '25',
        ]);
        $user = $this->actingUser();

        foreach ([19, 21] as $rows) {
            $this->actingAs($user)
                ->from(route('view.activity-public-detail', ['code' => 'ACT-001']))
                ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                    'submission_token' => 'activity-row-count-'.$rows,
                    'number_of_guests' => 20,
                    'guests' => [],
                    'guest_list' => $this->csvGuestListUpload($rows),
                ]))
                ->assertSessionHasErrors('guest_list');
        }

        $this->actingAs($user)
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                'submission_token' => 'activity-row-count-20',
                'number_of_guests' => 20,
                'guests' => [],
                'guest_list' => $this->csvGuestListUpload(20),
            ]))
            ->assertRedirect(route('view.detail-order-activity', ['id' => 1]));

        $this->assertSame(1, DB::table('orders')->where('service', 'Activity')->count());
        $this->assertSame(20, DB::table('guests')->count());
    }

    public function test_activity_capacity_rejects_uploaded_guest_list_above_capacity(): void
    {
        DB::table('activities')->where('id', 1)->update([
            'qty' => '20',
        ]);

        $this->actingAs($this->actingUser())
            ->from(route('view.activity-public-detail', ['code' => 'ACT-001']))
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                'number_of_guests' => 21,
                'guests' => [],
                'guest_list' => $this->csvGuestListUpload(21),
            ]))
            ->assertSessionHasErrors('number_of_guests');

        $this->assertSame(0, DB::table('orders')->where('service', 'Activity')->count());
    }

    public function test_expired_activity_is_hidden_and_automatically_moved_to_draft(): void
    {
        DB::table('activities')->where('id', 1)->update([
            'validity' => '2026-07-27',
        ]);
        DB::table('activities')->insert([
            'id' => 2,
            'partners_id' => 1,
            'name' => 'Expired Activity',
            'code' => 'ACT-EXPIRED',
            'type' => 'Water',
            'location' => 'Bali',
            'duration' => '2 Hours',
            'contract_rate' => 1000000,
            'markup' => 10,
            'qty' => '5',
            'min_pax' => '2',
            'status' => 'Active',
            'validity' => '2026-07-26',
        ]);

        $this->get(route('view.activities-service'))
            ->assertOk()
            ->assertSee('Test Activity')
            ->assertDontSee('Expired Activity');
        $this->get(route('view.activity-public-detail', ['code' => 'ACT-EXPIRED']))
            ->assertNotFound();

        $inventory = app(ActivityInventoryService::class)->indexData();
        $expiredRow = $inventory['activityIndex']->rows()
            ->first(fn (array $row) => $row['model']->code === 'ACT-EXPIRED');

        $this->assertSame('Draft', $expiredRow['model']->status);
        $this->assertTrue($expiredRow['price_available']);
        $this->assertSame('77.00', $expiredRow['published_rate']);
        $this->assertDatabaseHas('activities', [
            'id' => 2,
            'status' => 'Draft',
        ]);
        $this->assertDatabaseHas('activities', [
            'id' => 1,
            'status' => 'Active',
        ]);

        $this->artisan('activities:draft-expired')
            ->expectsOutput('Moved 0 expired Activity record(s) to Draft.')
            ->assertSuccessful();
    }

    public function test_activity_validity_boundary_today_is_not_auto_drafted(): void
    {
        DB::table('activities')->where('id', 1)->update([
            'status' => 'Active',
            'validity' => '2026-07-27',
        ]);

        app(ActivityInventoryService::class)->indexData();

        $this->assertDatabaseHas('activities', [
            'id' => 1,
            'status' => 'Active',
        ]);
    }

    public function test_expired_validity_auto_drafts_but_backend_price_remains_calculable(): void
    {
        DB::table('activities')->where('id', 1)->update([
            'status' => 'Active',
            'validity' => '2026-07-26',
        ]);

        $inventory = app(ActivityInventoryService::class)->indexData();
        $row = $inventory['activityIndex']->rows()->first();

        $this->assertSame('Draft', $row['model']->status);
        $this->assertTrue($row['price_available']);
        $this->assertSame('150.00', $row['published_rate']);
        $this->assertDatabaseHas('activities', [
            'id' => 1,
            'status' => 'Draft',
        ]);
    }

    public function test_backend_cannot_publish_an_expired_activity_as_active(): void
    {
        $request = UpdateActivityAdminRequest::create('/admin/activities/1', 'PUT', [
            'status' => 'Active',
            'validity' => '2026-07-26',
        ]);
        $validator = Validator::make($request->all(), $request->rules());
        $request->withValidator($validator);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('validity', $validator->errors()->toArray());
        $this->assertSame(
            'An expired Activity cannot be published as Active.',
            $validator->errors()->first('validity')
        );
    }

    public function test_backend_edit_activity_page_loads_with_usd_rate_context(): void
    {
        $response = $this->actingAs($this->actingUser(20, 'admin@example.test', 'developer', 'admin'))
            ->get(route('admin.activities.edit', ['id' => 1]))
            ->assertOk()
            ->assertSee('USD Rate:', false)
            ->assertSee('activity-edit-layout', false)
            ->assertSee('backend-detail-side-card activity-edit-context-panel', false)
            ->assertSee('Record Metadata', false)
            ->assertSee('Pricing Diagnostics', false)
            ->assertSee('Selling Price', false)
            ->assertSee('$150,00', false)
            ->assertSee('Rp2.250.000', false)
            ->assertDontSee('Partner Context', false)
            ->assertDontSee('Pricing Context', false)
            ->assertSee('activity-form-cover-preview', false)
            ->assertSee('data-activity-cover-input', false)
            ->assertSee('data-activity-cover-preview', false)
            ->assertSee('data-backend-translation-group', false)
            ->assertSee('Traditional Chinese', false)
            ->assertSee('Simplified Chinese', false)
            ->assertSee('Activity traditional description snapshot', false)
            ->assertSee('Activity simplified cancellation snapshot', false)
            ->assertSee('data-backend-picker="date"', false)
            ->assertSee('data-backend-money-unit="IDR"', false)
            ->assertSee('data-activity-pricing-preview', false)
            ->assertSee('data-activity-pricing-preview-usd', false)
            ->assertSee('data-activity-pricing-preview-idr', false)
            ->assertSee('data-activity-pricing-preview-message', false)
            ->assertSee('name="status"', false)
            ->assertDontSee('Description Traditional', false)
            ->assertDontSee('name="initial_state"', false)
            ->assertDontSee('name="page"', false);

        $response->assertSeeInOrder(['$150,00', 'Rp2.250.000'], false);
        $response->assertSeeInOrder(['Cover Image', 'Activity Profile', 'Operational Information'], false);
        $response->assertSeeInOrder(['Pricing Inputs', 'Contract Rate', 'Markup', 'Valid Until'], false);
        $response->assertSeeInOrder(['Current Status', 'Status', 'Record Metadata'], false);

        $this->assertDoesNotMatchRegularExpression(
            '/<textarea\b[^>]*name="description(_traditional|_simplified)?"[^>]*\srequired\b/',
            $response->getContent()
        );
    }

    public function test_backend_create_activity_page_uses_canonical_layout(): void
    {
        $response = $this->actingAs($this->actingUser(20, 'activity-create-page@example.test', 'developer', 'admin'))
            ->get(route('admin.activities.create'))
            ->assertOk()
            ->assertSee('activity-create-layout', false)
            ->assertSee('backend-detail-side-card activity-create-context-panel', false)
            ->assertSee('data-backend-translation-group', false)
            ->assertSee('Traditional Chinese', false)
            ->assertSee('Simplified Chinese', false)
            ->assertSee('data-backend-picker="date"', false)
            ->assertSee('data-backend-money-unit="IDR"', false)
            ->assertDontSee('Description Traditional', false)
            ->assertDontSee('name="status"', false)
            ->assertDontSee('name="author"', false);

        $response->assertSeeInOrder(['Cover Image', 'Activity Profile', 'Operational Information'], false);
        $response->assertSeeInOrder(['Pricing Inputs', 'Contract Rate', 'Markup', 'Valid Until'], false);
        $response->assertSeeInOrder(['Initial Status', 'Draft', 'Creation Guidance'], false);
    }

    public function test_backend_detail_activity_page_uses_canonical_read_only_layout(): void
    {
        DB::table('activities')->where('id', 1)->update([
            'map' => null,
            'additional_info_simplified' => null,
        ]);

        $response = $this->actingAs($this->actingUser(20, 'activity-detail-page@example.test', 'developer', 'admin'))
            ->get(route('admin.activities.show', ['id' => 1]))
            ->assertOk()
            ->assertSee('activity-detail-layout', false)
            ->assertSee('backend-detail-side-card activity-detail-context-panel', false)
            ->assertSee('Basic Information', false)
            ->assertSee('Operational Information', false)
            ->assertSee('Pricing Inputs', false)
            ->assertSee('Activity Profile', false)
            ->assertSee('Gallery Images', false)
            ->assertSee('Content and Translations', false)
            ->assertSee('Partner A', false)
            ->assertSee('2026-12-31', false)
            ->assertSee('Minimum Pax', false)
            ->assertSee('Capacity', false)
            ->assertSee('Activity description snapshot', false)
            ->assertSee('Activity traditional description snapshot', false)
            ->assertSee('Activity simplified cancellation snapshot', false)
            ->assertSee('Selling Price', false)
            ->assertSee('$150,00', false)
            ->assertSee('Rp2.250.000', false)
            ->assertSee('Rp1.500.000', false)
            ->assertSee('Rp750.000', false)
            ->assertSee('No content.', false)
            ->assertSee('Activity ID', false)
            ->assertSee('Activity Code', false)
            ->assertSee('Media Maintenance', false)
            ->assertSee('Edit Activity', false)
            ->assertSee('Add / Edit Gallery', false)
            ->assertDontSee('Preview Limit', false)
            ->assertDontSee('Pricing Context', false)
            ->assertDontSee('Description Traditional', false)
            ->assertDontSee('data-backend-richtext', false)
            ->assertDontSee('data-backend-picker', false)
            ->assertDontSee('activity-gallery__grid', false);

        $response->assertSeeInOrder(['$100,00', 'Rp1.500.000'], false);
        $response->assertSeeInOrder(['$50,00', 'Rp750.000'], false);
        $response->assertSeeInOrder(['$150,00', 'Rp2.250.000'], false);
    }

    public function test_backend_activity_administrator_can_access_detail_edit_and_gallery_actions(): void
    {
        $admin = $this->actingUser(20, 'activity-administrator@example.test', 'administrator', 'admin');

        $this->actingAs($admin)
            ->get(route('admin.activities.show', ['id' => 1]))
            ->assertOk()
            ->assertSee(route('admin.activities.edit', ['id' => 1]), false)
            ->assertSee(route('admin.activities.gallery.edit', ['id' => 1]), false)
            ->assertSee('Edit Activity', false)
            ->assertSee('Add / Edit Gallery', false);

        $this->actingAs($admin)
            ->get(route('admin.activities.edit', ['id' => 1]))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.activities.gallery.edit', ['id' => 1]))
            ->assertOk()
            ->assertSee('activity-gallery-layout', false)
            ->assertSee('Current Images', false)
            ->assertSee('Add Gallery Images', false)
            ->assertSee('data-activity-gallery-preview-target="#activityGalleryPreview"', false)
            ->assertSee('Edit Activity', false);
    }

    public function test_backend_detail_activity_contract_rate_idr_uses_database_value(): void
    {
        DB::table('activities')->where('id', 1)->update([
            'contract_rate' => 1500075,
        ]);

        $this->actingAs($this->actingUser(20, 'activity-contract-rate@example.test', 'developer', 'admin'))
            ->get(route('admin.activities.show', ['id' => 1]))
            ->assertOk()
            ->assertSeeInOrder(['$100,01', 'Rp1.500.075'], false)
            ->assertDontSee('Rp1.500.150', false);
    }

    public function test_backend_activity_gallery_upload_stores_images_and_returns_to_detail(): void
    {
        Storage::fake('public');
        $admin = $this->actingUser(20, 'activity-gallery-upload@example.test', 'administrator', 'admin');

        $this->actingAs($admin)
            ->put(route('admin.gallery-activities.update', ['activity' => 1]), [
                'images' => [
                    UploadedFile::fake()->image('gallery-one.jpg'),
                    UploadedFile::fake()->image('gallery-two.webp'),
                ],
            ])
            ->assertRedirect(route('admin.activities.show', ['id' => 1]))
            ->assertSessionHas('success', '2 gallery image(s) uploaded successfully.');

        $images = DB::table('activities_images')
            ->where('activities_id', 1)
            ->orderByDesc('id')
            ->limit(2)
            ->get();

        $this->assertCount(2, $images);

        foreach ($images as $image) {
            $this->assertStringStartsWith('activities/activities-images/', $image->image);
            Storage::disk('public')->assertExists($image->image);
        }
    }

    public function test_backend_activity_gallery_image_delete_does_not_require_upload_file(): void
    {
        Storage::fake('public');
        $admin = $this->actingUser(20, 'activity-gallery-delete@example.test', 'administrator', 'admin');
        $path = 'activities/activities-images/delete-me.jpg';

        Storage::disk('public')->put($path, 'fake image content');
        $imageId = DB::table('activities_images')->insertGetId([
            'activities_id' => 1,
            'image' => $path,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.activities.images.destroy', ['id' => $imageId]))
            ->assertRedirect()
            ->assertSessionHas('success', 'The Activity gallery image has been successfully deleted!');

        $this->assertDatabaseMissing('activities_images', [
            'id' => $imageId,
        ]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_backend_activity_update_preserves_cover_and_uses_authenticated_author(): void
    {
        $admin = $this->actingUser(20, 'admin@example.test', 'developer', 'admin');

        $this->actingAs($admin)
            ->put(route('admin.activities.update', ['id' => 1]), $this->activityAdminPayload([
                'name' => 'Updated Activity',
                'author' => 999,
            ]))
            ->assertRedirect(route('admin.activities.show', ['id' => 1]));

        $this->assertDatabaseHas('activities', [
            'id' => 1,
            'name' => 'Updated Activity',
            'cover' => 'activity-cover.jpg',
            'author_id' => $admin->id,
            'contract_rate' => 1750000,
            'markup' => 75,
        ]);
        $this->assertDatabaseHas('partners', [
            'id' => 1,
            'status' => 'Active',
        ]);
    }

    public function test_backend_activity_update_replaces_cover_when_new_image_uploaded(): void
    {
        Storage::fake('public');
        $admin = $this->actingUser(20, 'activity-cover-update@example.test', 'developer', 'admin');

        $this->actingAs($admin)
            ->put(route('admin.activities.update', ['id' => 1]), $this->activityAdminPayload([
                'cover' => UploadedFile::fake()->image('replacement-cover.webp'),
            ]))
            ->assertRedirect(route('admin.activities.show', ['id' => 1]));

        $activity = DB::table('activities')->where('id', 1)->first();

        $this->assertNotSame('activity-cover.jpg', $activity->cover);
        Storage::disk('public')->assertExists('activities/activities-cover/'.$activity->cover);
    }

    public function test_backend_activity_store_uses_authenticated_author_and_canonical_cover_storage(): void
    {
        Storage::fake('public');
        $admin = $this->actingUser(20, 'activity-create@example.test', 'developer', 'admin');

        $this->actingAs($admin)
            ->post(route('admin.activities.store'), $this->activityAdminPayload([
                'name' => 'Created Activity',
                'author' => 999,
                'cover' => UploadedFile::fake()->image('created-activity.webp'),
            ]))
            ->assertRedirect();

        $activity = DB::table('activities')->where('name', 'Created Activity')->first();

        $this->assertNotNull($activity);
        $this->assertSame((string) $admin->id, (string) $activity->author_id);
        $this->assertSame('Draft', $activity->status);
        $this->assertSame('Updated traditional description', $activity->description_traditional);
        $this->assertSame('Updated simplified cancellation policy', $activity->cancellation_policy_simplified);
        $this->assertDatabaseHas('user_logs', [
            'action' => 'Add Activity',
            'user_id' => $admin->id,
            'subservice_id' => $activity->id,
        ]);
        Storage::disk('public')->assertExists('activities/activities-cover/'.$activity->cover);
    }

    public function test_backend_activity_rejects_minimum_pax_greater_than_capacity(): void
    {
        $admin = $this->actingUser(20, 'activity-capacity@example.test', 'developer', 'admin');

        $this->actingAs($admin)
            ->from(route('admin.activities.edit', ['id' => 1]))
            ->put(route('admin.activities.update', ['id' => 1]), $this->activityAdminPayload([
                'min_pax' => 10,
                'qty' => 5,
            ]))
            ->assertRedirect(route('admin.activities.edit', ['id' => 1]))
            ->assertSessionHasErrors('qty');

        $this->assertDatabaseHas('activities', [
            'id' => 1,
            'min_pax' => '2',
            'qty' => '5',
        ]);
    }

    public function test_backend_activity_update_allows_empty_customer_facing_copy_fields(): void
    {
        $admin = $this->actingUser(20, 'admin@example.test', 'developer', 'admin');
        $copyFields = [
            'description',
            'description_traditional',
            'description_simplified',
            'itinerary',
            'itinerary_traditional',
            'itinerary_simplified',
            'include',
            'include_traditional',
            'include_simplified',
            'additional_info',
            'additional_info_traditional',
            'additional_info_simplified',
            'cancellation_policy',
            'cancellation_policy_traditional',
            'cancellation_policy_simplified',
        ];

        $this->actingAs($admin)
            ->from(route('admin.activities.edit', ['id' => 1]))
            ->put(route('admin.activities.update', ['id' => 1]), $this->activityAdminPayload(array_fill_keys($copyFields, '')))
            ->assertRedirect(route('admin.activities.show', ['id' => 1]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('activities', [
            'id' => 1,
            'description' => null,
            'description_traditional' => null,
            'description_simplified' => null,
            'itinerary' => null,
            'include' => null,
            'additional_info' => null,
            'cancellation_policy' => null,
        ]);
    }

    public function test_backend_activity_update_rejects_invalid_cover_file(): void
    {
        $admin = $this->actingUser(20, 'admin@example.test', 'developer', 'admin');

        $this->actingAs($admin)
            ->from(route('admin.activities.edit', ['id' => 1]))
            ->put(route('admin.activities.update', ['id' => 1]), $this->activityAdminPayload([
                'cover' => UploadedFile::fake()->create('not-image.txt', 1, 'text/plain'),
            ]))
            ->assertRedirect(route('admin.activities.edit', ['id' => 1]))
            ->assertSessionHasErrors('cover');

        $this->assertDatabaseHas('activities', [
            'id' => 1,
            'cover' => 'activity-cover.jpg',
        ]);
    }

    public function test_backend_activity_update_requires_operations_author_position(): void
    {
        $staffAdmin = $this->actingUser(20, 'staff-admin@example.test', 'staff', 'admin');

        $this->actingAs($staffAdmin)
            ->put(route('admin.activities.update', ['id' => 1]), $this->activityAdminPayload([
                'name' => 'Unauthorized Update',
            ]))
            ->assertRedirect('/dashboard');

        $this->assertDatabaseMissing('activities', [
            'id' => 1,
            'name' => 'Unauthorized Update',
        ]);
    }

    public function test_backend_and_frontend_price_per_pax_use_the_same_activity_quote(): void
    {
        $user = $this->actingUser();
        $quoteResponse = $this->actingAs($user)
            ->postJson(route('activity.quote', ['code' => 'ACT-001']), [
                'number_of_guests' => 2,
                'travel_date' => '2026-07-28 09:00:00',
            ])
            ->assertOk()
            ->assertJsonPath('display.unit_price_usd', '150.00');

        $inventory = app(ActivityInventoryService::class);
        $indexData = $inventory->indexData();
        $detailData = $inventory->detailData(1);
        $backendRow = $indexData['activityIndex']->rows()->first();
        $frontendUnitPrice = $quoteResponse->json('display.unit_price_usd');

        $this->assertTrue($backendRow['price_available']);
        $this->assertSame($frontendUnitPrice, $backendRow['published_rate']);
        $this->assertSame(2250000, $backendRow['published_rate_idr']);
        $this->assertTrue($detailData['activityDetail']->priceAvailable());
        $this->assertSame($frontendUnitPrice, $detailData['activityDetail']->publishedRate());
        $this->assertSame(2250000, $detailData['activityDetail']->sellingPriceIdr());
        $this->assertSame(1500000, $detailData['activityDetail']->contractRateIdr());
        $this->assertSame(750000, $detailData['activityDetail']->markupIdr());
        $this->assertSame('0', $detailData['activityDetail']->taxPercentage());

        $this->actingAs($user)
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                'travel_date' => '2026-07-28 09:00:00',
                'price_total' => 1,
                'final_price' => 1,
            ]))
            ->assertRedirect();

        $order = DB::table('orders')->where('service', 'Activity')->first();

        $this->assertSame($frontendUnitPrice, number_format((float) $order->price_pax, 2, '.', ''));
    }

    public function test_activity_backend_reference_price_is_independent_from_status(): void
    {
        $pricing = app(ActivityPricingService::class);
        $activity = Activities::query()->findOrFail(1);

        $activeQuote = $pricing->quote($activity->forceFill(['status' => 'Active']), 2);
        $draftQuote = $pricing->quote($activity->forceFill(['status' => 'Draft']), 2);

        $this->assertSame($activeQuote->unitPriceUsdMinor, $draftQuote->unitPriceUsdMinor);
        $this->assertSame('150.00', $draftQuote->unitPriceUsd());

        DB::table('activities')->where('id', 1)->update(['status' => 'Draft']);

        $inventory = app(ActivityInventoryService::class)->indexData();
        $row = $inventory['activityIndex']->rows()->first();

        $this->assertSame('Draft', $row['model']->status);
        $this->assertTrue($row['price_available']);
        $this->assertSame('150.00', $row['published_rate']);
    }

    public function test_activity_backend_price_reports_missing_dependency_reasons(): void
    {
        $pricing = app(ActivityPricingService::class);
        $activity = Activities::query()->findOrFail(1);

        foreach ([
            'contract_rate' => 'MISSING_CONTRACT_RATE',
            'markup' => 'MISSING_MARKUP',
            'validity' => 'MISSING_VALID_UNTIL',
        ] as $field => $expectedCode) {
            try {
                $pricing->quote($activity->replicate()->forceFill([$field => null]), 2);
                $this->fail("Expected {$expectedCode} for missing {$field}.");
            } catch (PricingException $exception) {
                $this->assertSame($expectedCode, $exception->pricingCode);
            }
        }

        $zeroMarkupQuote = $pricing->quote($activity->replicate()->forceFill(['markup' => 0]), 2);

        $this->assertSame('100.00', $zeroMarkupQuote->unitPriceUsd());

        DB::table('taxes')->delete();

        try {
            app(ActivityPricingService::class)->quote($activity, 2);
            $this->fail('Expected MISSING_TAX when Activity tax is not configured.');
        } catch (PricingException $exception) {
            $this->assertSame('MISSING_TAX', $exception->pricingCode);
        }

        DB::table('taxes')->insert(['id' => 1, 'name' => 'tax', 'tax' => 0]);
        DB::table('usd_rates')->where('name', 'USD')->delete();

        try {
            app(ActivityPricingService::class)->quote($activity, 2);
            $this->fail('Expected MISSING_USD_RATE when stored USD rate is not configured.');
        } catch (PricingException $exception) {
            $this->assertSame('MISSING_USD_RATE', $exception->pricingCode);
        }
    }

    public function test_legacy_activity_detail_url_redirects_to_the_canonical_pricing_page(): void
    {
        $this->actingAs($this->actingUser())
            ->get(route('view.activity-detail', ['code' => 'ACT-001']))
            ->assertRedirect(route('view.activity-public-detail', ['code' => 'ACT-001']));
    }

    public function test_activity_pricing_uses_latest_stored_usd_sell_rate_even_when_timestamp_is_stale(): void
    {
        DB::table('usd_rates')->where('name', 'USD')->update([
            'updated_at' => now()->subHours(25),
        ]);
        $user = $this->actingUser();

        $this->actingAs($user)
            ->postJson(route('activity.quote', ['code' => 'ACT-001']), [
                'number_of_guests' => 2,
                'travel_date' => '2026-08-10 09:00:00',
            ])
            ->assertOk()
            ->assertJsonPath('price_available', true)
            ->assertJsonPath('display.unit_price_usd', '150.00');

        $this->actingAs($user)
            ->from(route('view.activity-public-detail', ['code' => 'ACT-001']))
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload())
            ->assertRedirect();

        $this->assertSame(1, DB::table('orders')->where('service', 'Activity')->count());
    }

    public function test_public_activity_listing_and_detail_only_use_active_records(): void
    {
        DB::table('activities')->insert([
            'id' => 2,
            'partners_id' => 1,
            'name' => 'Inactive Activity',
            'code' => 'ACT-INACTIVE',
            'type' => 'Water',
            'location' => 'Bali',
            'duration' => '2 Hours',
            'contract_rate' => 1000000,
            'markup' => 10,
            'qty' => '5',
            'min_pax' => '2',
            'status' => 'Inactive',
            'validity' => '2026-12-31',
        ]);

        $this->get(route('view.activities-service'))
            ->assertOk()
            ->assertSee('Test Activity')
            ->assertDontSee('Inactive Activity');

        $this->get(route('view.activity-public-detail', ['code' => 'ACT-INACTIVE']))
            ->assertNotFound();
    }

    public function test_activity_order_validates_minimum_and_capacity_without_date_inventory(): void
    {
        $this->actingAs($this->actingUser())
            ->from('/activity/ACT-001')
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                'number_of_guests' => 1,
                'guests' => [self::guest('Leader Guest', '+628123456', true)],
            ]))
            ->assertSessionHasErrors('number_of_guests');

        $this->actingAs($this->actingUser(11, 'second@example.test'))
            ->from('/activity/ACT-001')
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $this->activityPayload([
                'submission_token' => 'activity-token-2',
                'number_of_guests' => 6,
                'guests' => [
                    self::guest('Leader Guest', '+628123456', true),
                    self::guest('Guest 2'),
                    self::guest('Guest 3'),
                    self::guest('Guest 4'),
                    self::guest('Guest 5'),
                    self::guest('Guest 6'),
                ],
            ]))
            ->assertSessionHasErrors('number_of_guests');

        $this->assertSame(0, DB::table('orders')->count());
    }

    public function test_activity_duplicate_submission_token_returns_existing_order(): void
    {
        $user = $this->actingUser();
        $payload = $this->activityPayload(['submission_token' => 'same-activity-token']);

        $this->actingAs($user)->post(route('view.activity-order.store', ['code' => 'ACT-001']), $payload);
        $this->actingAs($user)
            ->post(route('view.activity-order.store', ['code' => 'ACT-001']), $payload)
            ->assertRedirect(route('view.detail-order-activity', ['id' => 1]));

        $this->assertSame(1, DB::table('orders')->where('service', 'Activity')->count());
        $this->assertSame(1, DB::table('reservations')->count());
    }

    public function test_activity_payment_upload_stores_private_receipt_and_guarded_route_enforces_owner(): void
    {
        $owner = $this->actingUser(10);
        $nonOwner = $this->actingUser(11, 'other@example.test');
        $order = $this->insertApprovedActivityOrder($owner);

        $invoice = InvoiceAdmin::create([
            'rsv_id' => $order->rsv_id,
            'inv_no' => 'ACT-INV-001',
            'due_date' => now()->addDay(),
            'balance' => 300,
            'bank_id' => 1,
            'currency_id' => 1,
            'rate_usd' => 15000,
            'sell_usd' => 15000,
            'rate_cny' => 2000,
            'sell_cny' => 2000,
            'rate_twd' => 500,
            'sell_twd' => 500,
        ]);

        $this->actingAs($owner)
            ->post(route('upload.payment-confirmation', ['id' => $order->id]), [
                'receipt_name' => UploadedFile::fake()->image('receipt.jpg'),
            ])
            ->assertRedirect(route('view.detail-order-activity', ['id' => $order->id]));

        $payment = PaymentConfirmation::first();

        $this->assertSame(1, (int) $payment->kurs_id);
        $this->assertStringStartsWith('activity/payments/'.$order->id.'/', $payment->receipt_img);
        Storage::disk('private')->assertExists($payment->receipt_img);

        $this->actingAs($nonOwner)
            ->get(route('orders.activity.payments.receipt', ['order' => $order->id, 'payment' => $payment->id]))
            ->assertNotFound();

        $response = $this->actingAs($owner)
            ->get(route('orders.activity.payments.receipt', ['order' => $order->id, 'payment' => $payment->id]))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));

        Storage::disk('private')->put(
            app(AccommodationFinancialFileService::class)->privateInvoicePath($order, $invoice, 'en'),
            "%PDF-1.4\n% activity invoice\n"
        );

        $this->actingAs($owner)
            ->get(route('view.detail-order-activity', ['id' => $order->id]))
            ->assertOk()
            ->assertSee(route('orders.activity.invoice.preview', ['order' => $order->id, 'locale' => 'en']), false)
            ->assertSee(route('orders.activity.invoice.download', ['order' => $order->id, 'locale' => 'en']), false);

        $invoiceResponse = $this->actingAs($owner)
            ->get(route('orders.activity.invoice.preview', ['order' => $order->id, 'locale' => 'en']))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->assertStringContainsString('private', $invoiceResponse->headers->get('Cache-Control'));

        $this->actingAs($nonOwner)
            ->get(route('orders.activity.invoice.preview', ['order' => $order->id, 'locale' => 'en']))
            ->assertNotFound();
    }

    public function test_activity_payment_legacy_upload_route_remains_service_aware(): void
    {
        $owner = $this->actingUser(12, 'activity-legacy@example.test');
        $order = $this->insertApprovedActivityOrder($owner, ['orderno' => 'ACT-LEGACY']);

        InvoiceAdmin::create([
            'rsv_id' => $order->rsv_id,
            'inv_no' => 'ACT-INV-LEGACY',
            'due_date' => now()->addDay(),
            'balance' => 300,
            'bank_id' => 1,
            'currency_id' => 1,
            'rate_usd' => 15000,
            'sell_usd' => 15000,
            'rate_cny' => 2000,
            'sell_cny' => 2000,
            'rate_twd' => 500,
            'sell_twd' => 500,
        ]);

        $this->actingAs($owner)
            ->post(route('upload.payment-confirmation.legacy', ['id' => $order->id]), [
                'receipt_name' => UploadedFile::fake()->image('legacy-receipt.jpg'),
            ])
            ->assertRedirect(route('view.detail-order-activity', ['id' => $order->id]));

        $payment = PaymentConfirmation::first();

        $this->assertNotNull($payment);
        $this->assertStringStartsWith('activity/payments/'.$order->id.'/', $payment->receipt_img);
        Storage::disk('private')->assertExists($payment->receipt_img);
    }

    public function test_customer_cannot_approve_activity_order_through_admin_route(): void
    {
        $user = $this->actingUser();
        $order = $this->insertApprovedActivityOrder($user, ['status' => 'Pending']);

        $this->actingAs($user)
            ->put(url('/factivate-order/'.$order->id), [
                'due_date' => '2026-08-01',
                'currency' => 1,
                'bank_id' => 1,
            ])
            ->assertRedirect('/dashboard');

        $this->assertSame('Pending', DB::table('orders')->where('id', $order->id)->value('status'));
    }

    public function test_admin_confirm_activity_order_returns_to_admin_order_detail(): void
    {
        Mail::fake();
        $owner = $this->actingUser();
        $admin = $this->actingUser(20, 'activity-admin@example.test', 'developer', 'admin');
        $order = $this->insertApprovedActivityOrder($owner, [
            'status' => 'Pending',
            'reservation_status' => 'Pending',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.orders.workflow.activate', ['id' => $order->id]), [
                'bank' => 1,
                'currency' => 1,
            ])
            ->assertRedirect(route('admin.order.show', ['id' => $order->id]));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Approved',
            'verified_by' => $admin->id,
        ]);
        $this->assertDatabaseHas('reservations', [
            'id' => $order->rsv_id,
            'status' => 'Active',
        ]);
        $this->assertDatabaseHas('invoice_admins', [
            'rsv_id' => $order->rsv_id,
            'total_usd' => 300,
            'bank_id' => 1,
            'currency_id' => 1,
        ]);
    }

    public function test_admin_archive_activity_order_uses_deleted_status_and_cancels_reservation(): void
    {
        $owner = $this->actingUser();
        $admin = $this->actingUser(20, 'activity-admin@example.test', 'developer', 'admin');
        $order = $this->insertApprovedActivityOrder($owner, [
            'status' => 'Invalid',
            'reservation_status' => 'Canceled',
        ]);

        $this->actingAs($admin)
            ->put('/farchive-order/'.$order->id, [
                'msg' => '<script>bad()</script>Move to archive',
            ])
            ->assertRedirect('/orders-admin#invalidorders');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Deleted',
            'msg' => 'bad()Move to archive',
        ]);
        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
            'status' => 'Archive',
        ]);
        $this->assertDatabaseHas('reservations', [
            'id' => $order->rsv_id,
            'status' => 'Canceled',
        ]);
    }

    public function test_activity_completion_uses_completed_at_and_history_scope(): void
    {
        $owner = $this->actingUser();
        $order = $this->insertApprovedActivityOrder($owner, [
            'status' => 'Paid',
            'checkin' => '2026-07-20 09:00:00',
            'checkout' => '2026-07-20 11:00:00',
        ]);

        $this->assertSame(1, app(ActivityOrderLifecycleService::class)->completeEligiblePaidOrders(null, now()));
        $this->assertNotNull(DB::table('orders')->where('id', $order->id)->value('completed_at'));
        $this->assertSame('Completed', DB::table('reservations')->where('id', $order->rsv_id)->value('status'));
        $this->assertSame(0, app(ActivityOrderLifecycleService::class)->completeEligiblePaidOrders(null, now()));

        $this->assertSame(0, app(ActivityOrderLifecycleService::class)->applyActivityCurrentScope(Orders::query(), now())->count());
        $this->assertSame(1, app(ActivityOrderLifecycleService::class)->applyActivityHistoryScope(Orders::query(), now())->count());
    }

    private function activityPayload(array $overrides = []): array
    {
        return array_merge([
            'submission_token' => 'activity-token-1',
            'number_of_guests' => 2,
            'travel_date' => '2026-08-10 09:00:00',
            'pickup_location' => 'Hotel Nikko Bali Benoa',
            'dropoff_location' => 'Ubud Center',
            'terms_accepted' => '1',
            'guests' => [
                self::guest('First Guest', '+628123456'),
                self::guest('Second Guest'),
            ],
        ], $overrides);
    }

    private function activityAdminPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Admin Updated Activity',
            'location' => 'Ubud',
            'map' => 'https://maps.example.test/activity',
            'type' => 'Water',
            'duration' => '2 Hours',
            'description' => 'Updated description',
            'description_traditional' => 'Updated traditional description',
            'description_simplified' => 'Updated simplified description',
            'itinerary' => 'Updated itinerary',
            'itinerary_traditional' => 'Updated traditional itinerary',
            'itinerary_simplified' => 'Updated simplified itinerary',
            'include' => 'Updated include',
            'include_traditional' => 'Updated traditional include',
            'include_simplified' => 'Updated simplified include',
            'additional_info' => 'Updated additional info',
            'additional_info_traditional' => 'Updated traditional additional info',
            'additional_info_simplified' => 'Updated simplified additional info',
            'cancellation_policy' => 'Updated cancellation policy',
            'cancellation_policy_traditional' => 'Updated traditional cancellation policy',
            'cancellation_policy_simplified' => 'Updated simplified cancellation policy',
            'contract_rate' => 1750000,
            'markup' => 75,
            'qty' => 8,
            'min_pax' => 2,
            'validity' => '2026-12-31',
            'partners_id' => 1,
            'status' => 'Active',
        ], $overrides);
    }

    private static function guest(string $name, string $phone = '+628777', bool $leader = false): array
    {
        $guest = [
            'name' => $name,
            'phone' => $phone,
            'age' => 'Adult',
            'sex' => 'Male',
            'identification_type' => 'Passport',
            'identification_no' => 'ID-'.$name,
        ];

        if ($leader) {
            $guest['is_leader'] = '1';
        }

        return $guest;
    }

    private function csvGuestListUpload(int $count, array $overrides = []): UploadedFile
    {
        $content = "Guest Name,Age Category,Sex,Phone Number\n";

        for ($index = 1; $index <= $count; $index++) {
            $row = $overrides[$index] ?? [
                'Guest '.$index,
                $index % 2 === 0 ? 'child' : 'ADULT',
                $index % 2 === 0 ? 'female' : 'MALE',
                $index % 2 === 0 ? '' : sprintf('+62 800 000 %03d', $index),
            ];

            $handle = fopen('php://temp', 'r+');
            fputcsv($handle, $row);
            rewind($handle);
            $content .= stream_get_contents($handle);
            fclose($handle);
        }

        return UploadedFile::fake()->createWithContent('guest-list.csv', $content);
    }

    private function xlsxGuestListUpload(int $count): UploadedFile
    {
        $tmp = tempnam(sys_get_temp_dir(), 'activity-guest-list-test-');
        $zip = new ZipArchive();
        $zip->open($tmp, ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Guest List" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->testGuestSheetXml($count));
        $zip->close();

        return new UploadedFile(
            $tmp,
            'guest-list.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }

    private function testGuestSheetXml(int $count): string
    {
        $rows = [
            ['Guest Name', 'Age Category', 'Sex', 'Phone Number'],
        ];

        for ($index = 1; $index <= $count; $index++) {
            $rows[] = [
                'Guest '.$index,
                $index % 2 === 0 ? 'child' : 'ADULT',
                $index % 2 === 0 ? 'female' : 'MALE',
                $index % 2 === 0 ? '' : sprintf('+62 800 000 %03d', $index),
            ];
        }

        $rowXml = collect($rows)
            ->map(function (array $row, int $rowIndex) {
                $cells = collect($row)
                    ->map(function (string $value, int $columnIndex) use ($rowIndex) {
                        $cell = chr(65 + $columnIndex).($rowIndex + 1);

                        return '<c r="'.$cell.'" t="inlineStr"><is><t>'.htmlspecialchars($value, ENT_XML1).'</t></is></c>';
                    })
                    ->implode('');

                return '<row r="'.($rowIndex + 1).'">'.$cells.'</row>';
            })
            ->implode('');

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'.$rowXml.'</sheetData></worksheet>';
    }

    private function actingUser(int $id = 10, string $email = 'activity@example.test', string $position = 'agent', string $type = 'user'): User
    {
        return User::create([
            'id' => $id,
            'name' => 'Activity Agent '.$id,
            'email' => $email,
            'password' => 'secret',
            'type' => $type,
            'position' => $position,
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
            'code' => 'ACT',
        ]);
    }

    private function insertApprovedActivityOrder(User $owner, array $overrides = []): Orders
    {
        $orderOverrides = $overrides;
        unset($orderOverrides['reservation_status']);

        $reservation = Reservation::create([
            'rsv_no' => $overrides['orderno'] ?? 'ACT-APPROVED',
            'service' => 'Activity',
            'agn_id' => $owner->id,
            'adm_id' => $owner->id,
            'checkin' => $overrides['checkin'] ?? '2026-08-10 09:00:00',
            'checkout' => $overrides['checkout'] ?? '2026-08-10 11:00:00',
            'status' => $overrides['reservation_status'] ?? 'Active',
        ]);

        return Orders::create(array_merge([
            'user_id' => $owner->id,
            'sales_agent' => $owner->id,
            'name' => $owner->name,
            'email' => $owner->email,
            'orderno' => 'ACT-APPROVED',
            'service' => 'Activity',
            'service_type' => 'Water',
            'service_id' => 1,
            'servicename' => 'Partner A',
            'subservice' => 'Test Activity',
            'subservice_id' => 1,
            'checkin' => '2026-08-10 09:00:00',
            'checkout' => '2026-08-10 11:00:00',
            'travel_date' => '2026-08-10 09:00:00',
            'duration' => '2 Hours',
            'number_of_guests' => 2,
            'capacity' => 5,
            'price_pax' => 150,
            'normal_price' => 300,
            'price_total' => 300,
            'final_price' => 300,
            'status' => 'Approved',
            'rsv_id' => $reservation->id,
        ], $orderOverrides));
    }

    private function prepareSchema(): void
    {
        foreach ([
            'payment_confirmations',
            'invoice_admins',
            'reservations',
            'guests',
            'user_logs',
            'order_logs',
            'orders',
            'activities_images',
            'activities',
            'activity_types',
            'partners',
            'footer_links',
            'footer_settings',
            'promotions',
            'business_profiles',
            'bank_accounts',
            'usd_rates',
            'taxes',
            'users',
            'airport_shuttles',
            'drivers',
            'extra_beds',
            'guides',
            'hotels',
            'optional_rate_orders',
            'optional_rates',
            'villas',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('type')->nullable();
            $table->string('position')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('code')->nullable();
            $table->timestamps();
        });

        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('tax', 8, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('usd_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('rate', 14, 2)->default(1);
            $table->decimal('sell', 14, 2)->default(1);
            $table->timestamps();
        });

        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank')->nullable();
            $table->string('currency')->nullable();
            $table->timestamps();
        });

        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('profile_key')->nullable();
            $table->string('name')->nullable();
            $table->string('nickname')->nullable();
            $table->string('caption')->nullable();
            $table->timestamps();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('discounts', 14, 2)->default(0);
            $table->date('periode_start')->nullable();
            $table->date('periode_end')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('footer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable();
            $table->text('value')->nullable();
            $table->text('value_traditional')->nullable();
            $table->text('value_simplified')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('footer_links', function (Blueprint $table) {
            $table->id();
            $table->string('group')->nullable();
            $table->string('label')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_types', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->timestamps();
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partners_id')->nullable();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('type')->nullable();
            $table->string('location')->nullable();
            $table->string('map')->nullable();
            $table->text('description')->nullable();
            $table->text('description_traditional')->nullable();
            $table->text('description_simplified')->nullable();
            $table->text('itinerary')->nullable();
            $table->text('itinerary_traditional')->nullable();
            $table->text('itinerary_simplified')->nullable();
            $table->string('duration')->nullable();
            $table->text('include')->nullable();
            $table->text('include_traditional')->nullable();
            $table->text('include_simplified')->nullable();
            $table->text('additional_info')->nullable();
            $table->text('additional_info_traditional')->nullable();
            $table->text('additional_info_simplified')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->text('cancellation_policy_traditional')->nullable();
            $table->text('cancellation_policy_simplified')->nullable();
            $table->integer('contract_rate')->default(0);
            $table->integer('markup')->default(0);
            $table->string('qty')->nullable();
            $table->string('min_pax')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->date('validity')->nullable();
            $table->text('cover')->nullable();
            $table->timestamps();
        });

        Schema::create('activities_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activities_id')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('orderno')->nullable();
            $table->string('confirmation_order')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('service')->nullable();
            $table->string('service_type')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->string('servicename')->nullable();
            $table->string('subservice')->nullable();
            $table->unsignedBigInteger('subservice_id')->nullable();
            $table->dateTime('checkin')->nullable();
            $table->dateTime('checkout')->nullable();
            $table->dateTime('travel_date')->nullable();
            $table->dateTime('pickup_date')->nullable();
            $table->dateTime('dropoff_date')->nullable();
            $table->string('pickup_name')->nullable();
            $table->string('pickup_phone')->nullable();
            $table->text('pickup_location')->nullable();
            $table->text('dropoff_location')->nullable();
            $table->string('location')->nullable();
            $table->integer('capacity')->nullable();
            $table->integer('number_of_guests')->nullable();
            $table->text('guest_detail')->nullable();
            $table->text('note')->nullable();
            $table->text('include')->nullable();
            $table->text('additional_info')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->text('itinerary')->nullable();
            $table->string('duration')->nullable();
            $table->decimal('price_pax', 14, 2)->default(0);
            $table->decimal('normal_price', 14, 2)->default(0);
            $table->decimal('price_total', 14, 2)->default(0);
            $table->decimal('final_price', 14, 2)->default(0);
            $table->text('promotion')->nullable();
            $table->text('promotion_disc')->nullable();
            $table->decimal('usd_rate', 14, 2)->nullable();
            $table->decimal('cny_rate', 14, 2)->nullable();
            $table->decimal('twd_rate', 14, 2)->nullable();
            $table->unsignedBigInteger('sales_agent')->nullable();
            $table->string('status')->nullable();
            $table->text('msg')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->unsignedBigInteger('handled_by')->nullable();
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->timestamps();
        });

        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('villas', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('optional_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotels_id')->nullable();
            $table->unsignedBigInteger('villas_id')->nullable();
            $table->string('name')->nullable();
            $table->string('service')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->string('type')->nullable();
            $table->decimal('contract_rate', 14, 2)->default(0);
            $table->decimal('markup', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('optional_rate_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('service')->nullable();
            $table->unsignedBigInteger('optional_rate_id')->nullable();
            $table->integer('number_of_guest')->nullable();
            $table->dateTime('service_date')->nullable();
            $table->decimal('price_pax', 14, 2)->default(0);
            $table->decimal('price_total', 14, 2)->default(0);
            $table->string('mandatory')->nullable();
            $table->timestamps();
        });

        Schema::create('extra_beds', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('hotels_id')->nullable();
            $table->string('type')->nullable();
            $table->decimal('contract_rate', 14, 2)->default(0);
            $table->decimal('markup', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('airport_shuttles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->dateTime('date')->nullable();
            $table->string('flight_number')->nullable();
            $table->integer('number_of_guests')->nullable();
            $table->timestamps();
        });

        Schema::create('order_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('action')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable();
            $table->string('agent')->nullable();
            $table->unsignedBigInteger('admin')->nullable();
            $table->timestamps();
        });

        Schema::create('user_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action')->nullable();
            $table->string('service')->nullable();
            $table->string('subservice')->nullable();
            $table->unsignedBigInteger('subservice_id')->nullable();
            $table->string('page')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_ip')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('age')->nullable();
            $table->string('sex')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('identification_type')->nullable();
            $table->string('identification_no')->nullable();
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('rsv_no')->nullable();
            $table->string('service')->nullable();
            $table->unsignedBigInteger('agn_id')->nullable();
            $table->unsignedBigInteger('adm_id')->nullable();
            $table->dateTime('checkin')->nullable();
            $table->dateTime('checkout')->nullable();
            $table->dateTime('pickup_date')->nullable();
            $table->dateTime('dropoff_date')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_admins', function (Blueprint $table) {
            $table->id();
            $table->string('admin_code')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->string('inv_no')->nullable();
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->dateTime('inv_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->decimal('total_usd', 14, 2)->default(0);
            $table->decimal('total_idr', 14, 2)->default(0);
            $table->decimal('total_cny', 14, 2)->default(0);
            $table->decimal('total_twd', 14, 2)->default(0);
            $table->decimal('balance', 14, 2)->default(0);
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->decimal('rate_usd', 14, 2)->default(1);
            $table->decimal('sell_usd', 14, 2)->default(1);
            $table->decimal('rate_cny', 14, 2)->default(1);
            $table->decimal('sell_cny', 14, 2)->default(1);
            $table->decimal('rate_twd', 14, 2)->default(1);
            $table->decimal('sell_twd', 14, 2)->default(1);
            $table->timestamps();
        });

        Schema::create('payment_confirmations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inv_id')->nullable();
            $table->unsignedBigInteger('kurs_id')->nullable();
            $table->string('receipt_img')->nullable();
            $table->date('payment_date')->nullable();
            $table->decimal('amount', 14, 2)->nullable();
            $table->text('note')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    private function seedReferenceRows(): void
    {
        DB::table('taxes')->insert(['id' => 1, 'name' => 'tax', 'tax' => 0]);
        foreach ([['USD', 15000], ['CNY', 2000], ['TWD', 500]] as $index => [$name, $rate]) {
            DB::table('usd_rates')->insert([
                'id' => $index + 1,
                'name' => $name,
                'rate' => $rate,
                'sell' => $rate,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        DB::table('bank_accounts')->insert(['id' => 1, 'bank' => 'Test Bank', 'currency' => 'USD']);
        DB::table('business_profiles')->insert(['id' => 1, 'profile_key' => 'primary', 'name' => 'Bali Kami Tour', 'caption' => 'Travel']);
        DB::table('partners')->insert(['id' => 1, 'name' => 'Partner A', 'status' => 'Draft']);
        DB::table('activity_types')->insert(['id' => 1, 'type' => 'Water']);
        DB::table('activities')->insert([
            'id' => 1,
            'partners_id' => 1,
            'name' => 'Test Activity',
            'code' => 'ACT-001',
            'type' => 'Water',
            'location' => 'Bali',
            'map' => 'https://maps.example.test/activity',
            'description' => 'Activity description snapshot',
            'description_traditional' => 'Activity traditional description snapshot',
            'description_simplified' => 'Activity simplified description snapshot',
            'itinerary' => 'Activity itinerary snapshot',
            'itinerary_traditional' => 'Activity traditional itinerary snapshot',
            'itinerary_simplified' => 'Activity simplified itinerary snapshot',
            'duration' => '2 Hours',
            'include' => 'Activity include snapshot',
            'include_traditional' => 'Activity traditional include snapshot',
            'include_simplified' => 'Activity simplified include snapshot',
            'additional_info' => 'Activity info snapshot',
            'additional_info_traditional' => 'Activity traditional info snapshot',
            'additional_info_simplified' => 'Activity simplified info snapshot',
            'cancellation_policy' => 'Activity cancellation snapshot',
            'cancellation_policy_traditional' => 'Activity traditional cancellation snapshot',
            'cancellation_policy_simplified' => 'Activity simplified cancellation snapshot',
            'contract_rate' => 1500000,
            'markup' => 50,
            'qty' => '5',
            'min_pax' => '2',
            'status' => 'Active',
            'validity' => '2026-12-31',
            'cover' => 'activity-cover.jpg',
            'author_id' => 10,
        ]);
    }
}
