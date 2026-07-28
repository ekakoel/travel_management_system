<?php

namespace Tests\Feature;

use App\Http\Middleware\LogActivityMiddleware;
use App\Http\Middleware\TrackWebsiteVisit;
use App\Http\Requests\StoreHotelRoomRequest;
use App\Http\Requests\UpdateHotelRoomRequest;
use App\Mail\ReservationMail;
use App\Models\HotelPackage;
use App\Models\HotelPrice;
use App\Models\HotelPromo;
use App\Models\InvoiceAdmin;
use App\Models\Orders;
use App\Models\Reservation;
use App\Models\User;
use App\Services\AccommodationBookingGuardService;
use App\Services\AccommodationOrderLifecycleService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class AccommodationAuthoritativePricingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!$this->usingSafeSqliteMemoryDatabase()) {
            $this->markTestSkipped('Accommodation pricing feature tests require sqlite :memory: to avoid touching active data.');
        }

        $this->withoutMiddleware([
            LogActivityMiddleware::class,
            TrackWebsiteVisit::class,
        ]);

        Cache::flush();
        Mail::fake();
        Carbon::setTestNow('2026-07-27 12:00:00');
        $this->prepareSchema();
        $this->seedReferenceRows();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_normal_hotel_order_ignores_tampered_hidden_price_fields(): void
    {
        $user = $this->actingUser(10, 'developer');

        HotelPrice::create([
            'hotels_id' => 1,
            'rooms_id' => 1,
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-02',
            'contract_rate' => 1500000,
            'markup' => 20,
            'kick_back' => 5,
            'author' => 1,
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'booking_dates' => [
                    'checkin' => '2026-08-01',
                    'checkout' => '2026-08-03',
                    'duration' => 99,
                ],
                'bookingcode' => [
                    'code' => 'SAVE30',
                    'discounts' => 30,
                ],
            ])
            ->post(route('func.create.order-hotel-normal'), $this->normalHotelPayload([
                'duration' => 99,
                'var_normal_price' => 1,
                'var_kick_back_total' => 999,
                'var_kick_back_per_room' => 999,
                'var_promotions_discount' => 999,
            ]));

        $response->assertRedirect(route('view.detail-order-admin', ['id' => 1]));

        $order = DB::table('orders')->where('orderno', 'HTL-PRICE-001')->first();
        $this->assertNotNull($order);
        $this->assertSame('Hotel', $order->service);
        $this->assertSame('2', (string) $order->duration);
        $this->assertSame('240', (string) $order->price_pax);
        $this->assertSame('480', (string) $order->normal_price);
        $this->assertSame('20', (string) $order->kick_back);
        $this->assertSame('460', (string) $order->price_total);
        $this->assertSame('SAVE30', $order->bookingcode);
        $this->assertSame('30', (string) $order->bookingcode_disc);
        $this->assertSame('430', (string) $order->final_price);
        $this->assertSame(1, (int) DB::table('booking_codes')->where('code', 'SAVE30')->value('used'));
        Mail::assertSent(ReservationMail::class, 1);
    }

    public function test_normal_hotel_order_rejects_missing_authoritative_rate(): void
    {
        $user = $this->actingUser(10, 'developer');

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

        $response = $this->actingAs($user)
            ->withSession([
                'booking_dates' => [
                    'checkin' => '2026-08-01',
                    'checkout' => '2026-08-03',
                ],
            ])
            ->from('/order-form')
            ->post(route('func.create.order-hotel-normal'), $this->normalHotelPayload());

        $response->assertSessionHasErrors('price');
        $this->assertSame(0, DB::table('orders')->count());
        Mail::assertNothingSent();
    }

    public function test_normal_hotel_order_uses_server_side_add_on_prices(): void
    {
        $user = $this->actingUser(10, 'developer');
        $this->seedNormalRates('2026-08-01', '2026-08-02', 1500000, 20, 5);
        $this->seedExtraBed(1, 1, 300000, 5);
        $this->seedAirportShuttle(1, 1, 600000, 8);

        $response = $this->actingAs($user)
            ->withSession([
                'booking_dates' => [
                    'checkin' => '2026-08-01',
                    'checkout' => '2026-08-03',
                ],
            ])
            ->post(route('func.create.order-hotel-normal'), $this->normalHotelPayload([
                'number_of_guests' => [3],
                'guest_detail' => ['Room 1'],
                'special_day' => [null],
                'special_date' => [null],
                'extra_bed_id' => [1],
                'flight_type' => ['arrival'],
                'flight_number' => ['GA-001'],
                'flight_time' => ['2026-08-01 10:00:00'],
                'flight_transport_id' => [1],
                'airport_shuttle_in_price' => 1,
                'transport_in_price_id' => 999,
            ]));

        $response->assertRedirect(route('view.detail-order-admin', ['id' => 1]));

        $order = DB::table('orders')->where('orderno', 'HTL-PRICE-001')->first();
        $this->assertSame('240', (string) $order->normal_price);
        $this->assertSame('280', (string) $order->price_total);
        $this->assertSame('50', (string) $order->extra_bed_total_price);
        $this->assertSame('48', (string) $order->airport_shuttle_price);
        $this->assertSame('328', (string) $order->final_price);

        $shuttle = DB::table('airport_shuttles')->where('order_id', $order->id)->first();
        $this->assertSame('1', (string) $shuttle->price_id);
        $this->assertSame('48', (string) $shuttle->price);
    }

    public function test_staff_created_pending_hotel_order_creates_pending_reservation_in_same_flow(): void
    {
        Mail::fake();
        $user = $this->actingUser(10, 'developer');
        $this->seedNormalRates('2026-08-01', '2026-08-02', 1500000, 20, 5);

        $this->actingAs($user)
            ->withSession([
                'booking_dates' => [
                    'checkin' => '2026-08-01',
                    'checkout' => '2026-08-03',
                ],
            ])
            ->from('/hotel-form')
            ->post(route('func.create.order-hotel-normal'), $this->normalHotelPayload([
                'orderno' => 'HTL-RSV-001',
            ]))
            ->assertRedirect(route('view.detail-order-admin', ['id' => 1]));

        $order = DB::table('orders')->where('orderno', 'HTL-RSV-001')->first();
        $this->assertNotNull($order->rsv_id);

        $reservation = DB::table('reservations')->where('id', $order->rsv_id)->first();
        $this->assertSame('HTL-RSV-001', $reservation->rsv_no);
        $this->assertSame('Hotel', $reservation->service);
        $this->assertSame('Pending', $reservation->status);
        $this->assertSame('2026-08-01', (string) $reservation->checkin);
        $this->assertSame('2026-08-03', (string) $reservation->checkout);
    }

    public function test_customer_submitted_hotel_order_creates_pending_reservation_after_draft_submit(): void
    {
        Mail::fake();
        $user = $this->actingUser(10, 'agent');
        $this->seedNormalRates('2026-08-01', '2026-08-02', 1500000, 20, 5);

        $this->actingAs($user)
            ->withSession([
                'booking_dates' => [
                    'checkin' => '2026-08-01',
                    'checkout' => '2026-08-03',
                ],
            ])
            ->from('/hotel-form')
            ->post(route('func.create.order-hotel-normal'), $this->normalHotelPayload([
                'orderno' => 'HTL-RSV-002',
            ]))
            ->assertRedirect(route('view.detail-order-hotel', ['id' => 1]));

        $order = DB::table('orders')->where('orderno', 'HTL-RSV-002')->first();
        $this->assertSame('Pending', $order->status);
        $this->assertNotNull($order->rsv_id);
        $this->assertSame('Pending', DB::table('reservations')->where('id', $order->rsv_id)->value('status'));
        Mail::assertSent(ReservationMail::class, 1);
    }

    public function test_accommodation_current_and_history_classification_uses_checkout_and_status(): void
    {
        $paidInService = $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-IN-SERVICE',
            'sales_agent' => 1,
            'status' => 'Paid',
            'checkin' => '2026-07-26',
            'checkout' => '2026-07-29',
        ]);
        $paidPastStay = $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-PAST-PAID',
            'sales_agent' => 1,
            'status' => 'Paid',
            'checkin' => '2026-07-20',
            'checkout' => '2026-07-22',
        ]);
        $completedPastStay = $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-PAST-COMPLETED',
            'sales_agent' => 1,
            'status' => 'Paid',
            'completed_at' => '2026-07-27 12:00:00',
            'checkin' => '2026-07-20',
            'checkout' => '2026-07-22',
        ]);
        $approvedPastStay = $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-PAST-APPROVED',
            'sales_agent' => 1,
            'status' => 'Approved',
            'checkin' => '2026-07-20',
            'checkout' => '2026-07-22',
        ]);
        $canceledFutureStay = $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-CANCELED',
            'sales_agent' => 1,
            'status' => 'Canceled',
            'checkin' => '2026-08-20',
            'checkout' => '2026-08-22',
        ]);

        $lifecycle = app(AccommodationOrderLifecycleService::class);
        $now = Carbon::parse('2026-07-27 12:00:00');
        $currentIds = $lifecycle->applyAccommodationCurrentScope(Orders::query()->where('sales_agent', 1), $now)
            ->pluck('id')
            ->all();
        $historyIds = $lifecycle->applyAccommodationHistoryScope(Orders::query()->where('sales_agent', 1), $now)
            ->pluck('id')
            ->all();

        $this->assertContains($paidInService, $currentIds);
        $this->assertContains($paidPastStay, $currentIds);
        $this->assertContains($approvedPastStay, $currentIds);
        $this->assertNotContains($completedPastStay, $currentIds);
        $this->assertContains($completedPastStay, $historyIds);
        $this->assertContains($canceledFutureStay, $historyIds);
        $this->assertNotContains($paidPastStay, $historyIds);
        $this->assertNotContains($approvedPastStay, $historyIds);
        $this->assertSame('in_service', $lifecycle->displayGroup(Orders::findOrFail($paidInService), $now));
        $this->assertSame('history', $lifecycle->displayGroup(Orders::findOrFail($completedPastStay), $now));
    }

    public function test_completion_service_moves_eligible_paid_accommodation_to_completed_with_reservation(): void
    {
        $reservation = Reservation::create([
            'rsv_no' => 'HTL-COMPLETE-001',
            'service' => 'Hotel',
            'agn_id' => 1,
            'adm_id' => 2,
            'checkin' => '2026-07-20',
            'checkout' => '2026-07-22',
            'status' => 'Active',
        ]);
        $eligible = $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-COMPLETE-001',
            'sales_agent' => 1,
            'rsv_id' => $reservation->id,
            'status' => 'Paid',
            'checkin' => '2026-07-20',
            'checkout' => '2026-07-22',
        ]);
        $notPaid = $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-NOT-COMPLETE',
            'sales_agent' => 1,
            'status' => 'Approved',
            'checkin' => '2026-07-20',
            'checkout' => '2026-07-22',
        ]);

        $completed = app(AccommodationOrderLifecycleService::class)
            ->completeEligiblePaidOrders(1, Carbon::parse('2026-07-27 12:00:00'));

        $this->assertSame(1, $completed);
        $this->assertSame('Paid', Orders::findOrFail($eligible)->status);
        $this->assertNotNull(Orders::findOrFail($eligible)->completed_at);
        $this->assertSame('Completed', Reservation::findOrFail($reservation->id)->status);
        $this->assertSame('Approved', Orders::findOrFail($notPaid)->status);
        $this->assertNull(Orders::findOrFail($notPaid)->completed_at);
        $this->assertDatabaseHas('order_logs', [
            'order_id' => $eligible,
            'action' => 'Complete Accommodation Order',
            'method' => 'Complete',
        ]);
    }

    public function test_completion_command_runs_owner_scoped_accommodation_completion(): void
    {
        $ownerReservation = Reservation::create([
            'rsv_no' => 'HTL-CMD-OWNER',
            'service' => 'Hotel',
            'agn_id' => 1,
            'adm_id' => 2,
            'checkin' => '2026-07-20',
            'checkout' => '2026-07-22',
            'status' => 'Active',
        ]);
        $otherReservation = Reservation::create([
            'rsv_no' => 'HTL-CMD-OTHER',
            'service' => 'Hotel',
            'agn_id' => 2,
            'adm_id' => 2,
            'checkin' => '2026-07-20',
            'checkout' => '2026-07-22',
            'status' => 'Active',
        ]);
        $ownerOrder = $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-CMD-OWNER',
            'sales_agent' => 1,
            'rsv_id' => $ownerReservation->id,
            'status' => 'Paid',
            'checkin' => '2026-07-20',
            'checkout' => '2026-07-22',
        ]);
        $otherOrder = $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-CMD-OTHER',
            'sales_agent' => 2,
            'rsv_id' => $otherReservation->id,
            'status' => 'Paid',
            'checkin' => '2026-07-20',
            'checkout' => '2026-07-22',
        ]);

        $this->artisan('accommodation:complete-eligible-orders', ['--owner' => 1])
            ->expectsOutput('Completed 1 eligible Accommodation order(s).')
            ->assertExitCode(0);

        $this->assertSame('Paid', Orders::findOrFail($ownerOrder)->status);
        $this->assertNotNull(Orders::findOrFail($ownerOrder)->completed_at);
        $this->assertSame('Paid', Orders::findOrFail($otherOrder)->status);
        $this->assertNull(Orders::findOrFail($otherOrder)->completed_at);
    }

    public function test_completion_skips_unpaid_and_terminal_accommodation_orders(): void
    {
        $skippedIds = collect(['Approved', 'Canceled', 'Rejected', 'Invalid'])
            ->map(function ($status) {
                return $this->insertExistingAccommodationOrder([
                    'orderno' => 'HTL-SKIP-'.$status,
                    'sales_agent' => 1,
                    'status' => $status,
                    'checkin' => '2026-07-20',
                    'checkout' => '2026-07-22',
                ]);
            });

        $completed = app(AccommodationOrderLifecycleService::class)
            ->completeEligiblePaidOrders(1, Carbon::parse('2026-07-27 12:00:00'));

        $this->assertSame(0, $completed);
        foreach ($skippedIds as $skippedId) {
            $this->assertNull(Orders::findOrFail($skippedId)->completed_at);
        }
    }

    public function test_inventory_three_units_allows_booking_when_one_unit_remains(): void
    {
        DB::table('hotel_rooms')->where('id', 1)->update(['inventory' => 3]);
        $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-INVENTORY-2',
            'status' => 'Approved',
            'number_of_room' => 2,
            'checkin' => '2026-08-01',
            'checkout' => '2026-08-03',
        ]);

        app(AccommodationBookingGuardService::class)->ensureRoomCanBeBooked(
            1,
            1,
            '2026-08-01',
            '2026-08-03',
            1,
            null,
            true
        );

        $this->assertTrue(true);
    }

    public function test_inventory_rejects_booking_when_requested_rooms_exceed_remaining_units(): void
    {
        DB::table('hotel_rooms')->where('id', 1)->update(['inventory' => 3]);
        $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-INVENTORY-FULL',
            'status' => 'Pending',
            'number_of_room' => 2,
            'checkin' => '2026-08-01',
            'checkout' => '2026-08-03',
        ]);

        $this->expectException(ValidationException::class);

        app(AccommodationBookingGuardService::class)->ensureRoomCanBeBooked(
            1,
            1,
            '2026-08-01',
            '2026-08-03',
            2,
            null,
            true
        );
    }

    public function test_terminal_accommodation_orders_do_not_block_room_inventory(): void
    {
        DB::table('hotel_rooms')->where('id', 1)->update(['inventory' => 1]);

        foreach (['Canceled', 'Rejected', 'Invalid', 'Deleted'] as $status) {
            $this->insertExistingAccommodationOrder([
                'orderno' => 'HTL-TERMINAL-'.$status,
                'status' => $status,
                'number_of_room' => 1,
                'checkin' => '2026-08-01',
                'checkout' => '2026-08-03',
            ]);
        }

        app(AccommodationBookingGuardService::class)->ensureRoomCanBeBooked(
            1,
            1,
            '2026-08-01',
            '2026-08-03',
            1,
            null,
            true
        );

        $this->assertTrue(true);
    }

    public function test_accommodation_overlap_counts_dates_but_allows_same_day_checkout_turnover(): void
    {
        DB::table('hotel_rooms')->where('id', 1)->update(['inventory' => 1]);
        $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-OVERLAP',
            'status' => 'Paid',
            'number_of_room' => 1,
            'checkin' => '2026-08-01',
            'checkout' => '2026-08-03',
        ]);

        app(AccommodationBookingGuardService::class)->ensureRoomCanBeBooked(
            1,
            1,
            '2026-08-03',
            '2026-08-05',
            1,
            null,
            true
        );

        $this->expectException(ValidationException::class);

        app(AccommodationBookingGuardService::class)->ensureRoomCanBeBooked(
            1,
            1,
            '2026-08-02',
            '2026-08-04',
            1,
            null,
            true
        );
    }

    public function test_room_inventory_fallback_is_conservative_when_inventory_is_missing(): void
    {
        DB::table('hotel_rooms')->where('id', 1)->update(['inventory' => null]);
        $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-FALLBACK',
            'status' => 'Approved',
            'number_of_room' => 1,
            'checkin' => '2026-08-01',
            'checkout' => '2026-08-03',
        ]);

        $this->expectException(ValidationException::class);

        app(AccommodationBookingGuardService::class)->ensureRoomCanBeBooked(
            1,
            1,
            '2026-08-01',
            '2026-08-03',
            1,
            null,
            true
        );
    }

    public function test_locked_revalidation_prevents_second_booking_from_exceeding_inventory(): void
    {
        DB::table('hotel_rooms')->where('id', 1)->update(['inventory' => 1]);
        $guard = app(AccommodationBookingGuardService::class);

        DB::transaction(function () use ($guard) {
            $guard->ensureRoomCanBeBooked(1, 1, '2026-08-01', '2026-08-03', 1, null, true);

            $this->insertExistingAccommodationOrder([
                'orderno' => 'HTL-FIRST-LOCKED',
                'status' => 'Pending',
                'number_of_room' => 1,
                'checkin' => '2026-08-01',
                'checkout' => '2026-08-03',
            ]);

            $this->expectException(ValidationException::class);

            $guard->ensureRoomCanBeBooked(1, 1, '2026-08-01', '2026-08-03', 1, null, true);
        });
    }

    public function test_audit_room_inventory_command_reports_null_zero_and_fallback_counts(): void
    {
        DB::table('hotel_rooms')->where('id', 1)->update(['inventory' => null]);
        DB::table('hotel_rooms')->insert([
            'id' => 3,
            'hotels_id' => 1,
            'rooms' => 'Stop Sell Suite',
            'capacity' => 2,
            'capacity_adult' => 2,
            'capacity_child' => 0,
            'inventory' => 0,
            'status' => 'Draft',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('accommodation:audit-room-inventory')
            ->expectsOutputToContain('Inventory null: 1')
            ->expectsOutputToContain('Inventory zero: 1')
            ->expectsOutputToContain('Using fallback 1: 1')
            ->assertExitCode(0);
    }

    public function test_export_room_inventory_command_writes_expected_csv(): void
    {
        $path = storage_path('framework/testing/accommodation-room-inventory-export.csv');
        @unlink($path);

        $this->artisan('accommodation:export-room-inventory', [
            'path' => $path,
            '--active-only' => true,
        ])
            ->expectsOutputToContain('Exported 2 room(s).')
            ->assertExitCode(0);

        $this->assertFileExists($path);
        $csv = file($path, FILE_IGNORE_NEW_LINES);

        $this->assertSame(
            'hotel_id,hotel_name,room_id,room_name,status,current_inventory,new_inventory',
            $csv[0]
        );
        $this->assertStringContainsString('1,"Server Hotel",1,Suite,Active,3,', $csv[1]);
    }

    public function test_import_room_inventory_dry_run_validates_without_changing_inventory(): void
    {
        $path = $this->writeInventoryCsv([
            [1, 'Server Hotel', 1, 'Suite', 'Active', 3, 5],
        ]);

        $this->artisan('accommodation:import-room-inventory', [
            'path' => $path,
            '--dry-run' => true,
        ])
            ->expectsOutputToContain('Dry run only. No room inventory was changed.')
            ->expectsOutputToContain('Changed: 1')
            ->assertExitCode(0);

        $this->assertSame(3, (int) DB::table('hotel_rooms')->where('id', 1)->value('inventory'));
    }

    public function test_import_room_inventory_updates_only_listed_rooms_and_writes_audit_log(): void
    {
        $path = $this->writeInventoryCsv([
            [1, 'Server Hotel', 1, 'Suite', 'Active', 3, 5],
        ]);

        $this->artisan('accommodation:import-room-inventory', ['path' => $path])
            ->expectsOutputToContain('Changed: 1')
            ->expectsOutputToContain('Missing: 1')
            ->assertExitCode(0);

        $this->assertSame(5, (int) DB::table('hotel_rooms')->where('id', 1)->value('inventory'));
        $this->assertSame(3, (int) DB::table('hotel_rooms')->where('id', 2)->value('inventory'));
        $this->assertDatabaseHas('action_logs', [
            'action' => 'Import Accommodation Room Inventory',
            'service' => 'HotelRoom',
            'service_id' => 1,
            'initial_state' => '3',
            'final_state' => '5',
        ]);
    }

    public function test_import_room_inventory_rejects_invalid_value(): void
    {
        $path = $this->writeInventoryCsv([
            [1, 'Server Hotel', 1, 'Suite', 'Active', 3, -1],
        ]);

        $this->artisan('accommodation:import-room-inventory', ['path' => $path])
            ->expectsOutputToContain('new_inventory cannot be negative')
            ->expectsOutputToContain('Rejected: 1')
            ->assertExitCode(1);

        $this->assertSame(3, (int) DB::table('hotel_rooms')->where('id', 1)->value('inventory'));
    }

    public function test_import_room_inventory_rejects_duplicate_room_id(): void
    {
        $path = $this->writeInventoryCsv([
            [1, 'Server Hotel', 1, 'Suite', 'Active', 3, 4],
            [1, 'Server Hotel', 1, 'Suite', 'Active', 3, 5],
        ]);

        $this->artisan('accommodation:import-room-inventory', ['path' => $path])
            ->expectsOutputToContain('duplicate room_id 1')
            ->assertExitCode(1);

        $this->assertSame(3, (int) DB::table('hotel_rooms')->where('id', 1)->value('inventory'));
    }

    public function test_import_room_inventory_rejects_wrong_hotel_relation(): void
    {
        $path = $this->writeInventoryCsv([
            [2, 'Other Hotel', 1, 'Suite', 'Active', 3, 4],
        ]);

        $this->artisan('accommodation:import-room-inventory', ['path' => $path])
            ->expectsOutputToContain('belongs to hotel 1, not 2')
            ->assertExitCode(1);

        $this->assertSame(3, (int) DB::table('hotel_rooms')->where('id', 1)->value('inventory'));
    }

    public function test_import_room_inventory_rolls_back_when_audit_log_write_fails(): void
    {
        DB::unprepared("CREATE TRIGGER fail_inventory_action_log BEFORE INSERT ON action_logs BEGIN SELECT RAISE(ABORT, 'forced action log failure'); END;");

        $path = $this->writeInventoryCsv([
            [1, 'Server Hotel', 1, 'Suite', 'Active', 3, 6],
        ]);

        $this->artisan('accommodation:import-room-inventory', ['path' => $path])
            ->expectsOutputToContain('Import failed. No room inventory was changed.')
            ->assertExitCode(1);

        $this->assertSame(3, (int) DB::table('hotel_rooms')->where('id', 1)->value('inventory'));
    }

    public function test_repair_missing_reservations_dry_run_reports_without_changing_order(): void
    {
        $orderId = $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-REPAIR-DRY',
            'status' => 'Pending',
            'rsv_id' => null,
        ]);

        $this->artisan('accommodation:repair-missing-reservations', ['--dry-run' => true])
            ->expectsOutputToContain('Dry run only. No reservation was created.')
            ->expectsOutputToContain('Eligible: 1')
            ->assertExitCode(0);

        $this->assertNull(DB::table('orders')->where('id', $orderId)->value('rsv_id'));
        $this->assertSame(0, DB::table('reservations')->count());
    }

    public function test_repair_missing_reservations_creates_pending_reservation_and_is_idempotent(): void
    {
        $orderId = $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-REPAIR-001',
            'status' => 'Pending',
            'rsv_id' => null,
        ]);
        $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-REPAIR-SKIP',
            'status' => 'Approved',
            'rsv_id' => null,
        ]);

        $this->artisan('accommodation:repair-missing-reservations')
            ->expectsOutputToContain('Created 1 missing Accommodation reservation(s).')
            ->assertExitCode(0);

        $order = DB::table('orders')->where('id', $orderId)->first();
        $this->assertNotNull($order->rsv_id);
        $this->assertDatabaseHas('reservations', [
            'id' => $order->rsv_id,
            'rsv_no' => 'HTL-REPAIR-001',
            'status' => 'Pending',
        ]);
        $this->assertDatabaseHas('order_logs', [
            'order_id' => (string) $orderId,
            'action' => 'Repair Missing Accommodation Reservation',
        ]);

        $this->artisan('accommodation:repair-missing-reservations')
            ->expectsOutputToContain('Changed: 0')
            ->assertExitCode(0);

        $this->assertSame(1, DB::table('reservations')->count());
        $this->assertNull(DB::table('orders')->where('orderno', 'HTL-REPAIR-SKIP')->value('rsv_id'));
    }

    public function test_room_inventory_validation_requires_positive_inventory_for_active_rooms(): void
    {
        $storeRequest = StoreHotelRoomRequest::create('/rooms', 'POST', [
            'hotels_id' => 1,
            'rooms' => 'Suite',
            'room_view' => 'Garden',
            'beds' => 'King',
            'inventory' => 0,
        ]);
        $storeValidator = Validator::make($storeRequest->all(), (new StoreHotelRoomRequest())->rules());

        $this->assertTrue($storeValidator->fails());
        $this->assertArrayHasKey('inventory', $storeValidator->errors()->toArray());

        $updateRequest = UpdateHotelRoomRequest::create('/rooms/1', 'POST', [
            'hotels_id' => 1,
            'rooms' => 'Suite',
            'room_view' => 'Garden',
            'beds' => 'King',
            'status' => 'Active',
            'inventory' => 0,
        ]);
        $updateValidator = Validator::make($updateRequest->all(), $updateRequest->rules());
        $updateRequest->withValidator($updateValidator);

        $this->assertTrue($updateValidator->fails());
        $this->assertArrayHasKey('inventory', $updateValidator->errors()->toArray());

        $stopSellRequest = UpdateHotelRoomRequest::create('/rooms/1', 'POST', [
            'hotels_id' => 1,
            'rooms' => 'Suite',
            'room_view' => 'Garden',
            'beds' => 'King',
            'status' => 'Draft',
            'inventory' => 0,
        ]);
        $stopSellValidator = Validator::make($stopSellRequest->all(), $stopSellRequest->rules());
        $stopSellRequest->withValidator($stopSellValidator);

        $this->assertFalse($stopSellValidator->fails());
    }

    public function test_normal_hotel_order_revalidates_availability_before_insert(): void
    {
        Mail::fake();
        $user = $this->actingUser(10, 'developer');
        DB::table('hotel_rooms')->where('id', 1)->update(['inventory' => 2]);
        $this->seedNormalRates('2026-08-01', '2026-08-02', 1500000, 20, 5);
        $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-BLOCKING',
            'status' => 'Pending',
            'checkin' => '2026-08-01',
            'checkout' => '2026-08-03',
        ]);

        $this->actingAs($user)
            ->withSession([
                'booking_dates' => [
                    'checkin' => '2026-08-01',
                    'checkout' => '2026-08-03',
                ],
            ])
            ->from('/hotel-form')
            ->post(route('func.create.order-hotel-normal'), $this->normalHotelPayload([
                'orderno' => 'HTL-AVAIL-001',
            ]))
            ->assertSessionHasErrors('availability');

        $this->assertSame(1, DB::table('orders')->whereIn('orderno', ['HTL-BLOCKING', 'HTL-AVAIL-001'])->count());
        Mail::assertNothingSent();
    }

    public function test_canceled_accommodation_order_does_not_block_new_booking(): void
    {
        Mail::fake();
        $user = $this->actingUser(10, 'developer');
        $this->seedNormalRates('2026-08-01', '2026-08-02', 1500000, 20, 5);
        $this->insertExistingAccommodationOrder([
            'orderno' => 'HTL-CANCELED',
            'status' => 'Canceled',
            'checkin' => '2026-08-01',
            'checkout' => '2026-08-03',
        ]);

        $this->actingAs($user)
            ->withSession([
                'booking_dates' => [
                    'checkin' => '2026-08-01',
                    'checkout' => '2026-08-03',
                ],
            ])
            ->from('/hotel-form')
            ->post(route('func.create.order-hotel-normal'), $this->normalHotelPayload([
                'orderno' => 'HTL-AVAIL-002',
            ]))
            ->assertRedirect(route('view.detail-order-admin', ['id' => 2]));

        $this->assertSame('Pending', DB::table('orders')->where('orderno', 'HTL-AVAIL-002')->value('status'));
        Mail::assertSent(ReservationMail::class, 1);
    }

    public function test_accommodation_duplicate_submission_token_returns_existing_order_without_creating_duplicate(): void
    {
        Mail::fake();
        $user = $this->actingUser(10, 'developer');
        $this->seedNormalRates('2026-08-01', '2026-08-02', 1500000, 20, 5);
        $payload = $this->normalHotelPayload([
            'orderno' => 'HTL-TOKEN-001',
            'submission_token' => 'same-accommodation-token',
        ]);

        $this->actingAs($user)
            ->withSession([
                'booking_dates' => [
                    'checkin' => '2026-08-01',
                    'checkout' => '2026-08-03',
                ],
            ])
            ->from('/hotel-form')
            ->post(route('func.create.order-hotel-normal'), $payload)
            ->assertRedirect(route('view.detail-order-admin', ['id' => 1]));

        $this->actingAs($user)
            ->from('/hotel-form')
            ->post(route('func.create.order-hotel-normal'), array_merge($payload, [
                'orderno' => 'HTL-TOKEN-REPOST',
                'final_price' => 1,
            ]))
            ->assertRedirect(route('view.detail-order-admin', ['id' => 1]));

        $this->assertSame(1, DB::table('orders')->count());
        $this->assertSame('HTL-TOKEN-001', DB::table('orders')->value('orderno'));
        Mail::assertSent(ReservationMail::class, 1);
    }

    public function test_promo_hotel_order_happy_path_uses_authoritative_price_and_fallback(): void
    {
        $user = $this->actingUser(10, 'developer');
        $this->seedNormalRates('2026-08-02', '2026-08-02', 1500000, 20, 5);
        $promo = $this->seedPromo(1, 1, [
            'periode_start' => '2026-08-01',
            'periode_end' => '2026-08-01',
            'contract_rate' => 900000,
            'markup' => 10,
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'booking_dates' => [
                    'checkin' => '2026-08-01',
                    'checkout' => '2026-08-03',
                ],
            ])
            ->post(route('func.create.order-hotel-promo'), $this->promoHotelPayload([
                'promo_id' => json_encode([$promo->id]),
                'price_list' => json_encode([1, 1]),
                'promo_price' => 1,
                'var_promo_price' => 1,
            ]));

        $response->assertRedirect(route('view.detail-order-admin', ['id' => 1]));

        $order = DB::table('orders')->where('orderno', 'HPP-PRICE-001')->first();
        $this->assertSame('Hotel Promo', $order->service);
        $this->assertSame('190', (string) $order->price_pax);
        $this->assertSame('380', (string) $order->normal_price);
        $this->assertSame('380', (string) $order->price_total);
        $this->assertSame('380', (string) $order->final_price);
        $this->assertStringContainsString('Promo One', $order->promo_name);
    }

    public function test_promo_hotel_order_rejects_wrong_expired_minimum_stay_and_missing_fallback(): void
    {
        $user = $this->actingUser(10, 'developer');

        $wrongHotelPromo = $this->seedPromo(2, 2);
        $this->actingAs($user)
            ->withSession(['booking_dates' => ['checkin' => '2026-08-01', 'checkout' => '2026-08-03']])
            ->from('/promo-form')
            ->post(route('func.create.order-hotel-promo'), $this->promoHotelPayload(['promo_id' => json_encode([$wrongHotelPromo->id])]))
            ->assertSessionHasErrors('promo_id');

        $expiredPromo = $this->seedPromo(1, 1, [
            'book_periode_start' => '2026-01-01',
            'book_periode_end' => '2026-01-31',
        ]);
        $this->actingAs($user)
            ->withSession(['booking_dates' => ['checkin' => '2026-08-01', 'checkout' => '2026-08-03']])
            ->from('/promo-form')
            ->post(route('func.create.order-hotel-promo'), $this->promoHotelPayload(['promo_id' => json_encode([$expiredPromo->id])]))
            ->assertSessionHasErrors('promo_id');

        $minimumStayPromo = $this->seedPromo(1, 1, ['minimum_stay' => 3]);
        $this->seedNormalRates('2026-08-01', '2026-08-01', 2000000, 10, 0);
        $this->actingAs($user)
            ->withSession(['booking_dates' => ['checkin' => '2026-08-01', 'checkout' => '2026-08-02']])
            ->from('/promo-form')
            ->post(route('func.create.order-hotel-promo'), $this->promoHotelPayload([
                'duration' => 1,
                'promo_id' => json_encode([$minimumStayPromo->id]),
            ]))
            ->assertSessionHasErrors('promo_id');

        $partialPromo = $this->seedPromo(1, 1, [
            'periode_start' => '2026-08-01',
            'periode_end' => '2026-08-01',
        ]);
        $this->actingAs($user)
            ->withSession(['booking_dates' => ['checkin' => '2026-08-01', 'checkout' => '2026-08-03']])
            ->from('/promo-form')
            ->post(route('func.create.order-hotel-promo'), $this->promoHotelPayload(['promo_id' => json_encode([$partialPromo->id])]))
            ->assertSessionHasErrors('price');

        $this->assertSame(0, DB::table('orders')->count());
    }

    public function test_package_hotel_order_happy_path_uses_authoritative_price(): void
    {
        $user = $this->actingUser(10, 'developer');
        $package = $this->seedPackage(1, 1, [
            'duration' => 2,
            'contract_rate' => 3000000,
            'markup' => 30,
            'benefits' => 'Spa credit',
            'include' => 'Breakfast and dinner',
        ]);

        $response = $this->actingAs($user)
            ->withSession([
                'booking_dates' => [
                    'checkin' => '2026-08-01',
                    'checkout' => '2026-08-03',
                    'duration' => 99,
                ],
            ])
            ->post(route('func.create.order-hotel-package', ['id' => $package->id]), $this->packageHotelPayload([
                'duration' => 99,
                'final_price' => 1,
            ]));

        $response->assertRedirect(route('view.detail-order-admin', ['id' => 1]));

        $order = DB::table('orders')->where('orderno', 'HPA-PRICE-001')->first();
        $this->assertSame('Hotel Package', $order->service);
        $this->assertSame('430', (string) $order->price_pax);
        $this->assertSame('860', (string) $order->normal_price);
        $this->assertSame('860', (string) $order->price_total);
        $this->assertSame('860', (string) $order->final_price);
        $this->assertSame('2', (string) $order->duration);
        $this->assertSame('Spa credit', $order->benefits);
        $this->assertSame('Safe cancellation policy', $order->cancellation_policy);
    }

    public function test_package_hotel_order_rejects_wrong_duration_and_expired_package(): void
    {
        $user = $this->actingUser(10, 'developer');
        $wrongDuration = $this->seedPackage(1, 1, ['duration' => 3]);
        $expired = $this->seedPackage(1, 1, [
            'stay_period_start' => '2026-01-01',
            'stay_period_end' => '2026-01-31',
        ]);

        $this->actingAs($user)
            ->withSession(['booking_dates' => ['checkin' => '2026-08-01', 'checkout' => '2026-08-03']])
            ->from('/package-form')
            ->post(route('func.create.order-hotel-package', ['id' => $wrongDuration->id]), $this->packageHotelPayload())
            ->assertSessionHasErrors('duration');

        $this->actingAs($user)
            ->withSession(['booking_dates' => ['checkin' => '2026-08-01', 'checkout' => '2026-08-03']])
            ->from('/package-form')
            ->post(route('func.create.order-hotel-package', ['id' => $expired->id]), $this->packageHotelPayload())
            ->assertSessionHasErrors('package_id');

        $this->assertSame(0, DB::table('orders')->count());
    }

    public function test_order_create_rolls_back_order_logs_booking_code_and_mail_on_mid_transaction_failure(): void
    {
        $user = $this->actingUser(10, 'developer');
        $this->seedNormalRates('2026-08-01', '2026-08-02', 1500000, 20, 5);
        DB::unprepared("CREATE TRIGGER fail_order_logs_insert BEFORE INSERT ON order_logs BEGIN SELECT RAISE(ABORT, 'forced order log failure'); END;");

        $this->actingAs($user)
            ->withSession([
                'booking_dates' => [
                    'checkin' => '2026-08-01',
                    'checkout' => '2026-08-03',
                ],
                'bookingcode' => [
                    'code' => 'SAVE30',
                    'discounts' => 30,
                ],
            ])
            ->post(route('func.create.order-hotel-normal'), $this->normalHotelPayload())
            ->assertStatus(500);

        $this->assertSame(0, DB::table('orders')->count());
        $this->assertSame(0, DB::table('user_logs')->count());
        $this->assertSame(0, DB::table('order_logs')->count());
        $this->assertSame(0, (int) DB::table('booking_codes')->where('code', 'SAVE30')->value('used'));
        Mail::assertNothingSent();
    }

    public function test_invoice_generation_uses_order_snapshot_after_rate_changes(): void
    {
        $admin = $this->actingUser(20, 'developer');
        $reservation = Reservation::create([
            'id' => 1,
            'rsv_no' => 'RSV-PRICE-001',
            'service' => 'Hotel',
            'status' => 'Active',
        ]);
        Orders::create([
            'id' => 1,
            'orderno' => 'HTL-INVOICE-001',
            'confirmation_order' => '',
            'user_id' => 10,
            'sales_agent' => 10,
            'rsv_id' => $reservation->id,
            'name' => 'Invoice Agent',
            'email' => 'invoice@example.test',
            'servicename' => 'Server Hotel',
            'service' => 'Hotel',
            'service_id' => 1,
            'subservice' => 'Suite',
            'subservice_id' => 1,
            'status' => 'Approved',
            'checkin' => '2026-08-01',
            'checkout' => '2026-08-03',
            'duration' => 2,
            'number_of_room' => 2,
            'number_of_guests' => 4,
            'number_of_guests_room' => json_encode([2, 2]),
            'guest_detail' => json_encode(['Room 1', 'Room 2']),
            'special_day' => json_encode([null, null]),
            'special_date' => json_encode([null, null]),
            'extra_bed' => json_encode(['No', 'No']),
            'extra_bed_id' => json_encode([null, null]),
            'extra_bed_price' => json_encode([0, 0]),
            'promotion_disc' => json_encode([]),
            'normal_price' => 480,
            'price_total' => 460,
            'airport_shuttle_price' => 0,
            'final_price' => 430,
            'usd_rate' => 15000,
            'cny_rate' => 2000,
            'twd_rate' => 500,
        ]);
        $this->seedNormalRates('2026-08-01', '2026-08-02', 9999999, 999, 0);

        $pdf = Mockery::mock(\Barryvdh\DomPDF\PDF::class);
        $pdf->shouldReceive('save')->twice();
        \PDF::shouldReceive('loadView')->twice()->andReturn($pdf);

        $this->actingAs($admin)
            ->from('/orders-admin-1')
            ->put('/fgenerate-invoice-1', ['currency' => 1, 'bank' => 1])
            ->assertRedirect('/orders-admin-1');

        $invoice = InvoiceAdmin::where('rsv_id', $reservation->id)->first();
        $this->assertNotNull($invoice);
        $this->assertSame('430', (string) $invoice->total_usd);
        $this->assertSame('430', (string) $invoice->balance);
        $this->assertSame('6450000', (string) $invoice->total_idr);
    }

    private function normalHotelPayload(array $overrides = []): array
    {
        return array_merge([
            'terms_accepted' => '1',
            'orderno' => 'HTL-PRICE-001',
            'hotel_id' => 1,
            'room_id' => 1,
            'user_id' => 10,
            'request_quotation' => 'No',
            'service' => 'Hotel',
            'duration' => 2,
            'number_of_guests' => [2, 2],
            'guest_detail' => ['Room 1', 'Room 2'],
            'special_day' => [null, null],
            'special_date' => [null, null],
            'extra_bed_id' => [null, null],
            'arrival_time' => null,
            'arrival_flight' => null,
            'departure_time' => null,
            'departure_flight' => null,
            'airport_shuttle_in' => null,
            'airport_shuttle_out' => null,
            'note' => 'Pricing regression',
            'var_normal_price' => 1,
            'var_kick_back_total' => 1,
            'var_kick_back_per_room' => 1,
            'var_promotions_discount' => 1,
            'promotions_id' => '[]',
        ], $overrides);
    }

    private function promoHotelPayload(array $overrides = []): array
    {
        return array_merge($this->normalHotelPayload([
            'orderno' => 'HPP-PRICE-001',
            'service' => 'Hotel Promo',
            'hotel_id' => null,
            'promo_id' => json_encode([1]),
            'price_list' => json_encode([1]),
            'promo_price' => 1,
            'var_promo_price' => 1,
        ]), $overrides);
    }

    private function packageHotelPayload(array $overrides = []): array
    {
        return array_merge($this->normalHotelPayload([
            'orderno' => 'HPA-PRICE-001',
            'service' => 'Hotel Package',
            'hotel_id' => null,
            'package_name' => 'Package One',
            'final_price' => 1,
        ]), $overrides);
    }

    private function seedNormalRates(string $startDate, string $endDate, int $contractRate, int $markup, int $kickBack): void
    {
        HotelPrice::create([
            'hotels_id' => 1,
            'rooms_id' => 1,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'contract_rate' => $contractRate,
            'markup' => $markup,
            'kick_back' => $kickBack,
            'author' => 1,
        ]);
    }

    private function seedPromo(int $hotelId, int $roomId, array $overrides = []): HotelPromo
    {
        return HotelPromo::create(array_merge([
            'hotels_id' => $hotelId,
            'rooms_id' => $roomId,
            'name' => 'Promo One',
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
            'benefits' => 'Promo benefit',
            'include' => 'Promo include',
            'additional_info' => 'Promo info',
        ], $overrides));
    }

    private function seedPackage(int $hotelId, int $roomId, array $overrides = []): HotelPackage
    {
        return HotelPackage::create(array_merge([
            'hotels_id' => $hotelId,
            'rooms_id' => $roomId,
            'name' => 'Package One',
            'duration' => 2,
            'stay_period_start' => '2026-08-01',
            'stay_period_end' => '2026-08-31',
            'contract_rate' => 3000000,
            'markup' => 30,
            'status' => 'Active',
            'author' => 1,
            'benefits' => 'Package benefit',
            'include' => 'Package include',
            'additional_info' => 'Package info',
        ], $overrides));
    }

    private function seedExtraBed(int $hotelId, int $roomId, int $contractRate, int $markup): void
    {
        DB::table('extra_beds')->insert([
            'id' => 1,
            'name' => 'Extra Bed',
            'hotels_id' => $hotelId,
            'rooms_id' => $roomId,
            'type' => 'Adult',
            'contract_rate' => $contractRate,
            'markup' => $markup,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedAirportShuttle(int $transportId, int $priceId, int $contractRate, int $markup): void
    {
        DB::table('transports')->insert([
            'id' => $transportId,
            'name' => 'Airport Van',
            'brand' => 'Toyota',
            'capacity' => 4,
            'status' => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('transport_prices')->insert([
            'id' => $priceId,
            'transports_id' => $transportId,
            'type' => 'Airport Shuttle',
            'duration' => 2,
            'contract_rate' => $contractRate,
            'markup' => $markup,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertExistingAccommodationOrder(array $overrides = []): int
    {
        return DB::table('orders')->insertGetId(array_merge([
            'orderno' => 'HTL-EXISTING',
            'service' => 'Hotel',
            'service_id' => 1,
            'subservice_id' => 1,
            'servicename' => 'Server Hotel',
            'subservice' => 'Suite',
            'user_id' => 99,
            'sales_agent' => 99,
            'name' => 'Existing Agent',
            'email' => 'existing@example.test',
            'checkin' => '2026-08-01',
            'checkout' => '2026-08-03',
            'number_of_room' => 1,
            'number_of_guests' => 2,
            'number_of_guests_room' => json_encode([2]),
            'guest_detail' => json_encode(['Existing guest']),
            'duration' => 2,
            'price_pax' => 240,
            'normal_price' => 240,
            'price_total' => 240,
            'final_price' => 240,
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
    }

    private function usingSafeSqliteMemoryDatabase(): bool
    {
        return config('database.default') === 'sqlite'
            && config('database.connections.sqlite.database') === ':memory:';
    }

    private function actingUser(int $id, string $position): User
    {
        DB::table('users')->updateOrInsert(
            ['id' => $id],
            [
                'name' => 'Pricing User',
                'email' => "pricing{$id}@example.test",
                'password' => bcrypt('password'),
                'type' => 'admin',
                'position' => $position,
                'status' => 'Active',
                'is_approved' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return User::findOrFail($id);
    }

    private function prepareSchema(): void
    {
        foreach ([
            'order_logs',
            'user_logs',
            'action_logs',
            'payment_confirmations',
            'invoice_admins',
            'reservations',
            'bank_accounts',
            'business_profiles',
            'guests',
            'guides',
            'drivers',
            'villas',
            'airport_shuttles',
            'optional_rate_orders',
            'optional_rates',
            'transport_prices',
            'transports',
            'extra_beds',
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
            'users',
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
            $table->timestamp('session_id')->nullable();
            $table->timestamps();
        });

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
            $table->integer('airport_duration')->nullable();
            $table->integer('airport_distance')->nullable();
            $table->longText('cancellation_policy')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotels_id')->nullable();
            $table->string('rooms')->nullable();
            $table->integer('capacity')->nullable();
            $table->integer('capacity_adult')->nullable();
            $table->integer('capacity_child')->nullable();
            $table->unsignedInteger('inventory')->nullable();
            $table->longText('include')->nullable();
            $table->longText('additional_info')->nullable();
            $table->string('status')->nullable();
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
            $table->string('promotion_type')->nullable();
            $table->longText('quotes')->nullable();
            $table->string('booking_code')->nullable();
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
            $table->string('booking_code')->nullable();
            $table->longText('benefits')->nullable();
            $table->longText('include')->nullable();
            $table->longText('additional_info')->nullable();
            $table->string('status');
            $table->integer('author');
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
            $table->unsignedBigInteger('service_id')->nullable();
            $table->string('subservice')->nullable();
            $table->unsignedBigInteger('subservice_id')->nullable();
            $table->date('checkin')->nullable();
            $table->date('checkout')->nullable();
            $table->text('location')->nullable();
            $table->integer('number_of_guests')->nullable();
            $table->text('number_of_guests_room')->nullable();
            $table->text('guest_detail')->nullable();
            $table->text('request_quotation')->nullable();
            $table->text('special_day')->nullable();
            $table->text('special_date')->nullable();
            $table->text('extra_bed')->nullable();
            $table->text('capacity')->nullable();
            $table->text('benefits')->nullable();
            $table->text('include')->nullable();
            $table->text('include_traditional')->nullable();
            $table->text('include_simplified')->nullable();
            $table->text('exclude')->nullable();
            $table->text('exclude_traditional')->nullable();
            $table->text('exclude_simplified')->nullable();
            $table->text('additional_info')->nullable();
            $table->integer('number_of_room')->nullable();
            $table->string('duration')->nullable();
            $table->text('price_pax')->nullable();
            $table->text('normal_price')->nullable();
            $table->text('optional_price')->nullable();
            $table->text('kick_back')->nullable();
            $table->text('kick_back_per_pax')->nullable();
            $table->text('extra_bed_id')->nullable();
            $table->text('extra_bed_price')->nullable();
            $table->text('extra_bed_total_price')->nullable();
            $table->text('price_total')->nullable();
            $table->text('bookingcode')->nullable();
            $table->text('bookingcode_disc')->nullable();
            $table->text('promotion')->nullable();
            $table->text('promotion_disc')->nullable();
            $table->text('airport_shuttle_price')->nullable();
            $table->text('final_price')->nullable();
            $table->string('usd_rate')->nullable();
            $table->string('cny_rate')->nullable();
            $table->string('twd_rate')->nullable();
            $table->text('package_name')->nullable();
            $table->text('promo_id')->nullable();
            $table->text('promo_name')->nullable();
            $table->string('book_period_start')->nullable();
            $table->string('book_period_end')->nullable();
            $table->string('period_start')->nullable();
            $table->string('period_end')->nullable();
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->unsignedBigInteger('pickup_name')->nullable();
            $table->unsignedBigInteger('guide_id')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->string('arrival_flight')->nullable();
            $table->string('arrival_time')->nullable();
            $table->text('airport_shuttle_in')->nullable();
            $table->string('departure_flight')->nullable();
            $table->string('departure_time')->nullable();
            $table->text('airport_shuttle_out')->nullable();
            $table->text('note')->nullable();
            $table->longText('cancellation_policy')->nullable();
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('rsv_no')->nullable();
            $table->string('service')->nullable();
            $table->unsignedBigInteger('agn_id')->nullable();
            $table->unsignedBigInteger('adm_id')->nullable();
            $table->date('checkin')->nullable();
            $table->date('checkout')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_admins', function (Blueprint $table) {
            $table->id();
            $table->string('inv_no')->nullable();
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->dateTime('inv_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->decimal('total_usd', 14, 2)->default(0);
            $table->decimal('total_idr', 14, 2)->default(0);
            $table->decimal('total_cny', 14, 2)->default(0);
            $table->decimal('total_twd', 14, 2)->default(0);
            $table->decimal('rate_usd', 14, 2)->default(1);
            $table->decimal('sell_usd', 14, 2)->default(1);
            $table->decimal('rate_cny', 14, 2)->default(1);
            $table->decimal('sell_cny', 14, 2)->default(1);
            $table->decimal('rate_twd', 14, 2)->default(1);
            $table->decimal('sell_twd', 14, 2)->default(1);
            $table->decimal('balance', 14, 2)->default(0);
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_confirmations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inv_id')->nullable();
            $table->unsignedBigInteger('kurs_id')->nullable();
            $table->string('receipt_img')->nullable();
            $table->date('payment_date')->nullable();
            $table->text('note')->nullable();
            $table->decimal('amount', 14, 2)->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('extra_beds', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('hotels_id')->nullable();
            $table->unsignedBigInteger('rooms_id')->nullable();
            $table->string('type')->nullable();
            $table->integer('contract_rate')->nullable();
            $table->integer('markup')->nullable();
            $table->timestamps();
        });

        Schema::create('optional_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotels_id')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->date('active_date')->nullable();
            $table->integer('contract_rate')->nullable();
            $table->integer('markup')->nullable();
            $table->timestamps();
        });

        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('brand')->nullable();
            $table->string('status')->nullable();
            $table->integer('capacity')->nullable();
            $table->timestamps();
        });

        Schema::create('transport_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transports_id')->nullable();
            $table->string('type')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('contract_rate')->nullable();
            $table->integer('markup')->nullable();
            $table->timestamps();
        });

        Schema::create('optional_rate_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('service')->nullable();
            $table->unsignedBigInteger('optional_rate_id')->nullable();
            $table->integer('number_of_guest')->nullable();
            $table->date('service_date')->nullable();
            $table->decimal('price_pax', 14, 2)->nullable();
            $table->decimal('price_total', 14, 2)->nullable();
            $table->boolean('mandatory')->default(false);
            $table->timestamps();
        });

        Schema::create('airport_shuttles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->dateTime('date')->nullable();
            $table->string('flight_number')->nullable();
            $table->integer('number_of_guests')->nullable();
            $table->unsignedBigInteger('transport_id')->nullable();
            $table->unsignedBigInteger('price_id')->nullable();
            $table->string('src')->nullable();
            $table->string('dst')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('distance')->nullable();
            $table->string('price')->nullable();
            $table->string('nav')->nullable();
            $table->timestamps();
        });

        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank')->nullable();
            $table->string('currency')->nullable();
            $table->string('name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->timestamps();
        });

        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('name')->nullable();
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

        Schema::create('villas', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
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

        Schema::create('action_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->string('action');
            $table->string('service');
            $table->integer('service_id');
            $table->string('page');
            $table->string('user_ip');
            $table->string('initial_state')->nullable();
            $table->string('final_state')->nullable();
            $table->text('action_note')->nullable();
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
    }

    private function writeInventoryCsv(array $rows): string
    {
        $path = storage_path('framework/testing/accommodation-room-inventory-import-'.uniqid().'.csv');
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $handle = fopen($path, 'wb');
        fputcsv($handle, [
            'hotel_id',
            'hotel_name',
            'room_id',
            'room_name',
            'status',
            'current_inventory',
            'new_inventory',
        ]);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return $path;
    }

    private function seedReferenceRows(): void
    {
        foreach ([['USD', 15000], ['CNY', 2000], ['TWD', 500]] as $index => [$name, $rate]) {
            DB::table('usd_rates')->insert([
                'id' => $index + 1,
                'name' => $name,
                'rate' => $rate,
                'sell' => $rate,
                'buy' => $rate,
                'difference' => 0,
            ]);
        }

        DB::table('taxes')->insert(['id' => 1, 'tax' => 0]);
        DB::table('business_profiles')->insert([
            'id' => 1,
            'name' => 'Bali Kami Tour',
            'email' => 'reservation@example.test',
            'website' => 'example.test',
        ]);
        DB::table('bank_accounts')->insert([
            'id' => 1,
            'bank' => 'Test Bank',
            'currency' => 'USD',
            'name' => 'Bali Kami Tour',
            'account_name' => 'Bali Kami Tour',
            'account_number' => '0001',
        ]);
        DB::table('hotels')->insert([
            'id' => 1,
            'name' => 'Server Hotel',
            'code' => 'SERVER',
            'region' => 'Bali',
            'airport_duration' => 2,
            'airport_distance' => 20,
            'cancellation_policy' => 'Safe cancellation policy',
            'status' => 'Active',
        ]);
        DB::table('hotels')->insert([
            'id' => 2,
            'name' => 'Other Hotel',
            'code' => 'OTHER',
            'region' => 'Bali',
            'airport_duration' => 2,
            'airport_distance' => 20,
            'cancellation_policy' => 'Other policy',
            'status' => 'Active',
        ]);
        DB::table('hotel_rooms')->insert([
            'id' => 1,
            'hotels_id' => 1,
            'rooms' => 'Suite',
            'capacity' => 4,
            'capacity_adult' => 2,
            'capacity_child' => 2,
            'inventory' => 3,
            'include' => 'Breakfast',
            'additional_info' => 'Info',
            'status' => 'Active',
        ]);
        DB::table('hotel_rooms')->insert([
            'id' => 2,
            'hotels_id' => 2,
            'rooms' => 'Other Suite',
            'capacity' => 4,
            'capacity_adult' => 2,
            'capacity_child' => 2,
            'inventory' => 3,
            'include' => 'Breakfast',
            'additional_info' => 'Info',
            'status' => 'Active',
        ]);
        DB::table('booking_codes')->insert([
            'id' => 1,
            'name' => 'Save 30',
            'code' => 'SAVE30',
            'discounts' => 30,
            'amount' => 10,
            'used' => 0,
            'author' => 1,
            'expired_date' => '2026-12-31',
            'status' => 'Active',
        ]);
    }
}
