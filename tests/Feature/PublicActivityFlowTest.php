<?php

namespace Tests\Feature;

use App\Http\Middleware\LogActivityMiddleware;
use App\Http\Middleware\TrackWebsiteVisit;
use App\Http\Middleware\UserActivity;
use App\Models\InvoiceAdmin;
use App\Models\Orders;
use App\Models\PaymentConfirmation;
use App\Models\Reservation;
use App\Models\User;
use App\Services\AccommodationFinancialFileService;
use App\Services\ActivityOrderLifecycleService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

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
            ->assertRedirect(route('view.detail-order-hotel', ['id' => 1]));

        $order = DB::table('orders')->where('service', 'Activity')->first();

        $this->assertSame('Pending', $order->status);
        $this->assertSame('Test Activity', $order->subservice);
        $this->assertSame('Partner A', $order->servicename);
        $this->assertSame('150', (string) $order->price_pax);
        $this->assertSame('300', (string) $order->price_total);
        $this->assertSame('300', (string) $order->final_price);
        $this->assertSame('Activity include snapshot', $order->include);
        $this->assertSame('Activity itinerary snapshot', $order->itinerary);
        $this->assertNotNull($order->rsv_id);
        $this->assertDatabaseHas('reservations', [
            'id' => $order->rsv_id,
            'service' => 'Activity',
            'status' => 'Pending',
        ]);
        $this->assertSame(2, DB::table('guests')->where('order_id', $order->id)->where('rsv_id', $order->rsv_id)->count());
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

        $this->get(route('view.activity-services'))
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
            ->assertRedirect(route('view.detail-order-hotel', ['id' => 1]));

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
            ->assertRedirect(route('view.detail-order-hotel', ['id' => $order->id]));

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

        $invoiceResponse = $this->actingAs($owner)
            ->get(route('orders.accommodation.invoice.preview', ['order' => $order->id, 'locale' => 'en']))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->assertStringContainsString('private', $invoiceResponse->headers->get('Cache-Control'));

        $this->actingAs($nonOwner)
            ->get(route('orders.accommodation.invoice.preview', ['order' => $order->id, 'locale' => 'en']))
            ->assertNotFound();
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
            'terms_accepted' => '1',
            'guests' => [
                self::guest('Leader Guest', '+628123456', true),
                self::guest('Second Guest'),
            ],
        ], $overrides);
    }

    private static function guest(string $name, string $phone = '+628777', bool $leader = false): array
    {
        return [
            'name' => $name,
            'phone' => $phone,
            'age' => 'Adult',
            'sex' => 'Male',
            'identification_type' => 'Passport',
            'identification_no' => 'ID-'.$name,
            'is_leader' => $leader ? '1' : '0',
        ];
    }

    private function actingUser(int $id = 10, string $email = 'activity@example.test', string $position = 'agent'): User
    {
        return User::create([
            'id' => $id,
            'name' => 'Activity Agent '.$id,
            'email' => $email,
            'password' => 'secret',
            'type' => 'user',
            'position' => $position,
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
            'code' => 'ACT',
        ]);
    }

    private function insertApprovedActivityOrder(User $owner, array $overrides = []): Orders
    {
        $reservation = Reservation::create([
            'rsv_no' => $overrides['orderno'] ?? 'ACT-APPROVED',
            'service' => 'Activity',
            'agn_id' => $owner->id,
            'adm_id' => $owner->id,
            'checkin' => $overrides['checkin'] ?? '2026-08-10 09:00:00',
            'checkout' => $overrides['checkout'] ?? '2026-08-10 11:00:00',
            'status' => 'Active',
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
        ], $overrides));
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
            'partners',
            'footer_links',
            'footer_settings',
            'promotions',
            'business_profiles',
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
            $table->timestamps();
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partners_id')->nullable();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('type')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->text('itinerary')->nullable();
            $table->string('duration')->nullable();
            $table->text('include')->nullable();
            $table->text('additional_info')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->integer('contract_rate')->default(0);
            $table->integer('markup')->default(0);
            $table->string('qty')->nullable();
            $table->string('min_pax')->nullable();
            $table->string('status')->nullable();
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
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
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
        DB::table('business_profiles')->insert(['id' => 1, 'profile_key' => 'primary', 'name' => 'Bali Kami Tour', 'caption' => 'Travel']);
        DB::table('partners')->insert(['id' => 1, 'name' => 'Partner A']);
        DB::table('activities')->insert([
            'id' => 1,
            'partners_id' => 1,
            'name' => 'Test Activity',
            'code' => 'ACT-001',
            'type' => 'Water',
            'location' => 'Bali',
            'description' => 'Activity description snapshot',
            'itinerary' => 'Activity itinerary snapshot',
            'duration' => '2 Hours',
            'include' => 'Activity include snapshot',
            'additional_info' => 'Activity info snapshot',
            'cancellation_policy' => 'Activity cancellation snapshot',
            'contract_rate' => 1500000,
            'markup' => 50,
            'qty' => '5',
            'min_pax' => '2',
            'status' => 'Active',
            'validity' => '2026-12-31',
        ]);
    }
}
