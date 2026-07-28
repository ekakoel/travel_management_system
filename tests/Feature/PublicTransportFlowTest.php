<?php

namespace Tests\Feature;

use App\Http\Middleware\LogActivityMiddleware;
use App\Http\Middleware\TrackWebsiteVisit;
use App\Http\Middleware\UserActivity;
use App\Mail\ReservationMail;
use App\Models\InvoiceAdmin;
use App\Models\Orders;
use App\Models\PaymentConfirmation;
use App\Models\Reservation;
use App\Models\User;
use App\Services\AccommodationFinancialFileService;
use App\Services\TransportOrderLifecycleService;
use App\Services\TransportAvailabilityService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicTransportFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') {
            $this->markTestSkipped('Public Transport flow tests require sqlite :memory: to avoid touching active data.');
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

    public function test_public_transport_order_uses_authoritative_price_and_creates_pending_reservation(): void
    {
        $user = $this->actingUser();

        $this->actingAs($user)
            ->withSession([
                'bookingcode' => [
                    'code' => 'TRN10',
                    'discounts' => 10,
                ],
            ])
            ->post(route('func.create.order-transport', ['id' => 1]), $this->transportPayload([
                'normal_price' => 1,
                'final_price' => 1,
                'submission_token' => 'transport-create-1',
            ]))
            ->assertRedirect(route('view.detail-order-transport', ['id' => 1]));

        $order = DB::table('orders')->where('service', 'Transport')->first();

        $this->assertSame('Pending', $order->status);
        $this->assertSame('Transport', $order->service);
        $this->assertSame('Daily Rent', $order->subservice);
        $this->assertSame('240', (string) $order->price_pax);
        $this->assertSame('480', (string) $order->normal_price);
        $this->assertSame('465', (string) $order->final_price);
        $this->assertNotNull($order->rsv_id);

        $this->assertDatabaseHas('reservations', [
            'id' => $order->rsv_id,
            'rsv_no' => $order->orderno,
            'service' => 'Transport',
            'status' => 'Pending',
        ]);
        $this->assertSame(1, (int) DB::table('booking_codes')->where('code', 'TRN10')->value('used'));
    }

    public function test_transport_inventory_three_allows_three_overlapping_bookings_but_rejects_fourth(): void
    {
        DB::table('transports')->where('id', 1)->update(['inventory' => 3, 'capacity' => 1]);

        foreach ([10, 11] as $index => $userId) {
            $this->actingAs($this->actingUser($userId, 'agent'.$userId.'@example.test'))
                ->post(route('func.create.order-transport', ['id' => 1]), $this->transportPayload([
                    'submission_token' => 'inventory-'.$index,
                ]))
                ->assertRedirect();
        }

        $this->assertSame(1, app(TransportAvailabilityService::class)->remainingInventory(1, '2026-08-01 10:00:00', '2026-08-03 10:00:00'));

        $this->actingAs($this->actingUser(13, 'agent13@example.test'))
            ->from('/transportation-1')
            ->post(route('func.create.order-transport', ['id' => 1]), $this->transportPayload([
                'flight_date' => '2026-08-01 12:00:00',
                'submission_token' => 'inventory-fourth',
            ]))
            ->assertRedirect();

        $this->actingAs($this->actingUser(14, 'agent14@example.test'))
            ->from('/transportation-1')
            ->post(route('func.create.order-transport', ['id' => 1]), $this->transportPayload([
                'flight_date' => '2026-08-01 14:00:00',
                'submission_token' => 'inventory-fourth-rejected',
            ]))
            ->assertSessionHasErrors('transport_id');

        $this->assertSame(3, DB::table('orders')->where('service', 'Transport')->count());
    }

    public function test_transport_passenger_capacity_is_separate_from_inventory(): void
    {
        DB::table('transports')->where('id', 1)->update(['inventory' => 3, 'capacity' => 1]);

        $this->actingAs($this->actingUser())
            ->from('/transportation-1')
            ->post(route('func.create.order-transport', ['id' => 1]), $this->transportPayload([
                'guest_entries' => [
                    ['name' => 'Guest One', 'age' => 'Adult', 'sex' => 'Male', 'phone' => '08123'],
                    ['name' => 'Guest Two', 'age' => 'Adult', 'sex' => 'Female', 'phone' => '08124'],
                ],
            ]))
            ->assertSessionHasErrors('guest_entries');

        $this->assertSame(3, app(TransportAvailabilityService::class)->remainingInventory(1, '2026-08-01 10:00:00', '2026-08-03 10:00:00'));
    }

    public function test_terminal_transport_order_releases_inventory_and_non_overlap_does_not_block(): void
    {
        DB::table('transports')->where('id', 1)->update(['inventory' => 1]);
        $owner = $this->actingUser(10);

        $this->insertApprovedTransportOrder($owner, [
            'orderno' => 'TRN-CANCELED',
            'status' => 'Canceled',
            'checkin' => '2026-08-01 10:00:00',
            'checkout' => '2026-08-03 10:00:00',
        ]);

        $this->actingAs($owner)
            ->post(route('func.create.order-transport', ['id' => 1]), $this->transportPayload([
                'submission_token' => 'after-cancel',
            ]))
            ->assertRedirect();

        $this->assertSame(1, DB::table('orders')->where('status', 'Pending')->count());

        DB::table('orders')->delete();
        DB::table('reservations')->delete();
        DB::table('guests')->delete();
        DB::table('order_logs')->delete();
        DB::table('user_logs')->delete();

        $this->insertApprovedTransportOrder($owner, [
            'orderno' => 'TRN-NON-OVERLAP',
            'status' => 'Pending',
            'checkin' => '2026-08-03 10:00:00',
            'checkout' => '2026-08-04 10:00:00',
        ]);

        $this->actingAs($owner)
            ->post(route('func.create.order-transport', ['id' => 1]), $this->transportPayload([
                'flight_date' => '2026-08-01 10:00:00',
                'submission_token' => 'non-overlap',
            ]))
            ->assertRedirect();

        $this->assertSame(2, DB::table('orders')->where('service', 'Transport')->count());
    }

    public function test_transport_inventory_revalidation_prevents_second_booking_when_one_unit_remains(): void
    {
        DB::table('transports')->where('id', 1)->update(['inventory' => 1]);
        $owner = $this->actingUser(10);

        $this->actingAs($owner)
            ->post(route('func.create.order-transport', ['id' => 1]), $this->transportPayload([
                'submission_token' => 'single-unit-first',
            ]))
            ->assertRedirect();

        $this->actingAs($this->actingUser(11, 'other11@example.test'))
            ->from('/transportation-1')
            ->post(route('func.create.order-transport', ['id' => 1]), $this->transportPayload([
                'submission_token' => 'single-unit-second',
            ]))
            ->assertSessionHasErrors('transport_id');

        $this->assertSame(1, DB::table('orders')->where('service', 'Transport')->count());
    }

    public function test_transport_zero_inventory_cannot_be_booked(): void
    {
        DB::table('transports')->where('id', 1)->update(['inventory' => 0]);

        $this->actingAs($this->actingUser())
            ->from('/transportation-1')
            ->post(route('func.create.order-transport', ['id' => 1]), $this->transportPayload())
            ->assertSessionHasErrors('transport_id');

        $this->assertSame(0, DB::table('orders')->count());
    }

    public function test_transport_create_rejects_price_from_other_transport(): void
    {
        $user = $this->actingUser();

        $this->actingAs($user)
            ->from('/transportation-1')
            ->post(route('func.create.order-transport', ['id' => 2]), $this->transportPayload([
                'transport_id' => 1,
            ]))
            ->assertSessionHasErrors('transport_id');

        $this->assertSame(0, DB::table('orders')->count());
        Mail::assertNothingSent();
    }

    public function test_transport_create_uses_submitted_price_id_when_modal_action_is_stale(): void
    {
        DB::table('transport_prices')->insert([
            'id' => 3,
            'transports_id' => 1,
            'name' => 'Daily Alternate',
            'type' => 'Daily Rent',
            'src' => 'Villa',
            'dst' => 'Harbor',
            'duration' => 2,
            'contract_rate' => 900000,
            'markup' => 10,
            'extra_time' => 0,
        ]);

        $user = $this->actingUser();

        $this->actingAs($user)
            ->post(route('func.create.order-transport', ['id' => 1]), $this->transportPayload([
                'transport_price_id' => 3,
                'pickup_location' => 'Villa',
                'dropoff_location' => 'Harbor',
                'submission_token' => 'stale-action-rate',
            ]))
            ->assertRedirect(route('view.detail-order-transport', ['id' => 1]));

        $order = DB::table('orders')->where('service', 'Transport')->first();
        $this->assertSame(3, (int) $order->subservice_id);
        $this->assertSame('Daily Alternate', DB::table('transport_prices')->where('id', $order->subservice_id)->value('name'));
    }

    public function test_transport_create_rejects_past_service_date(): void
    {
        $user = $this->actingUser();

        $this->actingAs($user)
            ->from('/transportation-1')
            ->post(route('func.create.order-transport', ['id' => 1]), $this->transportPayload([
                'flight_date' => '2026-07-26 10:00:00',
            ]))
            ->assertSessionHasErrors('flight_date');

        $this->assertSame(0, DB::table('orders')->count());
        Mail::assertNothingSent();
    }

    public function test_transport_duplicate_submission_token_returns_existing_order(): void
    {
        $user = $this->actingUser();
        $payload = $this->transportPayload(['submission_token' => 'duplicate-token']);

        $this->actingAs($user)->post(route('func.create.order-transport', ['id' => 1]), $payload);
        $this->actingAs($user)
            ->post(route('func.create.order-transport', ['id' => 1]), $payload)
            ->assertRedirect(route('view.detail-order-transport', ['id' => 1]));

        $this->assertSame(1, DB::table('orders')->where('service', 'Transport')->count());
        $this->assertSame(1, DB::table('reservations')->count());
    }

    public function test_transport_customer_payment_upload_stores_private_receipt_and_guarded_route_enforces_owner(): void
    {
        $owner = $this->actingUser(10);
        $nonOwner = $this->actingUser(11, 'other@example.test');
        $order = $this->insertApprovedTransportOrder($owner);
        $invoice = InvoiceAdmin::create([
            'rsv_id' => $order->rsv_id,
            'inv_no' => 'TRN-INV-001',
            'due_date' => now()->addDay(),
            'balance' => 240,
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
            ->assertRedirect(route('view.detail-order-transport', ['id' => $order->id]));

        $payment = PaymentConfirmation::first();

        $this->assertNotNull($payment);
        $this->assertSame(1, (int) $payment->kurs_id);
        $this->assertStringStartsWith('transport/payments/'.$order->id.'/', $payment->receipt_img);
        Storage::disk('private')->assertExists($payment->receipt_img);

        $this->actingAs($nonOwner)
            ->get(route('orders.transport.payments.receipt', ['order' => $order->id, 'payment' => $payment->id]))
            ->assertNotFound();

        $response = $this->actingAs($owner)
            ->get(route('orders.transport.payments.receipt', ['order' => $order->id, 'payment' => $payment->id]))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));

        Storage::disk('private')->put(
            app(AccommodationFinancialFileService::class)->privateInvoicePath($order, $invoice, 'en'),
            "%PDF-1.4\n% transport invoice\n"
        );

        $invoiceResponse = $this->actingAs($owner)
            ->get(route('orders.accommodation.invoice.preview', ['order' => $order->id, 'locale' => 'en']))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->assertStringContainsString('private', $invoiceResponse->headers->get('Cache-Control'));

        $this->actingAs($nonOwner)
            ->get(route('orders.accommodation.invoice.preview', ['order' => $order->id, 'locale' => 'en']))
            ->assertNotFound();
    }

    public function test_transport_completion_uses_completed_at_without_spk_dependency_and_history_scope(): void
    {
        $owner = $this->actingUser();
        $order = $this->insertApprovedTransportOrder($owner, [
            'status' => 'Paid',
            'checkin' => '2026-07-20 10:00:00',
            'checkout' => '2026-07-20 12:00:00',
        ]);

        $completed = app(TransportOrderLifecycleService::class)->completeEligiblePaidOrders(null, now());

        $this->assertSame(1, $completed);
        $this->assertNotNull(DB::table('orders')->where('id', $order->id)->value('completed_at'));
        $this->assertSame('Completed', DB::table('reservations')->where('id', $order->rsv_id)->value('status'));
        $this->assertDatabaseHas('order_logs', [
            'order_id' => $order->id,
            'action' => 'Complete Transport Order',
        ]);

        $this->assertSame(0, app(TransportOrderLifecycleService::class)->completeEligiblePaidOrders(null, now()));

        $current = app(TransportOrderLifecycleService::class)
            ->applyTransportCurrentScope(Orders::query(), now())
            ->count();
        $history = app(TransportOrderLifecycleService::class)
            ->applyTransportHistoryScope(Orders::query(), now())
            ->count();

        $this->assertSame(0, $current);
        $this->assertSame(1, $history);
    }

    public function test_transport_inventory_audit_export_and_import_commands(): void
    {
        DB::table('transports')->where('id', 1)->update(['inventory' => null]);
        DB::table('transports')->where('id', 2)->update(['inventory' => 0, 'status' => 'Draft']);

        $this->artisan('transport:audit-inventory', ['--active-only' => true])
            ->expectsOutputToContain('Inventory null: 1')
            ->expectsOutputToContain('Fallback usage: 1')
            ->assertSuccessful();

        $this->artisan('transport:export-inventory', ['path' => 'testing/transport-inventory.csv'])
            ->assertSuccessful();

        $csvPath = storage_path('app/testing/transport-inventory-import.csv');
        if (! is_dir(dirname($csvPath))) {
            mkdir(dirname($csvPath), 0755, true);
        }
        file_put_contents($csvPath, implode("\n", [
            'transport_id,transport_name,type,status,passenger_capacity,current_inventory,new_inventory',
            '1,Hiace,Car,Active,4,,2',
            '2,Airport Car,Car,Draft,4,0,0',
        ]));

        $this->artisan('transport:import-inventory', ['path' => $csvPath, '--dry-run' => true])
            ->expectsOutputToContain('Changed: 1')
            ->expectsOutputToContain('Dry run complete. No data changed.')
            ->assertSuccessful();
        $this->assertNull(DB::table('transports')->where('id', 1)->value('inventory'));

        $this->artisan('transport:import-inventory', ['path' => $csvPath])
            ->expectsOutputToContain('Import complete.')
            ->assertSuccessful();
        $this->assertSame(2, (int) DB::table('transports')->where('id', 1)->value('inventory'));
        $this->assertSame(0, (int) DB::table('transports')->where('id', 2)->value('inventory'));

        file_put_contents($csvPath, implode("\n", [
            'transport_id,transport_name,type,status,passenger_capacity,current_inventory,new_inventory',
            '1,Hiace,Car,Active,4,2,2',
            '1,Hiace,Car,Active,4,2,3',
        ]));

        $this->artisan('transport:import-inventory', ['path' => $csvPath, '--dry-run' => true])
            ->expectsOutputToContain('duplicate transport_id 1')
            ->assertFailed();
    }

    public function test_transport_admin_validation_requires_inventory_for_active_transport(): void
    {
        $request = \App\Http\Requests\Backend\Operations\Transports\UpdateTransportAdminRequest::create('/fake', 'POST', [
            'name' => 'Hiace',
            'type' => 'Daily Rent',
            'brand' => 'Toyota',
            'capacity' => 4,
            'inventory' => 0,
            'description' => 'Description',
            'include' => 'Include',
            'status' => 'Active',
            'author' => 10,
        ]);
        $admin = $this->actingUser();
        $admin->type = 'admin';
        $admin->position = 'developer';
        $admin->save();
        $request->setContainer(app())->setRedirector(app('redirect'));
        $request->setUserResolver(fn () => $admin);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $request->validateResolved();
    }

    private function transportPayload(array $overrides = []): array
    {
        return array_merge([
            'transport_id' => 1,
            'transport_booking_flow' => 'detail_modal',
            'duration' => 2,
            'terms_accepted' => '1',
            'flight_date' => '2026-08-01 10:00:00',
            'pickup_location' => 'Hotel',
            'dropoff_location' => 'Airport',
            'guest_entries' => [
                ['name' => 'Guest One', 'age' => 'Adult', 'sex' => 'Male', 'phone' => '08123'],
            ],
            'note' => 'Transport test',
        ], $overrides);
    }

    private function actingUser(int $id = 10, string $email = 'agent@example.test'): User
    {
        $user = User::create([
            'id' => $id,
            'name' => 'Transport Agent '.$id,
            'email' => $email,
            'password' => 'secret',
            'type' => 'user',
            'position' => 'agent',
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
            'code' => 'TRN',
        ]);

        return $user;
    }

    private function insertApprovedTransportOrder(User $owner, array $overrides = []): Orders
    {
        $reservation = Reservation::create([
            'rsv_no' => $overrides['orderno'] ?? 'TRN-APPROVED',
            'service' => 'Transport',
            'agn_id' => $owner->id,
            'adm_id' => $owner->id,
            'checkin' => $overrides['checkin'] ?? '2026-08-01 10:00:00',
            'checkout' => $overrides['checkout'] ?? '2026-08-01 12:00:00',
            'status' => 'Active',
        ]);

        return Orders::create(array_merge([
            'user_id' => $owner->id,
            'sales_agent' => $owner->id,
            'name' => $owner->name,
            'email' => $owner->email,
            'orderno' => 'TRN-APPROVED',
            'service' => 'Transport',
            'service_id' => 1,
            'service_type' => 'Daily Rent',
            'servicename' => 'Toyota Hiace',
            'subservice' => 'Daily Rent',
            'subservice_id' => 1,
            'price_id' => 1,
            'checkin' => '2026-08-01 10:00:00',
            'checkout' => '2026-08-01 12:00:00',
            'duration' => 2,
            'number_of_guests' => 1,
            'capacity' => 4,
            'price_pax' => 240,
            'normal_price' => 240,
            'price_total' => 240,
            'final_price' => 240,
            'status' => 'Approved',
            'rsv_id' => $reservation->id,
        ], $overrides));
    }

    private function prepareSchema(): void
    {
        foreach ([
            'payment_confirmations',
            'invoice_admins',
            'reservations',
            'guests',
            'order_logs',
            'user_logs',
            'orders',
            'booking_codes',
            'promotions',
            'transport_prices',
            'transports',
            'bank_accounts',
            'usd_rates',
            'taxes',
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

        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('type')->nullable();
            $table->string('brand')->nullable();
            $table->integer('duration')->nullable();
            $table->longText('include')->nullable();
            $table->longText('additional_info')->nullable();
            $table->longText('cancellation_policy')->nullable();
            $table->integer('capacity')->nullable();
            $table->unsignedInteger('inventory')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('transport_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transports_id')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('src')->nullable();
            $table->string('dst')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('contract_rate')->default(0);
            $table->integer('markup')->default(0);
            $table->integer('extra_time')->nullable();
            $table->timestamps();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('discounts')->default(0);
            $table->date('periode_start');
            $table->date('periode_end');
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('booking_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->integer('discounts')->default(0);
            $table->integer('used')->default(0);
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
            $table->unsignedBigInteger('service_id')->nullable();
            $table->string('service_type')->nullable();
            $table->string('servicename')->nullable();
            $table->string('subservice')->nullable();
            $table->unsignedBigInteger('subservice_id')->nullable();
            $table->integer('number_of_guests')->nullable();
            $table->text('guest_detail')->nullable();
            $table->integer('extra_time')->nullable();
            $table->unsignedBigInteger('price_id')->nullable();
            $table->dateTime('checkin')->nullable();
            $table->dateTime('checkout')->nullable();
            $table->string('src')->nullable();
            $table->string('dst')->nullable();
            $table->unsignedBigInteger('sales_agent')->nullable();
            $table->unsignedBigInteger('pickup_name')->nullable();
            $table->string('pickup_phone')->nullable();
            $table->unsignedBigInteger('bookingcode')->nullable();
            $table->decimal('bookingcode_disc', 14, 2)->default(0);
            $table->integer('capacity')->nullable();
            $table->longText('include')->nullable();
            $table->longText('additional_info')->nullable();
            $table->longText('cancellation_policy')->nullable();
            $table->integer('duration')->nullable();
            $table->decimal('price_total', 14, 2)->default(0);
            $table->text('promotion')->nullable();
            $table->text('promotion_disc')->nullable();
            $table->decimal('final_price', 14, 2)->default(0);
            $table->decimal('usd_rate', 14, 2)->nullable();
            $table->decimal('cny_rate', 14, 2)->nullable();
            $table->decimal('twd_rate', 14, 2)->nullable();
            $table->decimal('normal_price', 14, 2)->default(0);
            $table->decimal('price_pax', 14, 2)->default(0);
            $table->string('arrival_flight')->nullable();
            $table->dateTime('arrival_time')->nullable();
            $table->unsignedBigInteger('airport_shuttle_in')->nullable();
            $table->string('departure_flight')->nullable();
            $table->dateTime('departure_time')->nullable();
            $table->unsignedBigInteger('airport_shuttle_out')->nullable();
            $table->string('pickup_location')->nullable();
            $table->dateTime('pickup_date')->nullable();
            $table->dateTime('dropoff_date')->nullable();
            $table->string('dropoff_location')->nullable();
            $table->string('status')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
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

        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->string('name')->nullable();
            $table->string('age')->nullable();
            $table->string('sex')->nullable();
            $table->string('phone')->nullable();
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
            $table->string('pickup_location')->nullable();
            $table->string('dropoff_location')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_admins', function (Blueprint $table) {
            $table->id();
            $table->string('inv_no')->nullable();
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->dateTime('due_date')->nullable();
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
            DB::table('usd_rates')->insert(['id' => $index + 1, 'name' => $name, 'rate' => $rate, 'sell' => $rate]);
        }
        DB::table('bank_accounts')->insert(['id' => 1, 'bank' => 'Test Bank', 'currency' => 'USD']);
        DB::table('transports')->insert([
            'id' => 1,
            'name' => 'Hiace',
            'code' => 'HIACE',
            'type' => 'Car',
            'brand' => 'Toyota',
            'capacity' => 4,
            'inventory' => 3,
            'include' => 'Fuel',
            'additional_info' => 'Info',
            'cancellation_policy' => 'Policy',
            'status' => 'Active',
        ]);
        DB::table('transports')->insert([
            'id' => 2,
            'name' => 'Airport Car',
            'code' => 'AIR',
            'type' => 'Car',
            'brand' => 'Toyota',
            'capacity' => 4,
            'inventory' => 3,
            'status' => 'Active',
        ]);
        DB::table('transport_prices')->insert([
            'id' => 1,
            'transports_id' => 1,
            'name' => 'Daily',
            'type' => 'Daily Rent',
            'src' => 'Hotel',
            'dst' => 'Airport',
            'duration' => 2,
            'contract_rate' => 1500000,
            'markup' => 140,
            'extra_time' => 0,
        ]);
        DB::table('transport_prices')->insert([
            'id' => 2,
            'transports_id' => 2,
            'name' => 'Airport',
            'type' => 'Airport Shuttle',
            'src' => 'Airport',
            'dst' => 'Hotel',
            'duration' => 1,
            'contract_rate' => 750000,
            'markup' => 50,
            'extra_time' => 0,
        ]);
        DB::table('promotions')->insert([
            'id' => 1,
            'name' => 'Promo Five',
            'discounts' => 5,
            'periode_start' => '2026-07-01',
            'periode_end' => '2026-08-31',
            'status' => 'Active',
        ]);
        DB::table('booking_codes')->insert([
            'id' => 1,
            'code' => 'TRN10',
            'discounts' => 10,
            'used' => 0,
        ]);
    }
}
