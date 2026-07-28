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
use App\Services\TourOrderLifecycleService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicTourPackageFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== ':memory:') {
            $this->markTestSkipped('Public Tour Package flow tests require sqlite :memory: to avoid touching active data.');
        }

        $this->withoutMiddleware([
            LogActivityMiddleware::class,
            TrackWebsiteVisit::class,
            UserActivity::class,
        ]);

        Cache::flush();
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

    public function test_tour_order_uses_authoritative_price_snapshot_and_creates_pending_reservation(): void
    {
        $user = $this->actingUser();

        $this->actingAs($user)
            ->post(route('func.order-tour-package.create', ['id' => 1]), $this->tourPayload([
                'price_total' => 1,
                'final_price' => 1,
            ]))
            ->assertRedirect(route('view.detail-order-tour', ['id' => 1]));

        $order = DB::table('orders')->where('service', 'Tour Package')->first();

        $this->assertSame('Pending', $order->status);
        $this->assertSame(1, (int) $order->price_id);
        $this->assertSame('150', (string) $order->price_pax);
        $this->assertSame('300', (string) $order->price_total);
        $this->assertSame('300', (string) $order->final_price);
        $this->assertSame('Tour include snapshot', $order->include);
        $this->assertStringContainsString('Tour itinerary snapshot', $order->itinerary);
        $this->assertNotNull($order->rsv_id);
        $this->assertDatabaseHas('reservations', [
            'id' => $order->rsv_id,
            'service' => 'Tour Package',
            'status' => 'Pending',
        ]);
        $this->assertSame(2, DB::table('guests')->where('order_id', $order->id)->where('rsv_id', $order->rsv_id)->count());
    }

    public function test_public_tour_listing_only_shows_active_packages(): void
    {
        DB::table('tours')->insert([
            'id' => 4,
            'name' => 'Inactive Tour',
            'code' => 'INACTIVE',
            'slug' => 'inactive-tour',
            'area' => 'Bali',
            'type_id' => 1,
            'status' => 'Inactive',
        ]);

        $this->get(route('view.tour-package-services'))
            ->assertOk()
            ->assertSee('Test Tour')
            ->assertDontSee('Inactive Tour');
    }

    public function test_public_tour_detail_rejects_inactive_package(): void
    {
        DB::table('tours')->insert([
            'id' => 5,
            'name' => 'Hidden Tour',
            'code' => 'HIDDEN',
            'slug' => 'hidden-tour',
            'area' => 'Bali',
            'type_id' => 1,
            'status' => 'Inactive',
        ]);

        $this->get(route('view.tour-detail', ['slug' => 'hidden-tour']))
            ->assertNotFound();
    }

    public function test_tour_order_rejects_price_from_other_package(): void
    {
        $this->actingAs($this->actingUser())
            ->from('/tour/test-tour')
            ->post(route('func.order-tour-package.create', ['id' => 1]), $this->tourPayload([
                'tour_price_id' => 3,
            ]))
            ->assertSessionHasErrors();

        $this->assertSame(0, DB::table('orders')->count());
    }

    public function test_tour_order_validates_minimum_participants_from_backend_rules(): void
    {
        $payload = $this->tourPayload([
            'number_of_guests' => 1,
            'guests' => [
                [
                    'name' => 'Leader Guest',
                    'phone' => '+628123456',
                    'age' => 'Adult',
                    'sex' => 'Male',
                    'identification_type' => 'Passport',
                    'identification_no' => 'P123',
                    'is_leader' => '1',
                ],
            ],
        ]);

        $this->actingAs($this->actingUser())
            ->from('/tour/test-tour')
            ->post(route('func.order-tour-package.create', ['id' => 1]), $payload)
            ->assertSessionHasErrors('number_of_guests');

        $this->assertSame(0, DB::table('orders')->count());
    }

    public function test_tour_order_validates_maximum_participants_from_price_tier(): void
    {
        $this->actingAs($this->actingUser())
            ->from('/tour/test-tour')
            ->post(route('func.order-tour-package.create', ['id' => 1]), $this->tourPayload([
                'number_of_guests' => 5,
                'guests' => array_merge($this->tourPayload()['guests'], [
                    [
                        'name' => 'Third Guest',
                        'phone' => '+628778',
                        'age' => 'Adult',
                        'sex' => 'Male',
                        'identification_type' => 'Passport',
                        'identification_no' => 'P789',
                        'is_leader' => '0',
                    ],
                    [
                        'name' => 'Fourth Guest',
                        'phone' => '+628779',
                        'age' => 'Adult',
                        'sex' => 'Female',
                        'identification_type' => 'Passport',
                        'identification_no' => 'P790',
                        'is_leader' => '0',
                    ],
                    [
                        'name' => 'Fifth Guest',
                        'phone' => '+628780',
                        'age' => 'Adult',
                        'sex' => 'Male',
                        'identification_type' => 'Passport',
                        'identification_no' => 'P791',
                        'is_leader' => '0',
                    ],
                ]),
            ]))
            ->assertSessionHasErrors('tour_price_id');

        $this->assertSame(0, DB::table('orders')->count());
    }

    public function test_customer_cannot_approve_tour_order_through_admin_route(): void
    {
        $user = $this->actingUser();
        $order = $this->insertApprovedTourOrder($user, ['status' => 'Pending']);

        $this->actingAs($user)
            ->put(url('/factivate-order/'.$order->id), [
                'due_date' => '2026-08-01',
                'currency' => 1,
                'bank_id' => 1,
            ])
            ->assertRedirect('/dashboard');

        $this->assertSame('Pending', DB::table('orders')->where('id', $order->id)->value('status'));
    }

    public function test_tour_duplicate_submission_token_returns_existing_order(): void
    {
        $user = $this->actingUser();
        $payload = $this->tourPayload(['submission_token' => 'same-tour-token']);

        $this->actingAs($user)->post(route('func.order-tour-package.create', ['id' => 1]), $payload);
        $this->actingAs($user)
            ->post(route('func.order-tour-package.create', ['id' => 1]), $payload)
            ->assertRedirect(route('view.detail-order-tour', ['id' => 1]));

        $this->assertSame(1, DB::table('orders')->where('service', 'Tour Package')->count());
        $this->assertSame(1, DB::table('reservations')->count());
    }

    public function test_tour_payment_upload_stores_private_receipt_and_guarded_route_enforces_owner(): void
    {
        $owner = $this->actingUser(10);
        $nonOwner = $this->actingUser(11, 'other@example.test');
        $order = $this->insertApprovedTourOrder($owner);

        $invoice = InvoiceAdmin::create([
            'rsv_id' => $order->rsv_id,
            'inv_no' => 'TOUR-INV-001',
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
            ->assertRedirect(route('view.detail-order-tour', ['id' => $order->id]));

        $payment = PaymentConfirmation::first();

        $this->assertSame(1, (int) $payment->kurs_id);
        $this->assertStringStartsWith('tour/payments/'.$order->id.'/', $payment->receipt_img);
        Storage::disk('private')->assertExists($payment->receipt_img);

        $this->actingAs($nonOwner)
            ->get(route('orders.tour.payments.receipt', ['order' => $order->id, 'payment' => $payment->id]))
            ->assertNotFound();

        $response = $this->actingAs($owner)
            ->get(route('orders.tour.payments.receipt', ['order' => $order->id, 'payment' => $payment->id]))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));

        Storage::disk('private')->put(
            app(AccommodationFinancialFileService::class)->privateInvoicePath($order, $invoice, 'en'),
            "%PDF-1.4\n% tour invoice\n"
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

    public function test_tour_completion_uses_completed_at_and_history_scope(): void
    {
        $owner = $this->actingUser();
        $order = $this->insertApprovedTourOrder($owner, [
            'status' => 'Paid',
            'checkin' => '2026-07-20 08:00:00',
            'checkout' => '2026-07-21 08:00:00',
        ]);

        $this->assertSame(1, app(TourOrderLifecycleService::class)->completeEligiblePaidOrders(null, now()));
        $this->assertNotNull(DB::table('orders')->where('id', $order->id)->value('completed_at'));
        $this->assertSame('Completed', DB::table('reservations')->where('id', $order->rsv_id)->value('status'));
        $this->assertSame(0, app(TourOrderLifecycleService::class)->completeEligiblePaidOrders(null, now()));

        $this->assertSame(0, app(TourOrderLifecycleService::class)->applyTourCurrentScope(Orders::query(), now())->count());
        $this->assertSame(1, app(TourOrderLifecycleService::class)->applyTourHistoryScope(Orders::query(), now())->count());
    }

    private function tourPayload(array $overrides = []): array
    {
        return array_merge([
            'submission_token' => 'tour-token-1',
            'number_of_guests' => 2,
            'tour_price_id' => 1,
            'travel_date' => '2026-08-10',
            'lead_guest_name' => 'Leader Guest',
            'lead_guest_phone' => '+628123456',
            'lead_guest_email' => 'leader@example.test',
            'pickup_location' => 'Hotel Lobby',
            'dropoff_location' => 'Hotel Lobby',
            'terms_accepted' => '1',
            'guests' => [
                [
                    'name' => 'Leader Guest',
                    'phone' => '+628123456',
                    'age' => 'Adult',
                    'sex' => 'Male',
                    'identification_type' => 'Passport',
                    'identification_no' => 'P123',
                    'is_leader' => '1',
                ],
                [
                    'name' => 'Second Guest',
                    'phone' => '+628777',
                    'age' => 'Adult',
                    'sex' => 'Female',
                    'identification_type' => 'Passport',
                    'identification_no' => 'P456',
                    'is_leader' => '0',
                ],
            ],
        ], $overrides);
    }

    private function actingUser(int $id = 10, string $email = 'agent@example.test'): User
    {
        return User::create([
            'id' => $id,
            'name' => 'Tour Agent '.$id,
            'email' => $email,
            'password' => 'secret',
            'type' => 'user',
            'position' => 'agent',
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
            'code' => 'TUR',
        ]);
    }

    private function insertApprovedTourOrder(User $owner, array $overrides = []): Orders
    {
        $reservation = Reservation::create([
            'rsv_no' => $overrides['orderno'] ?? 'TOUR-APPROVED',
            'service' => 'Tour Package',
            'agn_id' => $owner->id,
            'adm_id' => $owner->id,
            'checkin' => $overrides['checkin'] ?? '2026-08-10 00:00:00',
            'checkout' => $overrides['checkout'] ?? '2026-08-11 00:00:00',
            'status' => 'Active',
        ]);

        return Orders::create(array_merge([
            'user_id' => $owner->id,
            'sales_agent' => $owner->id,
            'name' => $owner->name,
            'email' => $owner->email,
            'orderno' => 'TOUR-APPROVED',
            'service' => 'Tour Package',
            'service_id' => 1,
            'servicename' => 'Test Tour',
            'price_id' => 1,
            'checkin' => '2026-08-10 00:00:00',
            'checkout' => '2026-08-11 00:00:00',
            'travel_date' => '2026-08-10 00:00:00',
            'duration' => '2D/1N',
            'number_of_guests' => 2,
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
            'order_logs',
            'orders',
            'tours_images',
            'tour_package_locations',
            'tour_prices',
            'tours',
            'tour_types',
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
            $table->string('type')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_2')->nullable();
            $table->string('phone_3')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('logo_dark')->nullable();
            $table->text('public_tagline')->nullable();
            $table->text('public_description')->nullable();
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
            $table->string('label_traditional')->nullable();
            $table->string('label_simplified')->nullable();
            $table->string('route_name')->nullable();
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('open_new_tab')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('discounts', 14, 2)->default(0);
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('tour_types', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->timestamps();
        });

        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('slug')->nullable();
            $table->string('area')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->longText('package_highlights')->nullable();
            $table->integer('duration_days')->default(2);
            $table->integer('duration_nights')->default(1);
            $table->longText('itinerary')->nullable();
            $table->longText('include')->nullable();
            $table->longText('exclude')->nullable();
            $table->longText('additional_info')->nullable();
            $table->longText('cancellation_policy')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('tours_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_id')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('tour_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_id')->nullable();
            $table->integer('min_qty')->default(2);
            $table->integer('max_qty')->default(10);
            $table->integer('contract_rate')->default(0);
            $table->integer('markup')->default(0);
            $table->date('expired_date');
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('tour_package_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_id')->nullable();
            $table->unsignedInteger('day_number')->default(1);
            $table->unsignedInteger('visit_order')->default(1);
            $table->time('visit_time')->nullable();
            $table->string('destination_name')->nullable();
            $table->string('location_type')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
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
            $table->string('servicename')->nullable();
            $table->unsignedBigInteger('price_id')->nullable();
            $table->dateTime('checkin')->nullable();
            $table->dateTime('checkout')->nullable();
            $table->dateTime('travel_date')->nullable();
            $table->string('location')->nullable();
            $table->string('tour_type')->nullable();
            $table->integer('number_of_guests')->nullable();
            $table->longText('destinations')->nullable();
            $table->longText('itinerary')->nullable();
            $table->longText('include')->nullable();
            $table->longText('exclude')->nullable();
            $table->longText('additional_info')->nullable();
            $table->longText('cancellation_policy')->nullable();
            $table->text('guest_detail')->nullable();
            $table->string('pickup_location')->nullable();
            $table->string('pickup_name')->nullable();
            $table->string('pickup_phone')->nullable();
            $table->dateTime('pickup_date')->nullable();
            $table->dateTime('dropoff_date')->nullable();
            $table->string('dropoff_location')->nullable();
            $table->text('note')->nullable();
            $table->longText('msg')->nullable();
            $table->string('duration')->nullable();
            $table->decimal('price_pax', 14, 2)->default(0);
            $table->decimal('normal_price', 14, 2)->default(0);
            $table->decimal('price_total', 14, 2)->default(0);
            $table->decimal('final_price', 14, 2)->default(0);
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
        DB::table('business_profiles')->insert(['id' => 1, 'profile_key' => 'primary', 'name' => 'Bali Kami Tour']);
        DB::table('tour_types')->insert(['id' => 1, 'type' => 'Adventure']);
        DB::table('tours')->insert([
            'id' => 1,
            'name' => 'Test Tour',
            'code' => 'TOUR',
            'slug' => 'test-tour',
            'area' => 'Bali',
            'type_id' => 1,
            'package_highlights' => 'Tour highlights snapshot',
            'duration_days' => 2,
            'duration_nights' => 1,
            'itinerary' => 'Tour itinerary snapshot',
            'include' => 'Tour include snapshot',
            'exclude' => 'Tour exclude snapshot',
            'additional_info' => 'Tour info snapshot',
            'cancellation_policy' => 'Tour cancellation snapshot',
            'status' => 'Active',
        ]);
        DB::table('tours')->insert([
            'id' => 2,
            'name' => 'Other Tour',
            'code' => 'OTHER',
            'slug' => 'other-tour',
            'type_id' => 1,
            'status' => 'Active',
        ]);
        DB::table('tour_prices')->insert([
            'id' => 1,
            'tour_id' => 1,
            'min_qty' => 2,
            'max_qty' => 4,
            'contract_rate' => 1500000,
            'markup' => 50,
            'expired_date' => '2026-12-31',
            'status' => 'Active',
        ]);
        DB::table('tour_package_locations')->insert([
            'id' => 1,
            'tour_id' => 1,
            'day_number' => 1,
            'visit_order' => 1,
            'visit_time' => '09:00:00',
            'destination_name' => 'Snapshot Temple',
            'location_type' => 'Attraction',
            'description' => 'Tour itinerary snapshot',
            'is_active' => true,
        ]);
        DB::table('tour_prices')->insert([
            'id' => 3,
            'tour_id' => 2,
            'min_qty' => 2,
            'max_qty' => 4,
            'contract_rate' => 1000000,
            'markup' => 10,
            'expired_date' => '2026-12-31',
            'status' => 'Active',
        ]);
    }
}
