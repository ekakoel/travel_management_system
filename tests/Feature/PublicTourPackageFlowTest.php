<?php

namespace Tests\Feature;

use App\Http\Controllers\FrontEndController;
use App\Http\Middleware\LogActivityMiddleware;
use App\Http\Middleware\TrackWebsiteVisit;
use App\Http\Middleware\UserActivity;
use App\Models\InvoiceAdmin;
use App\Models\Orders;
use App\Models\PaymentConfirmation;
use App\Models\Reservation;
use App\Models\User;
use App\Services\AccommodationFinancialFileService;
use App\Services\OrderConfirmationEmailDataService;
use App\Services\TourOrderLifecycleService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
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
                'number_of_guests' => 99,
                'special_request' => 'Wheelchair assistance',
            ]))
            ->assertRedirect(route('view.detail-order-tour', ['id' => 1]));

        $order = DB::table('orders')->where('service', 'Tour Package')->first();

        $this->assertSame('Pending', $order->status);
        $this->assertSame(1, (int) $order->price_id);
        $this->assertSame('150', (string) $order->price_pax);
        $this->assertSame('300', (string) $order->price_total);
        $this->assertSame('300', (string) $order->final_price);
        $this->assertSame('Wheelchair assistance', $order->note);
        $this->assertNull($order->msg);
        $this->assertSame(2, (int) $order->number_of_guests);
        $this->assertSame(4_500_000, (int) $order->final_total_idr);
        $this->assertSame(30_000, (int) $order->final_total_usd_minor);
        $this->assertNotNull($order->pricing_snapshot_id);
        $this->assertNotNull($order->submission_token_hash);
        $this->assertDatabaseHas('order_pricing_snapshots', [
            'id' => $order->pricing_snapshot_id,
            'order_id' => $order->id,
            'final_total_idr' => 4_500_000,
            'final_total_usd_minor' => 30_000,
        ]);
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

    public function test_admin_approval_commits_tour_snapshot_invoice_and_active_reservation(): void
    {
        Mail::fake();
        $agent = $this->actingUser();
        $this->actingAs($agent)
            ->post(route('func.order-tour-package.create', ['id' => 1]), $this->tourPayload())
            ->assertRedirect();
        $order = Orders::query()->firstOrFail();

        $admin = User::query()->create([
            'name' => 'Tour Admin',
            'email' => 'tour-admin@example.test',
            'password' => bcrypt('password'),
            'type' => 'admin',
            'position' => 'developer',
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $this->flushSession();
        $this->actingAs($admin)
            ->put('/factivate-order/'.$order->id, ['bank' => 1, 'currency' => 1])
            ->assertRedirect('/orders-admin-'.$order->id);

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'Approved']);
        $this->assertDatabaseHas('reservations', [
            'id' => $order->rsv_id,
            'status' => 'Active',
        ]);
        $this->assertFalse(Schema::hasColumn('reservations', 'send'));
        $this->assertDatabaseHas('invoice_admins', [
            'rsv_id' => $order->rsv_id,
            'total_usd' => 300,
            'total_idr' => 4_500_000,
            'total_cny' => 2_250,
            'total_twd' => 9_000,
            'balance' => 300,
            'currency_id' => 1,
            'rate_cny' => 2_000,
            'sell_cny' => 2_000,
            'rate_twd' => 500,
            'sell_twd' => 500,
        ]);
        $this->assertDatabaseHas('order_logs', [
            'order_id' => $order->id,
            'action' => 'Confirm Tour Package Order',
        ]);
        $this->assertDatabaseHas('order_logs', [
            'order_id' => $order->id,
            'action' => 'Send Confirmation',
            'method' => 'Send',
        ]);

        $invoice = InvoiceAdmin::where('rsv_id', $order->rsv_id)->firstOrFail();
        $files = app(AccommodationFinancialFileService::class);

        foreach (['en', 'zh-CN', 'zh'] as $locale) {
            $path = $files->privateInvoicePath($order->fresh(), $invoice, $locale);

            Storage::disk('private')->assertExists($path);
            $pdfContent = Storage::disk('private')->get($path);
            $this->assertStringStartsWith('%PDF', $pdfContent);

            if ($locale !== 'en') {
                $this->assertStringContainsString('NotoSans', $pdfContent);
            }
        }
    }

    public function test_admin_tour_confirm_order_rejects_repeat_execution_without_duplicate_invoice_or_log(): void
    {
        Mail::fake();
        $agent = $this->actingUser();
        $this->actingAs($agent)
            ->post(route('func.order-tour-package.create', ['id' => 1]), $this->tourPayload())
            ->assertRedirect();
        $order = Orders::query()->firstOrFail();
        $admin = User::query()->create([
            'name' => 'Tour Confirm Admin',
            'email' => 'tour-confirm-admin@example.test',
            'password' => bcrypt('password'),
            'type' => 'admin',
            'position' => 'developer',
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $this->flushSession();
        $this->actingAs($admin)
            ->put('/factivate-order/'.$order->id, ['bank' => 1, 'currency' => 1])
            ->assertRedirect('/orders-admin-'.$order->id);

        $this->actingAs($admin)
            ->put('/factivate-order/'.$order->id, ['bank' => 1, 'currency' => 1])
            ->assertStatus(409);

        $this->assertSame(1, InvoiceAdmin::query()->where('rsv_id', $order->rsv_id)->count());
        $this->assertSame(1, DB::table('order_logs')
            ->where('order_id', $order->id)
            ->where('action', 'Confirm Tour Package Order')
            ->count());

        $this->actingAs($admin)
            ->put('/fsend-confirmation-'.$order->id)
            ->assertStatus(409);
        $this->actingAs($admin)
            ->from('/orders-admin-'.$order->id)
            ->put('/fresend-confirmation-order-'.$order->id)
            ->assertRedirect('/orders-admin-'.$order->id);

        $this->assertSame(1, DB::table('order_logs')
            ->where('order_id', $order->id)
            ->where('action', 'Send Confirmation')
            ->count());
        $this->assertSame(1, DB::table('order_logs')
            ->where('order_id', $order->id)
            ->where('action', 'Resend Confirmation')
            ->count());
    }

    public function test_admin_tour_approval_uses_selected_cny_or_twd_as_payable_currency(): void
    {
        Mail::fake();
        $agent = $this->actingUser();
        $admin = User::query()->create([
            'name' => 'Tour Currency Admin',
            'email' => 'tour-currency-admin@example.test',
            'password' => bcrypt('password'),
            'type' => 'admin',
            'position' => 'developer',
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        foreach ([
            ['id' => 2, 'code' => 'CNY', 'balance' => 2_250],
            ['id' => 3, 'code' => 'TWD', 'balance' => 9_000],
        ] as $currency) {
            $this->flushSession();
            $this->actingAs($agent)
                ->post(route('func.order-tour-package.create', ['id' => 1]), $this->tourPayload([
                    'submission_token' => 'tour-currency-'.$currency['code'],
                ]))
                ->assertRedirect();
            $order = Orders::query()->latest('id')->firstOrFail();

            $this->flushSession();
            $this->actingAs($admin)
                ->put('/factivate-order/'.$order->id, ['bank' => 1, 'currency' => $currency['id']])
                ->assertRedirect('/orders-admin-'.$order->id);

            $this->assertDatabaseHas('invoice_admins', [
                'rsv_id' => $order->rsv_id,
                'total_usd' => 300,
                'total_idr' => 4_500_000,
                'total_cny' => 2_250,
                'total_twd' => 9_000,
                'currency_id' => $currency['id'],
                'balance' => $currency['balance'],
            ]);
        }
    }

    public function test_admin_tour_workflow_template_lists_supported_payable_currencies(): void
    {
        $template = file_get_contents(resource_path('views/admin/ordersadmindetail.blade.php'));

        $this->assertStringContainsString("->whereIn('name', ['USD', 'CNY', 'TWD'])", $template);
        $this->assertStringContainsString("'USD' => 'USD - US Dollar'", $template);
        $this->assertStringContainsString("'CNY' => 'CNY - Chinese Yuan'", $template);
        $this->assertStringContainsString("'TWD' => 'TWD - Taiwan Dollar'", $template);
        $this->assertStringNotContainsString("'IDR' => 'IDR - Indonesian Rupiah'", $template);
    }

    public function test_admin_tour_workflow_exposes_each_invoice_language_explicitly(): void
    {
        $template = file_get_contents(resource_path('views/admin/ordersadmindetail.blade.php'));
        $chineseTemplate = file_get_contents(resource_path('views/emails/invoiceTourZh.blade.php'));

        $this->assertStringContainsString('Invoice English', $template);
        $this->assertStringContainsString('Invoice Chinese Simplified', $template);
        $this->assertStringContainsString('Invoice Chinese Traditional', $template);
        $this->assertStringContainsString('Regenerate 3-language Invoice', $template);
        $this->assertStringContainsString('$hasCompleteInvoiceSet', $template);
        $this->assertStringContainsString("'locale' => 'zh-CN'", $template);
        $this->assertStringContainsString('font-family: "notosans", sans-serif', $chineseTemplate);
        $this->assertStringContainsString("'invoice' => '发票'", $chineseTemplate);
        $this->assertStringContainsString("'invoice' => '發票'", $chineseTemplate);
        $this->assertStringContainsString("currencyFormatUsd(\$tourPricing['total_usd'])", $chineseTemplate);
        $this->assertStringContainsString("'total_price_usd' => '总价（USD）'", $chineseTemplate);
        $this->assertStringContainsString("'total_price_usd' => '總價（USD）'", $chineseTemplate);
        $this->assertStringContainsString('Amount Due ({{ $currencyCode }})', file_get_contents(resource_path('views/emails/invoiceTourEn.blade.php')));
    }

    public function test_tour_confirmation_email_uses_professional_international_contract(): void
    {
        $agent = $this->actingUser();
        $agent->update(['name' => 'Travel Partner <b>Unsafe</b>']);

        $this->actingAs($agent)
            ->post(route('func.order-tour-package.create', ['id' => 1]), $this->tourPayload())
            ->assertRedirect();

        $order = Orders::query()->firstOrFail();
        $reservation = Reservation::query()->findOrFail($order->rsv_id);
        $invoice = InvoiceAdmin::create([
            'rsv_id' => $reservation->id,
            'inv_no' => 'INV-EMAIL-001',
            'inv_date' => now(),
            'due_date' => now()->addDays(2),
            'total_usd' => 300,
            'total_idr' => 4_500_000,
            'total_cny' => 2_250,
            'total_twd' => 9_000,
            'balance' => 2_250,
            'currency_id' => 2,
            'bank_id' => 1,
        ]);
        $confirmation = app(OrderConfirmationEmailDataService::class)->build(
            $order,
            $reservation,
            $invoice,
            $agent
        );
        $html = view('emails.confirmationOrder', compact('confirmation'))->render();

        $this->assertSame('Order Confirmed | '.$order->orderno.' | Bali Kami Tour', $confirmation['subject']);
        $this->assertSame('$300.00', $confirmation['billing']['total_usd']);
        $this->assertSame('CNY', $confirmation['billing']['currency']);
        $this->assertSame('CNY 2,250', $confirmation['billing']['amount_due']);
        $this->assertSame(
            route('view.detail-order-tour', ['id' => $order->id]),
            $confirmation['action_url']
        );
        $this->assertStringContainsString('/detail-order-tour/'.$order->id, $confirmation['action_url']);
        $this->assertStringNotContainsString('/detail-order-'.$order->id, $confirmation['action_url']);
        $this->assertStringContainsString('Your order is confirmed', $html);
        $this->assertStringContainsString('Order reference', $html);
        $this->assertStringContainsString('Total price (USD)', $html);
        $this->assertStringContainsString('Amount due (CNY)', $html);
        $this->assertStringContainsString('View order details', $html);
        $this->assertStringContainsString('Three invoice PDFs are attached', $html);
        $this->assertStringContainsString('Travel Partner &lt;b&gt;Unsafe&lt;/b&gt;', $html);
        $this->assertStringNotContainsString('Travel Partner <b>Unsafe</b>', $html);
        $this->assertStringNotContainsString('display:flex', $html);
        $this->assertStringNotContainsString('display:grid', $html);
        $this->assertStringNotContainsString('<form', $html);
        $this->assertStringNotContainsString('<script', $html);
    }

    public function test_admin_tour_terminal_actions_use_canonical_statuses_and_cancel_reservation(): void
    {
        $agent = $this->actingUser();
        $this->actingAs($agent)
            ->post(route('func.order-tour-package.create', ['id' => 1]), $this->tourPayload())
            ->assertRedirect();
        $order = Orders::query()->firstOrFail();
        $admin = User::query()->create([
            'name' => 'Tour Admin',
            'email' => 'tour-terminal-admin@example.test',
            'password' => bcrypt('password'),
            'type' => 'admin',
            'position' => 'developer',
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $this->flushSession();
        $this->actingAs($admin)
            ->put('/fupdate-order-invalid/'.$order->id, ['msg' => '<script>bad()</script>Invalid guest data'])
            ->assertRedirect('/orders-admin');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Invalid',
            'msg' => 'bad()Invalid guest data',
        ]);
        $this->assertDatabaseHas('reservations', ['id' => $order->rsv_id, 'status' => 'Canceled']);

        $this->actingAs($admin)
            ->put('/farchive-order/'.$order->id, ['msg' => 'Move to history'])
            ->assertRedirect('/orders-admin');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Deleted',
        ]);
        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
            'status' => 'Archive',
        ]);
    }

    public function test_admin_tour_confirmation_and_notes_are_validated_and_sanitized(): void
    {
        $agent = $this->actingUser();
        $this->actingAs($agent)
            ->post(route('func.order-tour-package.create', ['id' => 1]), $this->tourPayload())
            ->assertRedirect();
        $order = Orders::query()->firstOrFail();
        $admin = User::query()->create([
            'name' => 'Tour Admin',
            'email' => 'tour-note-admin@example.test',
            'password' => bcrypt('password'),
            'type' => 'admin',
            'position' => 'developer',
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $this->flushSession();
        $this->actingAs($admin)
            ->put('/fupdate-confirmation-number-'.$order->id, ['confirmation_order' => 'TOUR-CONF-001'])
            ->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'confirmation_order' => 'TOUR-CONF-001',
            'handled_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->post('/fadd-order-note-'.$order->id, [
                'status' => 'Info',
                'order_note' => '<b>Supplier contacted</b>',
            ])
            ->assertRedirect();
        $this->assertDatabaseHas('order_notes', [
            'order_id' => $order->id,
            'status' => 'Info',
            'note' => 'Supplier contacted',
        ]);
    }

    public function test_admin_tour_manifest_mutations_reprice_atomically_across_pax_tiers(): void
    {
        DB::table('tour_prices')->where('id', 1)->update(['max_qty' => 2]);
        DB::table('tour_prices')->insert([
            'id' => 2,
            'tour_id' => 1,
            'min_qty' => 3,
            'max_qty' => 4,
            'contract_rate' => 1_200_000,
            'markup' => 50,
            'contract_rate_idr' => 1_200_000,
            'markup_type' => 'usd',
            'markup_amount' => '50',
            'markup_currency' => 'USD',
            'markup_source' => 'test-fixture',
            'markup_verified_at' => now(),
            'markup_verified_by' => 1,
            'pricing_data_status' => 'ready',
            'valid_from' => now()->subDay()->toDateString(),
            'expired_date' => '2026-12-31',
            'valid_until' => '2026-12-31',
            'status' => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('booking_codes')->insert([
            'code' => 'MANIFEST10',
            'used' => 0,
            'amount' => 1,
            'status' => 'Active',
            'discount_type' => 'fixed',
            'discount_value' => 10,
            'discount_currency' => 'USD',
            'service_scope' => 'Tour Package',
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addMonth(),
            'pricing_data_status' => 'ready',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $agent = $this->actingUser();
        $this->actingAs($agent)
            ->post(route('func.order-tour-package.create', ['id' => 1]), $this->tourPayload([
                'booking_code' => 'MANIFEST10',
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('view.detail-order-tour', ['id' => 1]));
        $order = Orders::query()->firstOrFail();
        $admin = User::query()->create([
            'name' => 'Manifest Admin',
            'email' => 'manifest-admin@example.test',
            'password' => bcrypt('password'),
            'type' => 'admin',
            'position' => 'developer',
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
        ]);

        $this->flushSession();
        $this->actingAs($admin)
            ->put('/fadd-guest/'.$order->id, [
                'rsv_id' => $order->rsv_id,
                'name' => 'Third Guest',
                'phone' => '+628999',
                'age' => 'Adult',
                'sex' => 'Male',
            ])
            ->assertRedirect();

        $order->refresh();
        $thirdGuest = DB::table('guests')->where('order_id', $order->id)->orderByDesc('id')->first();
        $this->assertSame(3, (int) $order->number_of_guests);
        $this->assertSame(2, (int) $order->price_id);
        $this->assertSame(130.0, (float) $order->price_pax);
        $this->assertSame(380.0, (float) $order->final_price);
        $this->assertSame('MANIFEST10', $order->bookingcode);
        $this->assertSame(2, DB::table('order_pricing_snapshots')->where('order_id', $order->id)->count());
        $this->assertDatabaseHas('order_pricing_snapshots', [
            'id' => $order->pricing_snapshot_id,
            'quantity' => 3,
            'price_id' => 2,
            'reason' => 'tour_guest_added',
        ]);

        $this->actingAs($admin)
            ->put('/fupdate-guest/'.$thirdGuest->id, [
                'name' => 'Third Guest Updated',
                'phone' => '+628999',
                'age' => 'Child',
                'sex' => 'Female',
            ])
            ->assertRedirect();
        $this->assertDatabaseHas('order_pricing_snapshots', [
            'order_id' => $order->id,
            'snapshot_sequence' => 3,
            'quantity' => 3,
            'reason' => 'tour_guest_updated',
        ]);

        $this->actingAs($admin)
            ->delete('/delete-guest/'.$thirdGuest->id)
            ->assertRedirect();
        $order->refresh();
        $this->assertSame(2, (int) $order->number_of_guests);
        $this->assertSame(1, (int) $order->price_id);
        $this->assertSame(290.0, (float) $order->final_price);
        $this->assertDatabaseHas('order_pricing_snapshots', [
            'id' => $order->pricing_snapshot_id,
            'snapshot_sequence' => 4,
            'quantity' => 2,
            'reason' => 'tour_guest_removed',
        ]);

        $remainingGuestId = DB::table('guests')->where('order_id', $order->id)->value('id');
        $this->actingAs($admin)
            ->from('/orders-admin-'.$order->id)
            ->delete('/delete-guest/'.$remainingGuestId)
            ->assertRedirect('/orders-admin-'.$order->id)
            ->assertSessionHasErrors('guests');
        $this->assertSame(2, DB::table('guests')->where('order_id', $order->id)->count());
        $this->assertSame(4, DB::table('order_pricing_snapshots')->where('order_id', $order->id)->count());
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

        $this->get(route('view.tour-packages-service'))
            ->assertOk()
            ->assertSee('Test Tour')
            ->assertDontSee('Inactive Tour');
    }

    public function test_legacy_tour_package_service_url_uses_the_container_resolved_canonical_action(): void
    {
        $legacyRoute = app('router')->getRoutes()->getByName('tour-package-service');
        $canonicalRoute = app('router')->getRoutes()->getByName('view.tour-packages-service');

        $this->assertNotNull($legacyRoute);
        $this->assertNotNull($canonicalRoute);
        $this->assertSame(
            FrontEndController::class.'@tour_package_services',
            $legacyRoute->getActionName()
        );
        $this->assertSame($canonicalRoute->getActionName(), $legacyRoute->getActionName());

        $this->get(route('tour-package-service'))
            ->assertOk()
            ->assertViewIs('frontend.landing-page.tours.directory')
            ->assertSee('Test Tour');
    }

    public function test_legacy_tour_index_redirects_to_canonical_localized_directory(): void
    {
        $this->actingAs($this->actingUser())
            ->get(route('view.tours'))
            ->assertRedirect(route('view.tour-packages-service'));
    }

    public function test_public_tour_pages_render_localized_ui_copy_for_each_supported_chinese_locale(): void
    {
        $user = $this->actingUser();

        foreach (['zh', 'zh-CN'] as $locale) {
            $this->actingAs($user)
                ->withSession(['locale' => $locale])
                ->get(route('view.tour-packages-service'))
                ->assertOk()
                ->assertSee(__('tour-packages.hero.title', [], $locale))
                ->assertSee(__('tour-detail.duration_days_nights', [
                    'days' => 2,
                    'nights' => 1,
                ], $locale));

            $this->withSession(['locale' => $locale])
                ->get(route('view.tour-detail', ['slug' => 'test-tour']))
                ->assertOk()
                ->assertSee(__('tour-detail.topband_title', [], $locale))
                ->assertSee('data-price-unavailable-label="'.__('tour-detail.price_temporarily_unavailable', [], $locale).'"', false)
                ->assertSee('data-loading-price-label="'.__('tour-detail.loading_price', [], $locale).'"', false)
                ->assertSee('Test Tour');
        }
    }

    public function test_tour_frontend_translation_domains_have_key_parity(): void
    {
        foreach (['tour-packages', 'tour-detail', 'tour-map'] as $domain) {
            $englishKeys = array_keys(Arr::dot(require resource_path("lang/en/{$domain}.php")));
            sort($englishKeys);

            foreach (['zh', 'zh-CN'] as $locale) {
                $localizedKeys = array_keys(Arr::dot(require resource_path("lang/{$locale}/{$domain}.php")));
                sort($localizedKeys);

                $this->assertSame($englishKeys, $localizedKeys, "Missing or extra {$domain} keys for {$locale}.");
            }
        }
    }

    public function test_tour_order_package_overview_prefers_localized_tour_content_with_safe_fallbacks(): void
    {
        $user = $this->actingUser();
        $this->actingAs($user)
            ->post(route('func.order-tour-package.create', ['id' => 1]), $this->tourPayload())
            ->assertRedirect(route('view.detail-order-tour', ['id' => 1]));
        $order = Orders::query()->firstOrFail();

        DB::table('tours')->where('id', 1)->update([
            'itinerary_traditional' => '<p>繁體中文行程內容</p>',
            'itinerary_simplified' => '<p>简体中文行程内容</p>',
            'additional_info_traditional' => '<p>繁體中文附加資訊</p>',
            'additional_info_simplified' => '<p>简体中文附加信息</p>',
        ]);

        foreach ([
            'zh' => ['繁體中文行程內容', '繁體中文附加資訊'],
            'zh-CN' => ['简体中文行程内容', '简体中文附加信息'],
        ] as $locale => [$itinerary, $additionalInfo]) {
            $this->actingAs($user)
                ->withSession(['locale' => $locale])
                ->get(route('view.detail-order-tour', ['id' => $order->id]))
                ->assertOk()
                ->assertSee($itinerary)
                ->assertSee($additionalInfo)
                ->assertDontSee('Tour itinerary snapshot')
                ->assertDontSee('Tour info snapshot');
        }

        $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get(route('view.detail-order-tour', ['id' => $order->id]))
            ->assertOk()
            ->assertSee('Tour itinerary snapshot')
            ->assertSee('Tour info snapshot');
    }

    public function test_tour_order_price_details_show_snapshot_unit_price_and_guest_total(): void
    {
        $user = $this->actingUser();

        $this->actingAs($user)
            ->post(route('func.order-tour-package.create', ['id' => 1]), $this->tourPayload())
            ->assertRedirect(route('view.detail-order-tour', ['id' => 1]));

        $this->actingAs($user)
            ->get(route('view.detail-order-tour', ['id' => 1]))
            ->assertOk()
            ->assertSee(__('messages.Price/pax'))
            ->assertSee(currencyFormatUsd('150.00'))
            ->assertSee(__('tour-detail.price_for_guests', ['count' => 2]))
            ->assertSee(currencyFormatUsd('300.00'));

        $template = file_get_contents(resource_path('views/frontend/home/orders/details/tour-modern.blade.php'));
        $this->assertStringContainsString("\$tourPricing['unit_price_usd']", $template);
        $this->assertStringContainsString("\$tourPricing['gross_total_usd']", $template);
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

    public function test_tour_order_modal_uses_manifest_driven_pax_without_identity_or_leader_fields(): void
    {
        $this->actingAs($this->actingUser())
            ->get(route('view.tour-detail', ['slug' => 'test-tour']))
            ->assertOk()
            ->assertSee('name="travel_date"', false)
            ->assertSee('name="pickup_location"', false)
            ->assertSee('name="dropoff_location"', false)
            ->assertSee('data-tour-guest-field="name"', false)
            ->assertSee('data-tour-guest-field="phone"', false)
            ->assertSee('data-tour-guest-field="age"', false)
            ->assertSee('data-tour-guest-field="sex"', false)
            ->assertDontSee('name="number_of_guests"', false)
            ->assertDontSee('data-tour-guest-field="identification_type"', false)
            ->assertDontSee('data-tour-guest-field="identification_no"', false)
            ->assertDontSee('data-tour-guest-field="is_leader"', false)
            ->assertDontSee('name="lead_guest_name"', false)
            ->assertDontSee('name="lead_guest_phone"', false);
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

    public function test_tour_detail_does_not_offer_price_expired_for_prefilled_travel_date(): void
    {
        DB::table('tour_prices')->where('id', 1)->update([
            'expired_date' => '2026-08-01',
            'valid_until' => '2026-08-01',
        ]);

        $this->actingAs($this->actingUser())
            ->withSession([
                '_old_input' => ['travel_date' => '2026-08-10 09:00'],
            ])
            ->get(route('view.tour-detail', ['slug' => 'test-tour']))
            ->assertOk()
            ->assertSee(__('tour-detail.no_active_price'));
    }

    public function test_tour_detail_displays_database_rate_by_validity_without_legacy_status_filter(): void
    {
        DB::table('tour_prices')->where('id', 1)->update([
            'contract_rate_idr' => 1_800_000,
            'markup_amount' => '20',
            'status' => 'Draft',
            'pricing_data_status' => 'unresolved',
        ]);

        $this->actingAs($this->actingUser())
            ->get(route('view.tour-detail', ['slug' => 'test-tour']))
            ->assertOk()
            ->assertSee('USD 140.00', false)
            ->assertSee('Valid 2026-07-26 to 2026-12-31', false);
    }

    public function test_tour_detail_explains_independent_rate_and_tax_blockers(): void
    {
        DB::table('usd_rates')->where('name', 'USD')->update([
            'retrieved_at' => now()->subDays(2),
        ]);
        DB::table('tax_policies')->where('service', 'Tour Package')->delete();

        $this->actingAs($this->actingUser())
            ->get(route('view.tour-detail', ['slug' => 'test-tour']))
            ->assertOk()
            ->assertSee(__('tour-detail.pricing_requirements'))
            ->assertSee(__('tour-detail.pricing_rate_stale'))
            ->assertSee(__('tour-detail.pricing_tax_missing'))
            ->assertSee(trans_choice('tour-detail.pricing_tiers_ready', 1, ['count' => 1]))
            ->assertSee(__('tour-detail.pricing_tier_label', [
                'min' => 2,
                'max' => 4,
                'from' => '2026-07-26',
                'until' => '2026-12-31',
            ]));
    }

    public function test_tour_order_rejects_price_expired_before_travel_date(): void
    {
        DB::table('tour_prices')->where('id', 1)->update([
            'expired_date' => '2026-08-01',
            'valid_until' => '2026-08-01',
        ]);

        $this->actingAs($this->actingUser())
            ->from('/tour/test-tour')
            ->post(route('func.order-tour-package.create', ['id' => 1]), $this->tourPayload())
            ->assertSessionHasErrors('tour_price_id');

        $this->assertSame(0, DB::table('orders')->count());
    }

    public function test_tour_order_uses_validity_and_not_legacy_price_status(): void
    {
        DB::table('tour_prices')->where('id', 1)->update([
            'status' => 'Draft',
            'pricing_data_status' => 'unresolved',
        ]);

        $this->actingAs($this->actingUser())
            ->from('/tour/test-tour')
            ->post(route('func.order-tour-package.create', ['id' => 1]), $this->tourPayload())
            ->assertRedirect(route('view.detail-order-tour', ['id' => 1]));

        $this->assertSame(1, DB::table('orders')->count());
    }

    public function test_tour_order_rejects_logically_deleted_price(): void
    {
        DB::table('tour_prices')->where('id', 1)->update([
            'deleted_at' => now(),
        ]);

        $this->actingAs($this->actingUser())
            ->from('/tour/test-tour')
            ->post(route('func.order-tour-package.create', ['id' => 1]), $this->tourPayload())
            ->assertSessionHasErrors('tour_price_id');

        $this->assertSame(0, DB::table('orders')->count());
    }

    public function test_tour_quote_fails_closed_when_usd_sell_rate_is_stale(): void
    {
        DB::table('usd_rates')->where('name', 'USD')->update([
            'retrieved_at' => now()->subHours(25),
        ]);

        $this->postJson(route('tour-package.quote', ['tour' => 1]), [
            'number_of_guests' => 2,
            'tour_price_id' => 1,
            'travel_date' => '2026-08-10',
        ])
            ->assertUnprocessable()
            ->assertJson([
                'price_available' => false,
                'code' => 'PRICING_RATE_STALE',
                'message' => 'Price temporarily unavailable',
            ]);
    }

    public function test_tour_quote_success_returns_review_price_contract_without_resource_wrapper(): void
    {
        $this->actingAs($this->actingUser())
            ->postJson(route('tour-package.quote', ['tour' => 1]), [
                'number_of_guests' => 2,
                'travel_date' => '2026-08-10 09:00',
            ])
            ->assertOk()
            ->assertJsonPath('price_available', true)
            ->assertJsonPath('quote.price_id', 1)
            ->assertJsonPath('display.unit_price_usd', '150.00')
            ->assertJsonPath('display.final_total_usd', '300.00')
            ->assertJsonMissingPath('data');
    }

    public function test_tour_order_validates_minimum_participants_from_backend_rules(): void
    {
        $payload = $this->tourPayload([
            'guests' => [
                [
                    'name' => 'First Guest',
                    'phone' => '+628123456',
                    'age' => 'Adult',
                    'sex' => 'Male',
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
                'guests' => array_merge($this->tourPayload()['guests'], [
                    [
                        'name' => 'Third Guest',
                        'phone' => '+628778',
                        'age' => 'Adult',
                        'sex' => 'Male',
                    ],
                    [
                        'name' => 'Fourth Guest',
                        'phone' => '+628779',
                        'age' => 'Adult',
                        'sex' => 'Female',
                    ],
                    [
                        'name' => 'Fifth Guest',
                        'phone' => '+628780',
                        'age' => 'Adult',
                        'sex' => 'Male',
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
        Storage::disk('private')->put(
            app(AccommodationFinancialFileService::class)->privateInvoicePath($order, $invoice, 'zh-CN'),
            "%PDF-1.4\n% simplified chinese tour invoice\n"
        );

        $invoiceResponse = $this->actingAs($owner)
            ->get(route('orders.accommodation.invoice.preview', ['order' => $order->id, 'locale' => 'en']))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->assertStringContainsString('private', $invoiceResponse->headers->get('Cache-Control'));

        $this->actingAs($owner)
            ->get(route('orders.accommodation.invoice.preview', ['order' => $order->id, 'locale' => 'zh-CN']))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->actingAs($nonOwner)
            ->get(route('orders.accommodation.invoice.preview', ['order' => $order->id, 'locale' => 'en']))
            ->assertNotFound();
    }

    public function test_tour_detail_uses_canonical_actions_and_payment_confirmation_fields(): void
    {
        $owner = $this->actingUser(12);
        $order = $this->insertApprovedTourOrder($owner);

        InvoiceAdmin::create([
            'rsv_id' => $order->rsv_id,
            'inv_no' => 'TOUR-INV-ACTIONS',
            'due_date' => now()->addDay(),
            'balance' => 300,
            'bank_id' => 1,
            'currency_id' => 1,
            'total_usd' => 300,
        ]);

        $this->actingAs($owner)
            ->get(route('view.detail-order-tour', ['id' => $order->id]))
            ->assertOk()
            ->assertSee('ui-btn ui-btn--primary ui-btn--block', false)
            ->assertSee('name="payment_standard_version" value="1"', false)
            ->assertSee('name="payment_date"', false)
            ->assertSee('name="amount_paid"', false)
            ->assertSee('name="receipt_file"', false)
            ->assertDontSee('name="order_id"', false);
    }

    public function test_customer_delete_tour_uses_deleted_lifecycle_and_audit_log(): void
    {
        $owner = $this->actingUser(13);
        $order = $this->insertApprovedTourOrder($owner, ['status' => 'Draft']);

        $this->actingAs($owner)
            ->delete(route('func.delete-order', ['id' => $order->id]))
            ->assertRedirect(route('view.orders'));

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'Deleted']);
        $this->assertDatabaseHas('reservations', ['id' => $order->rsv_id, 'status' => 'Canceled']);
        $this->assertDatabaseHas('order_logs', [
            'order_id' => $order->id,
            'action' => 'Delete Tour Order',
            'method' => 'Update',
            'admin' => $owner->id,
        ]);
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
            'tour_price_id' => 1,
            'travel_date' => '2026-08-10',
            'pickup_location' => 'Hotel Lobby',
            'dropoff_location' => 'Hotel Lobby',
            'terms_accepted' => '1',
            'guests' => [
                [
                    'name' => 'First Guest',
                    'phone' => '+628123456',
                    'age' => 'Adult',
                    'sex' => 'Male',
                ],
                [
                    'name' => 'Second Guest',
                    'phone' => '+628777',
                    'age' => 'Adult',
                    'sex' => 'Female',
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
            'phone' => '+628123456',
            'office' => 'Tour Office',
            'address' => 'Test Address',
            'country' => 'Indonesia',
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
            'order_notes',
            'user_logs',
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
            'order_pricing_snapshots',
            'tax_policies',
            'booking_codes',
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
            $table->string('phone')->nullable();
            $table->string('office')->nullable();
            $table->string('address')->nullable();
            $table->string('country')->nullable();
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

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default('Active');
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

        Schema::create('booking_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->unsignedInteger('used')->default(0);
            $table->unsignedInteger('amount')->default(1);
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
            $table->longText('itinerary_traditional')->nullable();
            $table->longText('itinerary_simplified')->nullable();
            $table->longText('include')->nullable();
            $table->longText('exclude')->nullable();
            $table->longText('additional_info')->nullable();
            $table->longText('additional_info_traditional')->nullable();
            $table->longText('additional_info_simplified')->nullable();
            $table->longText('cancellation_policy')->nullable();
            $table->string('status')->nullable();
            $table->softDeletes();
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
            $table->softDeletes();
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
            $table->decimal('discounts', 14, 2)->nullable();
            $table->string('bookingcode')->nullable();
            $table->decimal('bookingcode_disc', 14, 2)->nullable();
            $table->string('promotion')->nullable();
            $table->decimal('promotion_disc', 14, 2)->nullable();
            $table->decimal('order_tax', 14, 2)->nullable();
            $table->decimal('final_price', 14, 2)->default(0);
            $table->decimal('usd_rate', 14, 2)->nullable();
            $table->decimal('cny_rate', 14, 2)->nullable();
            $table->decimal('twd_rate', 14, 2)->nullable();
            $table->unsignedBigInteger('sales_agent')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->unsignedBigInteger('handled_by')->nullable();
            $table->dateTime('handled_date')->nullable();
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

        Schema::create('order_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('order_wedding_id')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('status')->nullable();
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
            $table->string('name_mandarin')->nullable();
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
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->dateTime('inv_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->decimal('total_usd', 14, 2)->default(0);
            $table->decimal('total_idr', 16, 2)->default(0);
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

        foreach ([
            '2026_07_29_170000_add_pricing_shadow_fields_to_tour_prices_table.php',
            '2026_07_29_170100_add_retrieval_metadata_to_usd_rates_table.php',
            '2026_07_29_170200_create_tax_policies_table.php',
            '2026_07_29_170300_create_order_pricing_snapshots_table.php',
            '2026_07_29_170400_add_pricing_summary_to_orders_table.php',
            '2026_07_29_170500_add_pricing_metadata_to_tour_discounts.php',
            '2026_07_29_170600_add_tour_order_idempotency_to_orders_table.php',
            '2026_07_31_120000_add_valid_from_to_tour_prices_table.php',
            '2026_07_31_130000_add_markup_type_to_tour_prices_table.php',
            '2026_07_31_140000_store_tour_price_markup_as_exact_numeric_string.php',
        ] as $migration) {
            (require database_path('migrations/'.$migration))->up();
        }
    }

    private function seedReferenceRows(): void
    {
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Pricing Approver',
            'email' => 'pricing@example.test',
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('taxes')->insert(['id' => 1, 'name' => 'tax', 'tax' => 0]);
        foreach ([['USD', 15000], ['CNY', 2000], ['TWD', 500]] as $index => [$name, $rate]) {
            DB::table('usd_rates')->insert([
                'id' => $index + 1,
                'name' => $name,
                'rate' => $rate,
                'sell' => $rate,
                'retrieved_at' => now()->subHour(),
                'retrieval_source' => 'test-fixture',
            ]);
        }
        DB::table('tax_policies')->insert([
            'id' => 1,
            'service' => 'Tour Package',
            'name' => 'Tour Tax Fixture',
            'percentage_scaled' => 0,
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
            'contract_rate_idr' => 1500000,
            'markup_amount' => '50.000000',
            'markup_currency' => 'USD',
            'markup_source' => 'test-fixture',
            'markup_verified_at' => now(),
            'markup_verified_by' => 1,
            'pricing_data_status' => 'ready',
            'valid_from' => now()->subDay()->toDateString(),
            'expired_date' => '2026-12-31',
            'valid_until' => '2026-12-31',
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
            'contract_rate_idr' => 1000000,
            'markup_amount' => '10.000000',
            'markup_currency' => 'USD',
            'markup_source' => 'test-fixture',
            'markup_verified_at' => now(),
            'markup_verified_by' => 1,
            'pricing_data_status' => 'ready',
            'valid_from' => now()->subDay()->toDateString(),
            'expired_date' => '2026-12-31',
            'valid_until' => '2026-12-31',
            'status' => 'Active',
        ]);
    }
}
