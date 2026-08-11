<?php

namespace Tests\Feature;

use App\Http\Middleware\LogActivityMiddleware;
use App\Http\Middleware\TrackWebsiteVisit;
use App\Models\InvoiceAdmin;
use App\Models\Orders;
use App\Services\AccommodationFinancialFileService;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccommodationPaymentAuthorizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!$this->usingSafeSqliteMemoryDatabase()) {
            $this->markTestSkipped('Accommodation payment authorization tests require sqlite :memory: to avoid touching active data.');
        }

        $this->withoutMiddleware([
            LogActivityMiddleware::class,
            TrackWebsiteVisit::class,
        ]);

        Cache::flush();
        config(['filesystems.default' => 'private']);

        $this->prepareSchema();
    }

    private function usingSafeSqliteMemoryDatabase(): bool
    {
        return config('database.default') === 'sqlite'
            && config('database.connections.sqlite.database') === ':memory:';
    }

    private function prepareSchema(): void
    {
        foreach ([
            'order_logs',
            'payment_confirmations',
            'invoice_admins',
            'optional_rate_orders',
            'airport_shuttles',
            'optional_rates',
            'hotel_rooms',
            'hotels',
            'footer_links',
            'footer_settings',
            'business_profiles',
            'taxes',
            'usd_rates',
            'reservations',
            'orders',
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
            $table->boolean('is_approved')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('session_id')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('orderno')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('sales_agent')->nullable();
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('subservice_id')->nullable();
            $table->unsignedBigInteger('handled_by')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('service')->nullable();
            $table->string('servicename')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->date('checkin')->nullable();
            $table->date('checkout')->nullable();
            $table->integer('duration')->nullable();
            $table->decimal('final_price', 14, 2)->nullable();
            $table->decimal('bookingcode_disc', 14, 2)->nullable();
            $table->decimal('discounts', 14, 2)->nullable();
            $table->text('promotion_disc')->nullable();
            $table->text('number_of_guests_room')->nullable();
            $table->text('guest_detail')->nullable();
            $table->text('special_day')->nullable();
            $table->text('special_date')->nullable();
            $table->text('extra_bed')->nullable();
            $table->text('extra_bed_price')->nullable();
            $table->text('extra_bed_total_price')->nullable();
            $table->text('additional_service')->nullable();
            $table->text('additional_service_date')->nullable();
            $table->text('additional_service_qty')->nullable();
            $table->text('additional_service_price')->nullable();
            $table->text('msg')->nullable();
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('rsv_no')->nullable();
            $table->string('status')->nullable();
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

        Schema::create('invoice_admins', function (Blueprint $table) {
            $table->id();
            $table->string('inv_no')->nullable();
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('total_usd', 14, 2)->default(0);
            $table->decimal('total_idr', 14, 2)->default(0);
            $table->decimal('total_cny', 14, 2)->default(0);
            $table->decimal('total_twd', 14, 2)->default(0);
            $table->decimal('rate_usd', 14, 2)->default(1);
            $table->decimal('sell_usd', 14, 2)->default(1);
            $table->decimal('rate_twd', 14, 2)->default(1);
            $table->decimal('sell_twd', 14, 2)->default(1);
            $table->decimal('rate_cny', 14, 2)->default(1);
            $table->decimal('sell_cny', 14, 2)->default(1);
            $table->decimal('balance', 14, 2)->default(0);
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_confirmations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inv_id')->nullable();
            $table->unsignedBigInteger('kurs_id')->nullable();
            $table->string('receipt_img')->nullable();
            $table->string('status')->nullable();
            $table->decimal('amount', 14, 2)->nullable();
            $table->date('payment_date')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('order_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('order_wedding_id')->nullable();
            $table->string('action')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable();
            $table->string('agent')->nullable();
            $table->unsignedBigInteger('admin')->nullable();
            $table->timestamps();
        });

        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->decimal('tax', 8, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('profile_key')->nullable();
            $table->string('name')->nullable();
            $table->string('nickname')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_2')->nullable();
            $table->string('phone_3')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('logo_dark')->nullable();
            $table->string('public_tagline')->nullable();
            $table->text('public_description')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('linkedin')->nullable();
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
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('open_new_tab')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotels_id')->nullable();
            $table->string('rooms')->nullable();
            $table->unsignedInteger('inventory')->nullable();
            $table->timestamps();
        });

        Schema::create('optional_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotels_id')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
        });

        Schema::create('airport_shuttles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->decimal('price', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('optional_rate_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->decimal('price_total', 14, 2)->default(0);
            $table->timestamps();
        });

        $this->seedReferenceRows();
    }

    private function seedReferenceRows(): void
    {
        foreach ([
            [1, 'USD', 15000, 15000],
            [2, 'CNY', 2100, 2100],
            [3, 'TWD', 500, 500],
            [4, 'IDR', 1, 1],
        ] as [$id, $name, $rate, $sell]) {
            DB::table('usd_rates')->insert([
                'id' => $id,
                'name' => $name,
                'rate' => $rate,
                'sell' => $sell,
                'buy' => $rate,
                'difference' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('taxes')->insert(['id' => 1, 'tax' => 0, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('business_profiles')->insert([
            'id' => 1,
            'profile_key' => 'primary',
            'name' => 'Bali Kami Tour',
            'nickname' => 'Bali Kami Tour',
            'email' => 'reservation@example.test',
            'website' => 'example.test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('hotels')->insert(['id' => 1, 'name' => 'Regression Hotel', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('hotel_rooms')->insert(['id' => 1, 'hotels_id' => 1, 'rooms' => 'Suite', 'created_at' => now(), 'updated_at' => now()]);
    }

    private function actingUser(int $id, string $position = 'agent', string $type = 'user'): User
    {
        DB::table('users')->updateOrInsert(
            ['id' => $id],
            [
                'name' => 'User '.$id,
                'email' => 'user'.$id.'@example.test',
                'password' => 'test-password-hash',
                'type' => $type,
                'position' => $position,
                'is_approved' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return User::findOrFail($id);
    }

    private function createAccommodationOrder(int $ownerId, array $overrides = []): array
    {
        $service = $overrides['service'] ?? 'Hotel Promo';
        $status = $overrides['status'] ?? 'Approved';
        $balance = $overrides['balance'] ?? 100;
        $reservationStatus = $overrides['reservation_status'] ?? 'Active';

        $reservationId = DB::table('reservations')->insertGetId([
            'rsv_no' => $overrides['rsv_no'] ?? 'RSV-'.$ownerId.'-'.str_replace(' ', '-', $service).'-'.uniqid(),
            'status' => $reservationStatus,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $orderId = DB::table('orders')->insertGetId([
            'orderno' => $overrides['orderno'] ?? 'ORD-'.$ownerId.'-'.$reservationId,
            'user_id' => $overrides['user_id'] ?? $ownerId,
            'sales_agent' => $ownerId,
            'rsv_id' => $reservationId,
            'service_id' => $overrides['service_id'] ?? 1,
            'subservice_id' => $overrides['subservice_id'] ?? 1,
            'handled_by' => $overrides['handled_by'] ?? null,
            'name' => $overrides['name'] ?? 'Owner '.$ownerId,
            'email' => 'owner'.$ownerId.'@example.test',
            'service' => $service,
            'servicename' => $overrides['servicename'] ?? 'Regression Hotel',
            'status' => $status,
            'completed_at' => $overrides['completed_at'] ?? null,
            'completed_by' => $overrides['completed_by'] ?? null,
            'checkin' => $overrides['checkin'] ?? now()->addDays(30)->toDateString(),
            'checkout' => $overrides['checkout'] ?? now()->addDays(32)->toDateString(),
            'duration' => 2,
            'final_price' => 100,
            'bookingcode_disc' => 0,
            'discounts' => 0,
            'promotion_disc' => json_encode([]),
            'number_of_guests_room' => json_encode([]),
            'guest_detail' => json_encode([]),
            'special_day' => json_encode([]),
            'special_date' => json_encode([]),
            'extra_bed' => json_encode([]),
            'extra_bed_price' => json_encode([]),
            'extra_bed_total_price' => json_encode([]),
            'additional_service' => json_encode([]),
            'additional_service_date' => json_encode([]),
            'additional_service_qty' => json_encode([]),
            'additional_service_price' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $invoiceId = DB::table('invoice_admins')->insertGetId([
            'inv_no' => $overrides['inv_no'] ?? 'INV-'.$orderId,
            'rsv_id' => $reservationId,
            'due_date' => $overrides['due_date'] ?? now()->addDay()->toDateString(),
            'total_usd' => 100,
            'rate_usd' => 1,
            'sell_usd' => 1,
            'rate_twd' => 1,
            'sell_twd' => 1,
            'rate_cny' => 1,
            'sell_cny' => 1,
            'balance' => $balance,
            'currency_id' => $overrides['currency_id'] ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'order_id' => $orderId,
            'reservation_id' => $reservationId,
            'invoice_id' => $invoiceId,
        ];
    }

    private function createReceipt(int $invoiceId, string $status = 'Pending', string $filename = 'existing.jpg'): int
    {
        return DB::table('payment_confirmations')->insertGetId([
            'inv_id' => $invoiceId,
            'receipt_img' => $filename,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function validUpload(string $name = 'receipt.jpg'): UploadedFile
    {
        if (str_ends_with($name, '.pdf')) {
            return UploadedFile::fake()->createWithContent($name, "%PDF-1.4\n% test receipt\n");
        }

        return UploadedFile::fake()->image($name);
    }

    private function fakeFinancialStorage(): void
    {
        Storage::fake('private');
        Storage::fake('public');
    }

    private function privateReceiptPath(int $orderId, string $filename): string
    {
        return AccommodationFinancialFileService::PAYMENT_ROOT.'/'.$orderId.'/'.$filename;
    }

    private function postCustomerUpload(int $orderId, UploadedFile $file)
    {
        return $this->post(route('upload.payment-confirmation', ['id' => $orderId]), [
            'receipt_name' => $file,
        ]);
    }

    private function putCustomerUpdate(int $orderId, UploadedFile $file, array $extra = [])
    {
        return $this->put(route('update-payment-confirmation', ['id' => $orderId]), array_merge($extra, [
            'activity_receipt_name' => $file,
        ]));
    }

    private function postAdminValidation(int $receiptId, array $payload = [])
    {
        return $this->post(route('admin.confirm.receipt', ['id' => $receiptId]), array_merge([
            'status' => 'Valid',
            'amount' => 100,
            'kurs_id' => 1,
            'payment_date' => Carbon::today()->toDateString(),
            'note' => '<b>ok</b>',
        ], $payload));
    }

    private function putPrivateReceipt(int $orderId, int $invoiceId, string $filename = 'receipt.jpg', string $status = 'Pending'): int
    {
        $path = $this->privateReceiptPath($orderId, $filename);
        Storage::disk('private')->put($path, $this->fileContentsFor($filename));

        return $this->createReceipt($invoiceId, $status, $path);
    }

    private function putLegacyReceipt(int $invoiceId, string $filename = 'legacy.jpg', string $status = 'Pending'): int
    {
        Storage::disk('public')->put('receipt/'.$filename, $this->fileContentsFor($filename));

        return $this->createReceipt($invoiceId, $status, $filename);
    }

    private function putPrivateInvoice(int $orderId, int $invoiceId, string $locale = 'en'): string
    {
        $order = Orders::findOrFail($orderId);
        $invoice = InvoiceAdmin::findOrFail($invoiceId);
        $path = app(AccommodationFinancialFileService::class)->privateInvoicePath($order, $invoice, $locale);

        Storage::disk('private')->put($path, "%PDF-1.4\n% accommodation invoice\n");

        return $path;
    }

    private function putLegacyInvoice(int $orderId, int $invoiceId, string $locale = 'en'): string
    {
        $invoice = DB::table('invoice_admins')->where('id', $invoiceId)->first();
        $filename = 'invoice-'.$invoice->inv_no.'-'.$orderId.'_'.$locale.'.pdf';

        Storage::disk('public')->put('document/'.$filename, "%PDF-1.4\n% legacy invoice\n");

        return $filename;
    }

    private function fileContentsFor(string $filename): string
    {
        if (str_ends_with($filename, '.pdf')) {
            return "%PDF-1.4\n% receipt\n";
        }

        $file = UploadedFile::fake()->image($filename);

        return file_get_contents($file->getRealPath());
    }

    public function test_owner_can_open_hotel_detail_with_payment_form(): void
    {
        $fixture = $this->createAccommodationOrder(10);

        $response = $this->actingAs($this->actingUser(10))->get(route('view.detail-order-hotel', $fixture['order_id']));

        $response->assertOk();
        $response->assertSee('payment-confirm-'.$fixture['order_id'], false);
        $response->assertSee(route('upload.payment-confirmation', ['id' => $fixture['order_id']]), false);
    }

    public function test_guest_is_redirected_to_login_before_uploading_payment(): void
    {
        $fixture = $this->createAccommodationOrder(11);

        $response = $this->postCustomerUpload($fixture['order_id'], $this->validUpload());

        $response->assertRedirect('/login');
        $this->assertSame(0, DB::table('payment_confirmations')->count());
    }

    public function test_non_owner_payment_upload_is_404_and_does_not_auto_cancel_or_write_side_effects(): void
    {
        $this->fakeFinancialStorage();
        Mail::fake();
        $fixture = $this->createAccommodationOrder(20, [
            'due_date' => now()->subDay()->toDateString(),
        ]);

        $response = $this->actingAs($this->actingUser(21))->postCustomerUpload(
            $fixture['order_id'],
            $this->validUpload('receipt.jpg')
        );

        $response->assertNotFound();
        $this->assertSame('Approved', DB::table('orders')->where('id', $fixture['order_id'])->value('status'));
        $this->assertSame('Active', DB::table('reservations')->where('id', $fixture['reservation_id'])->value('status'));
        $this->assertEquals(100, DB::table('invoice_admins')->where('id', $fixture['invoice_id'])->value('balance'));
        $this->assertSame(0, DB::table('payment_confirmations')->count());
        $this->assertSame(0, DB::table('order_logs')->count());
        $this->assertSame([], Storage::disk('private')->allFiles(AccommodationFinancialFileService::PAYMENT_ROOT));
        $this->assertSame([], Storage::disk('public')->allFiles('receipt'));
        Mail::assertNothingSent();
    }

    public function test_customer_payment_route_rejects_unsupported_public_services(): void
    {
        $this->fakeFinancialStorage();

        foreach (['Private Villa'] as $service) {
            $fixture = $this->createAccommodationOrder(30, ['service' => $service]);

            $response = $this->actingAs($this->actingUser(30))->postCustomerUpload(
                $fixture['order_id'],
                $this->validUpload($service.'.jpg')
            );

            $response->assertNotFound();
        }

        $this->assertSame(0, DB::table('payment_confirmations')->count());
        $this->assertSame(0, DB::table('order_logs')->count());
        $this->assertSame([], Storage::disk('public')->allFiles('receipt'));
    }

    public function test_owner_can_upload_receipt_for_approved_order_with_outstanding_invoice(): void
    {
        $this->fakeFinancialStorage();
        Mail::fake();
        $fixture = $this->createAccommodationOrder(40, [
            'inv_no' => 'INV/unsafe number',
            'balance' => 100,
        ]);

        $response = $this->actingAs($this->actingUser(40))->postCustomerUpload(
            $fixture['order_id'],
            $this->validUpload('unsafe original name.jpg')
        );

        $response->assertRedirect('/detail-order-hotel/'.$fixture['order_id']);
        $receipt = DB::table('payment_confirmations')->first();

        $this->assertNotNull($receipt);
        $this->assertSame(1, (int) $receipt->kurs_id);
        $this->assertSame('Pending', $receipt->status);
        $this->assertStringStartsWith($this->privateReceiptPath($fixture['order_id'], 'INVunsafenumber_'), $receipt->receipt_img);
        $this->assertStringNotContainsString('unsafe original name', $receipt->receipt_img);
        $this->assertStringNotContainsString('..', $receipt->receipt_img);
        $this->assertFalse(str_starts_with($receipt->receipt_img, '/'));
        Storage::disk('private')->assertExists($receipt->receipt_img);
        Storage::disk('public')->assertMissing('receipt/'.basename($receipt->receipt_img));
        $this->assertDatabaseHas('order_logs', [
            'order_id' => $fixture['order_id'],
            'action' => 'Upload Receipt',
            'method' => 'Upload',
            'admin' => 40,
        ]);
    }

    public function test_canonical_payment_submission_stores_reported_date_amount_and_invoice_currency(): void
    {
        $this->fakeFinancialStorage();
        Mail::fake();
        $fixture = $this->createAccommodationOrder(41, ['balance' => 100]);

        $response = $this->actingAs($this->actingUser(41))->post(
            route('upload.payment-confirmation', ['id' => $fixture['order_id']]),
            [
                'payment_standard_version' => '1',
                'payment_date' => '2026-08-02',
                'amount_paid' => '40.50',
                'receipt_file' => $this->validUpload('canonical.pdf'),
            ]
        );

        $response->assertRedirect('/detail-order-hotel/'.$fixture['order_id']);
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('payment_confirmations', [
            'inv_id' => $fixture['invoice_id'],
            'kurs_id' => 1,
            'payment_date' => '2026-08-02',
            'amount' => '40.5',
            'status' => 'Pending',
        ]);
        $this->assertEquals(100, DB::table('invoice_admins')->where('id', $fixture['invoice_id'])->value('balance'));
    }

    public function test_customer_cannot_create_a_second_pending_payment_confirmation(): void
    {
        $this->fakeFinancialStorage();
        Mail::fake();
        $fixture = $this->createAccommodationOrder(42, ['balance' => 100]);
        $this->createReceipt($fixture['invoice_id'], 'Pending', 'existing.jpg');

        $response = $this->actingAs($this->actingUser(42))
            ->from('/detail-order-hotel/'.$fixture['order_id'])
            ->post(route('upload.payment-confirmation', ['id' => $fixture['order_id']]), [
                'payment_standard_version' => '1',
                'payment_date' => '2026-08-02',
                'amount_paid' => '100',
                'receipt_file' => $this->validUpload('duplicate.jpg'),
            ]);

        $response->assertRedirect('/detail-order-hotel/'.$fixture['order_id']);
        $response->assertSessionHasErrors('receipt_file');
        $this->assertSame(1, DB::table('payment_confirmations')->where('inv_id', $fixture['invoice_id'])->count());
        $this->assertSame([], Storage::disk('private')->allFiles(AccommodationFinancialFileService::PAYMENT_ROOT));
    }

    public function test_canonical_payment_submission_rejects_future_date_and_amount_above_balance(): void
    {
        $this->fakeFinancialStorage();
        $fixture = $this->createAccommodationOrder(43, ['balance' => 100]);

        $response = $this->actingAs($this->actingUser(43))
            ->from('/detail-order-hotel/'.$fixture['order_id'])
            ->post(route('upload.payment-confirmation', ['id' => $fixture['order_id']]), [
                'payment_standard_version' => '1',
                'payment_date' => now()->addDay()->toDateString(),
                'amount_paid' => '101',
                'receipt_file' => $this->validUpload('invalid-claim.jpg'),
            ]);

        $response->assertRedirect('/detail-order-hotel/'.$fixture['order_id']);
        $response->assertSessionHasErrors('payment_date');

        $amountResponse = $this->actingAs($this->actingUser(43))
            ->from('/detail-order-hotel/'.$fixture['order_id'])
            ->post(route('upload.payment-confirmation', ['id' => $fixture['order_id']]), [
                'payment_standard_version' => '1',
                'payment_date' => now()->toDateString(),
                'amount_paid' => '101',
                'receipt_file' => $this->validUpload('amount-above-balance.jpg'),
            ]);

        $amountResponse->assertRedirect('/detail-order-hotel/'.$fixture['order_id']);
        $amountResponse->assertSessionHasErrors('amount_paid');
        $this->assertSame(0, DB::table('payment_confirmations')->count());
    }

    public function test_customer_upload_status_matrix_only_allows_approved_with_outstanding_balance(): void
    {
        $this->fakeFinancialStorage();
        Mail::fake();

        foreach (['Draft', 'Pending', 'Paid', 'Canceled', 'Rejected', 'Invalid', 'Deleted'] as $status) {
            $fixture = $this->createAccommodationOrder(50, [
                'status' => $status,
                'balance' => $status === 'Paid' ? 0 : 100,
            ]);

            $beforePayments = DB::table('payment_confirmations')->count();
            $beforeLogs = DB::table('order_logs')->count();

            $response = $this->actingAs($this->actingUser(50))->postCustomerUpload(
                $fixture['order_id'],
                $this->validUpload($status.'.jpg')
            );

            $response->assertRedirect('/detail-order-hotel/'.$fixture['order_id']);
            $response->assertSessionHas('error');
            $this->assertSame($beforePayments, DB::table('payment_confirmations')->count(), $status);
            $this->assertSame($beforeLogs, DB::table('order_logs')->count(), $status);
        }

        $zeroBalance = $this->createAccommodationOrder(50, ['status' => 'Approved', 'balance' => 0]);
        $response = $this->actingAs($this->actingUser(50))->postCustomerUpload(
            $zeroBalance['order_id'],
            $this->validUpload('approved-zero.jpg')
        );

        $response->assertRedirect('/detail-order-hotel/'.$zeroBalance['order_id']);
        $response->assertSessionHas('error');
        $this->assertSame(0, DB::table('payment_confirmations')->count());
        $this->assertSame([], Storage::disk('private')->allFiles(AccommodationFinancialFileService::PAYMENT_ROOT));
        $this->assertSame([], Storage::disk('public')->allFiles('receipt'));
    }

    public function test_partial_payment_order_can_submit_another_pending_receipt_without_changing_balance(): void
    {
        $this->fakeFinancialStorage();
        Mail::fake();
        $fixture = $this->createAccommodationOrder(60, ['status' => 'Approved', 'balance' => 40]);
        $this->createReceipt($fixture['invoice_id'], 'Valid', 'previous-valid.pdf');

        $response = $this->actingAs($this->actingUser(60))->postCustomerUpload(
            $fixture['order_id'],
            $this->validUpload('partial.pdf')
        );

        $response->assertRedirect('/detail-order-hotel/'.$fixture['order_id']);
        $this->assertSame('Approved', DB::table('orders')->where('id', $fixture['order_id'])->value('status'));
        $this->assertEquals(40, DB::table('invoice_admins')->where('id', $fixture['invoice_id'])->value('balance'));
        $this->assertSame(2, DB::table('payment_confirmations')->where('inv_id', $fixture['invoice_id'])->count());
        $this->assertSame(1, DB::table('payment_confirmations')->where('status', 'Pending')->count());
    }

    public function test_owner_can_replace_pending_receipt_and_old_file_is_removed(): void
    {
        $this->fakeFinancialStorage();
        $fixture = $this->createAccommodationOrder(70);
        $oldReceiptPath = $this->privateReceiptPath($fixture['order_id'], 'old-receipt.jpg');
        $receiptId = $this->createReceipt($fixture['invoice_id'], 'Pending', $oldReceiptPath);
        Storage::disk('private')->put($oldReceiptPath, 'old');

        $response = $this->actingAs($this->actingUser(70))->putCustomerUpdate(
            $fixture['order_id'],
            $this->validUpload('new-receipt.png')
        );

        $response->assertRedirect('/detail-order-hotel/'.$fixture['order_id']);
        $receipt = DB::table('payment_confirmations')->where('id', $receiptId)->first();

        $this->assertSame('Pending', $receipt->status);
        $this->assertNull($receipt->note);
        $this->assertNotSame($oldReceiptPath, $receipt->receipt_img);
        Storage::disk('private')->assertMissing($oldReceiptPath);
        Storage::disk('private')->assertExists($receipt->receipt_img);
        Storage::disk('public')->assertMissing('receipt/'.basename($receipt->receipt_img));
        $this->assertDatabaseHas('order_logs', [
            'order_id' => $fixture['order_id'],
            'action' => 'Change Receipt',
            'method' => 'Update',
            'admin' => 70,
        ]);
    }

    public function test_non_owner_cannot_replace_pending_receipt_or_create_files(): void
    {
        $this->fakeFinancialStorage();
        $fixture = $this->createAccommodationOrder(80);
        $oldReceiptPath = $this->privateReceiptPath($fixture['order_id'], 'owner-receipt.jpg');
        $receiptId = $this->createReceipt($fixture['invoice_id'], 'Pending', $oldReceiptPath);
        Storage::disk('private')->put($oldReceiptPath, 'old');

        $response = $this->actingAs($this->actingUser(81))->putCustomerUpdate(
            $fixture['order_id'],
            $this->validUpload('attacker.png')
        );

        $response->assertNotFound();
        $this->assertSame($oldReceiptPath, DB::table('payment_confirmations')->where('id', $receiptId)->value('receipt_img'));
        $this->assertSame(0, DB::table('order_logs')->count());
        $this->assertSame([$oldReceiptPath], Storage::disk('private')->allFiles(AccommodationFinancialFileService::PAYMENT_ROOT));
        $this->assertSame([], Storage::disk('public')->allFiles('receipt'));
    }

    public function test_valid_or_invalid_customer_receipt_cannot_be_replaced(): void
    {
        $this->fakeFinancialStorage();

        foreach (['Valid', 'Invalid'] as $status) {
            $fixture = $this->createAccommodationOrder(90, ['orderno' => 'ORD-'.$status]);
            $oldReceiptPath = $this->privateReceiptPath($fixture['order_id'], $status.'-receipt.jpg');
            $receiptId = $this->createReceipt($fixture['invoice_id'], $status, $oldReceiptPath);
            Storage::disk('private')->put($oldReceiptPath, 'old');

            $response = $this->actingAs($this->actingUser(90))->putCustomerUpdate(
                $fixture['order_id'],
                $this->validUpload($status.'-new.jpg')
            );

            $response->assertRedirect('/detail-order-hotel/'.$fixture['order_id']);
            $response->assertSessionHas('error');
            $this->assertSame($oldReceiptPath, DB::table('payment_confirmations')->where('id', $receiptId)->value('receipt_img'));
        }

        $this->assertSame(0, DB::table('order_logs')->count());
    }

    public function test_update_ignores_request_invoice_or_receipt_ids_from_other_order(): void
    {
        $this->fakeFinancialStorage();
        $first = $this->createAccommodationOrder(100, ['orderno' => 'ORD-A']);
        $second = $this->createAccommodationOrder(100, ['orderno' => 'ORD-B']);
        $firstOldPath = $this->privateReceiptPath($first['order_id'], 'first-old.jpg');
        $secondOldPath = $this->privateReceiptPath($second['order_id'], 'second-old.jpg');
        $firstReceiptId = $this->createReceipt($first['invoice_id'], 'Pending', $firstOldPath);
        $secondReceiptId = $this->createReceipt($second['invoice_id'], 'Pending', $secondOldPath);
        Storage::disk('private')->put($firstOldPath, 'first');
        Storage::disk('private')->put($secondOldPath, 'second');

        $response = $this->actingAs($this->actingUser(100))->putCustomerUpdate(
            $first['order_id'],
            $this->validUpload('replacement.pdf'),
            ['invoice_id' => $second['invoice_id'], 'receipt_id' => $secondReceiptId]
        );

        $response->assertRedirect('/detail-order-hotel/'.$first['order_id']);
        $firstReceipt = DB::table('payment_confirmations')->where('id', $firstReceiptId)->first();
        $secondReceipt = DB::table('payment_confirmations')->where('id', $secondReceiptId)->first();

        $this->assertNotSame($firstOldPath, $firstReceipt->receipt_img);
        $this->assertSame($secondOldPath, $secondReceipt->receipt_img);
        Storage::disk('private')->assertMissing($firstOldPath);
        Storage::disk('private')->assertExists($secondOldPath);
    }

    public function test_customer_upload_accepts_jpg_png_and_pdf_only_when_valid(): void
    {
        $this->fakeFinancialStorage();
        Mail::fake();

        foreach (['jpg', 'png', 'pdf'] as $extension) {
            $fixture = $this->createAccommodationOrder(110, ['orderno' => 'ORD-valid-'.$extension]);

            $response = $this->actingAs($this->actingUser(110))->postCustomerUpload(
                $fixture['order_id'],
                $this->validUpload('receipt.'.$extension)
            );

            $response->assertRedirect('/detail-order-hotel/'.$fixture['order_id']);
            $this->assertSame(1, DB::table('payment_confirmations')->where('inv_id', $fixture['invoice_id'])->count(), $extension);
        }
    }

    public function test_customer_upload_rejects_unsafe_file_types_large_files_and_fake_extensions(): void
    {
        $this->fakeFinancialStorage();
        $invalidFiles = [
            UploadedFile::fake()->create('shell.php', 1, 'application/x-php'),
            UploadedFile::fake()->create('page.html', 1, 'text/html'),
            UploadedFile::fake()->create('vector.svg', 1, 'image/svg+xml'),
            UploadedFile::fake()->create('huge.pdf', 5121, 'application/pdf'),
            UploadedFile::fake()->createWithContent('fake.jpg', '<?php echo "not an image";'),
        ];

        foreach ($invalidFiles as $index => $file) {
            $fixture = $this->createAccommodationOrder(120, ['orderno' => 'ORD-invalid-'.$index]);

            $response = $this->actingAs($this->actingUser(120))
                ->from('/detail-order-hotel/'.$fixture['order_id'])
                ->postCustomerUpload($fixture['order_id'], $file);

            $response->assertRedirect('/detail-order-hotel/'.$fixture['order_id']);
            $response->assertSessionHasErrors('receipt_name');
        }

        $this->assertSame(0, DB::table('payment_confirmations')->count());
        $this->assertSame(0, DB::table('order_logs')->count());
        $this->assertSame([], Storage::disk('private')->allFiles(AccommodationFinancialFileService::PAYMENT_ROOT));
        $this->assertSame([], Storage::disk('public')->allFiles('receipt'));
    }

    public function test_admin_payment_validation_denies_customer_and_unauthorized_staff_through_endpoint(): void
    {
        $fixture = $this->createAccommodationOrder(130);
        $receiptId = $this->createReceipt($fixture['invoice_id'], 'Pending', 'pending.jpg');

        $customerResponse = $this->actingAs($this->actingUser(130, 'customer', 'user'))->postAdminValidation($receiptId);
        $customerResponse->assertRedirect('/dashboard');

        $staffResponse = $this->actingAs($this->actingUser(131, 'author', 'admin'))->postAdminValidation($receiptId);
        $staffResponse->assertRedirect('/dashboard');

        $this->assertSame('Pending', DB::table('payment_confirmations')->where('id', $receiptId)->value('status'));
        $this->assertEquals(100, DB::table('invoice_admins')->where('id', $fixture['invoice_id'])->value('balance'));
        $this->assertSame(0, DB::table('order_logs')->count());
    }

    public function test_reservation_admin_can_validate_assigned_pending_receipt(): void
    {
        $admin = $this->actingUser(140, 'reservation', 'admin');
        $fixture = $this->createAccommodationOrder(141, ['handled_by' => $admin->id]);
        $receiptId = $this->createReceipt($fixture['invoice_id'], 'Pending', 'pending.jpg');

        $response = $this->actingAs($admin)->postAdminValidation($receiptId, ['note' => '<script>alert(1)</script>ok']);

        $response->assertRedirect('/orders-admin-'.$fixture['order_id']);
        $this->assertSame('Valid', DB::table('payment_confirmations')->where('id', $receiptId)->value('status'));
        $this->assertSame('alert(1)ok', DB::table('payment_confirmations')->where('id', $receiptId)->value('note'));
        $this->assertEquals(0, DB::table('invoice_admins')->where('id', $fixture['invoice_id'])->value('balance'));
        $this->assertSame('Paid', DB::table('orders')->where('id', $fixture['order_id'])->value('status'));
        $this->assertDatabaseHas('order_logs', [
            'order_id' => $fixture['order_id'],
            'order_wedding_id' => null,
            'action' => 'Validate Payment Receipt',
            'admin' => $admin->id,
        ]);
    }

    public function test_valid_receipt_reversal_reopens_invoice_and_downgrades_paid_order_to_approved(): void
    {
        $admin = $this->actingUser(142, 'developer', 'admin');
        $fixture = $this->createAccommodationOrder(143, [
            'status' => 'Paid',
            'balance' => 0,
        ]);
        $receiptId = $this->createReceipt($fixture['invoice_id'], 'Valid', 'valid.jpg');

        $this->actingAs($admin)
            ->postAdminValidation($receiptId, [
                'status' => 'Invalid',
                'amount' => 100,
                'note' => 'duplicate receipt',
            ])
            ->assertRedirect('/orders-admin-'.$fixture['order_id']);

        $this->assertEquals(100, DB::table('invoice_admins')->where('id', $fixture['invoice_id'])->value('balance'));
        $this->assertSame('Approved', DB::table('orders')->where('id', $fixture['order_id'])->value('status'));
        $this->assertSame('Invalid', DB::table('payment_confirmations')->where('id', $receiptId)->value('status'));
    }

    public function test_completed_accommodation_payment_cannot_be_reversed_without_reopen_workflow(): void
    {
        $admin = $this->actingUser(144, 'developer', 'admin');
        $fixture = $this->createAccommodationOrder(145, [
            'status' => 'Paid',
            'completed_at' => now()->subDay(),
            'balance' => 0,
        ]);
        $receiptId = $this->createReceipt($fixture['invoice_id'], 'Valid', 'valid.jpg');

        $this->actingAs($admin)
            ->from('/orders-admin-'.$fixture['order_id'])
            ->postAdminValidation($receiptId, [
                'status' => 'Invalid',
                'amount' => 100,
            ])
            ->assertSessionHasErrors('status');

        $this->assertEquals(0, DB::table('invoice_admins')->where('id', $fixture['invoice_id'])->value('balance'));
        $this->assertSame('Paid', DB::table('orders')->where('id', $fixture['order_id'])->value('status'));
        $this->assertNotNull(DB::table('orders')->where('id', $fixture['order_id'])->value('completed_at'));
        $this->assertSame('Valid', DB::table('payment_confirmations')->where('id', $receiptId)->value('status'));
    }

    public function test_developer_admin_can_validate_even_when_order_is_handled_by_someone_else(): void
    {
        $developer = $this->actingUser(150, 'developer', 'admin');
        $fixture = $this->createAccommodationOrder(151, ['handled_by' => 999]);
        $receiptId = $this->createReceipt($fixture['invoice_id'], 'Pending', 'pending.jpg');

        $response = $this->actingAs($developer)->postAdminValidation($receiptId);

        $response->assertRedirect('/orders-admin-'.$fixture['order_id']);
        $this->assertSame('Valid', DB::table('payment_confirmations')->where('id', $receiptId)->value('status'));
    }

    public function test_reservation_admin_cannot_validate_unassigned_order_receipt(): void
    {
        $admin = $this->actingUser(160, 'reservation', 'admin');
        $fixture = $this->createAccommodationOrder(161, ['handled_by' => 999]);
        $receiptId = $this->createReceipt($fixture['invoice_id'], 'Pending', 'pending.jpg');

        $response = $this->actingAs($admin)->postAdminValidation($receiptId);

        $response->assertForbidden();
        $this->assertSame('Pending', DB::table('payment_confirmations')->where('id', $receiptId)->value('status'));
        $this->assertEquals(100, DB::table('invoice_admins')->where('id', $fixture['invoice_id'])->value('balance'));
        $this->assertSame(0, DB::table('order_logs')->count());
    }

    public function test_admin_validation_rejects_bad_payment_relation(): void
    {
        $admin = $this->actingUser(170, 'developer', 'admin');
        $receiptId = DB::table('payment_confirmations')->insertGetId([
            'inv_id' => 999999,
            'receipt_img' => 'bad.jpg',
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)->postAdminValidation($receiptId);

        $response->assertNotFound();
        $this->assertSame('Pending', DB::table('payment_confirmations')->where('id', $receiptId)->value('status'));
        $this->assertSame(0, DB::table('order_logs')->count());
    }

    public function test_admin_add_payment_receipt_requires_authorized_handler_and_safe_upload(): void
    {
        $this->fakeFinancialStorage();
        $admin = $this->actingUser(180, 'reservation', 'admin');
        $fixture = $this->createAccommodationOrder(181, [
            'handled_by' => $admin->id,
            'inv_no' => 'INV/Admin Unsafe',
        ]);

        $response = $this->actingAs($admin)->post(route('func.admin-add-payment-confirmation', $fixture['order_id']), [
            'receipt_name' => $this->validUpload('admin original.jpg'),
        ]);

        $response->assertRedirect('/orders-admin-'.$fixture['order_id']);
        $receipt = DB::table('payment_confirmations')->first();
        $this->assertSame('Pending', $receipt->status);
        $this->assertStringStartsWith($this->privateReceiptPath($fixture['order_id'], 'INVAdminUnsafe_'), $receipt->receipt_img);
        Storage::disk('private')->assertExists($receipt->receipt_img);
        Storage::disk('public')->assertMissing('receipt/'.basename($receipt->receipt_img));
    }

    public function test_admin_add_payment_receipt_denies_unassigned_handler_without_file_side_effect(): void
    {
        $this->fakeFinancialStorage();
        $admin = $this->actingUser(190, 'reservation', 'admin');
        $fixture = $this->createAccommodationOrder(191, ['handled_by' => 999]);

        $response = $this->actingAs($admin)->post(route('func.admin-add-payment-confirmation', $fixture['order_id']), [
            'receipt_name' => $this->validUpload('admin-denied.jpg'),
        ]);

        $response->assertForbidden();
        $this->assertSame(0, DB::table('payment_confirmations')->count());
        $this->assertSame(0, DB::table('order_logs')->count());
        $this->assertSame([], Storage::disk('private')->allFiles(AccommodationFinancialFileService::PAYMENT_ROOT));
        $this->assertSame([], Storage::disk('public')->allFiles('receipt'));
    }

    public function test_customer_owner_can_open_private_receipt_with_security_headers(): void
    {
        $this->fakeFinancialStorage();
        $fixture = $this->createAccommodationOrder(200);
        $receiptId = $this->putPrivateReceipt($fixture['order_id'], $fixture['invoice_id']);

        $response = $this->actingAs($this->actingUser(200))->get(route('orders.accommodation.payments.receipt', [
            'order' => $fixture['order_id'],
            'payment' => $receiptId,
        ]));

        $response->assertOk();
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition'));
    }

    public function test_customer_receipt_delivery_rejects_guest_non_owner_non_accommodation_wrong_payment_and_missing_payment(): void
    {
        $this->fakeFinancialStorage();
        $fixture = $this->createAccommodationOrder(210);
        $other = $this->createAccommodationOrder(211, ['orderno' => 'ORD-other']);
        $receiptId = $this->putPrivateReceipt($fixture['order_id'], $fixture['invoice_id']);
        $otherReceiptId = $this->putPrivateReceipt($other['order_id'], $other['invoice_id'], 'other.jpg');
        $transport = $this->createAccommodationOrder(210, ['service' => 'Transport', 'orderno' => 'ORD-transport']);
        $transportReceiptId = $this->putPrivateReceipt($transport['order_id'], $transport['invoice_id'], 'transport.jpg');

        $this->get(route('orders.accommodation.payments.receipt', ['order' => $fixture['order_id'], 'payment' => $receiptId]))
            ->assertRedirect('/login');

        $this->actingAs($this->actingUser(211))->get(route('orders.accommodation.payments.receipt', [
            'order' => $fixture['order_id'],
            'payment' => $receiptId,
        ]))->assertNotFound();

        $this->actingAs($this->actingUser(210))->get(route('orders.accommodation.payments.receipt', [
            'order' => $transport['order_id'],
            'payment' => $transportReceiptId,
        ]))->assertNotFound();

        $this->actingAs($this->actingUser(210))->get(route('orders.accommodation.payments.receipt', [
            'order' => $fixture['order_id'],
            'payment' => $otherReceiptId,
        ]))->assertNotFound();

        $this->actingAs($this->actingUser(210))->get(route('orders.accommodation.payments.receipt', [
            'order' => $fixture['order_id'],
            'payment' => 999999,
        ]))->assertNotFound();
    }

    public function test_receipt_delivery_handles_missing_and_unsafe_paths_without_leaking_filesystem_paths(): void
    {
        $this->fakeFinancialStorage();
        $fixture = $this->createAccommodationOrder(220);

        foreach ([
            AccommodationFinancialFileService::PAYMENT_ROOT.'/'.$fixture['order_id'].'/missing.jpg',
            '../receipt.jpg',
            'C:\\temp\\secret.jpg',
        ] as $index => $path) {
            $receiptId = $this->createReceipt($fixture['invoice_id'], 'Pending', $path);

            $response = $this->actingAs($this->actingUser(220))->get(route('orders.accommodation.payments.receipt', [
                'order' => $fixture['order_id'],
                'payment' => $receiptId,
            ]));

            $response->assertNotFound();
            $response->assertDontSee('storage'.DIRECTORY_SEPARATOR.'app', false);
            $response->assertDontSee('C:\\temp', false);
        }
    }

    public function test_admin_receipt_delivery_requires_backend_role_and_assignment(): void
    {
        $this->fakeFinancialStorage();
        $admin = $this->actingUser(230, 'reservation', 'admin');
        $fixture = $this->createAccommodationOrder(231, ['handled_by' => $admin->id]);
        $receiptId = $this->putPrivateReceipt($fixture['order_id'], $fixture['invoice_id']);

        $this->actingAs($admin)->get(route('admin.orders.accommodation.payments.receipt', [
            'order' => $fixture['order_id'],
            'payment' => $receiptId,
        ]))->assertOk();

        $this->actingAs($this->actingUser(231, 'customer', 'user'))->get(route('admin.orders.accommodation.payments.receipt', [
            'order' => $fixture['order_id'],
            'payment' => $receiptId,
        ]))->assertRedirect('/dashboard');

        $this->actingAs($this->actingUser(232, 'author', 'admin'))->get(route('admin.orders.accommodation.payments.receipt', [
            'order' => $fixture['order_id'],
            'payment' => $receiptId,
        ]))->assertRedirect('/dashboard');

        $this->actingAs($this->actingUser(233, 'reservation', 'admin'))->get(route('admin.orders.accommodation.payments.receipt', [
            'order' => $fixture['order_id'],
            'payment' => $receiptId,
        ]))->assertForbidden();
    }

    public function test_customer_invoice_delivery_is_guarded_to_owner_accommodation_order_and_invoice_relation(): void
    {
        $this->fakeFinancialStorage();
        $fixture = $this->createAccommodationOrder(240);
        $this->putPrivateInvoice($fixture['order_id'], $fixture['invoice_id']);
        $villa = $this->createAccommodationOrder(240, ['service' => 'Private Villa', 'orderno' => 'ORD-invoice-villa']);

        $response = $this->actingAs($this->actingUser(240))->get(route('orders.accommodation.invoice.preview', [
            'order' => $fixture['order_id'],
            'locale' => 'en',
        ]));

        $response->assertOk();
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition'));

        $this->actingAs($this->actingUser(241))->get(route('orders.accommodation.invoice.preview', [
            'order' => $fixture['order_id'],
            'locale' => 'en',
        ]))->assertNotFound();

        $this->actingAs($this->actingUser(240))->get(route('orders.accommodation.invoice.preview', [
            'order' => $villa['order_id'],
            'locale' => 'en',
        ]))->assertNotFound();
    }

    public function test_admin_invoice_delivery_requires_backend_role_and_assignment(): void
    {
        $this->fakeFinancialStorage();
        $admin = $this->actingUser(250, 'reservation', 'admin');
        $fixture = $this->createAccommodationOrder(251, ['handled_by' => $admin->id]);
        $this->putPrivateInvoice($fixture['order_id'], $fixture['invoice_id']);

        $this->actingAs($admin)->get(route('admin.orders.accommodation.invoice.download', [
            'order' => $fixture['order_id'],
            'locale' => 'en',
        ]))->assertOk();

        $this->actingAs($this->actingUser(252, 'reservation', 'admin'))->get(route('admin.orders.accommodation.invoice.download', [
            'order' => $fixture['order_id'],
            'locale' => 'en',
        ]))->assertForbidden();
    }

    public function test_legacy_known_directory_receipt_and_invoice_are_served_only_through_guarded_routes(): void
    {
        $this->fakeFinancialStorage();
        $fixture = $this->createAccommodationOrder(260);
        $receiptId = $this->putLegacyReceipt($fixture['invoice_id']);
        $this->putLegacyInvoice($fixture['order_id'], $fixture['invoice_id']);
        Storage::disk('public')->put('random/secret.jpg', $this->fileContentsFor('secret.jpg'));

        $this->actingAs($this->actingUser(260))->get(route('orders.accommodation.payments.receipt', [
            'order' => $fixture['order_id'],
            'payment' => $receiptId,
        ]))->assertOk();

        $this->actingAs($this->actingUser(260))->get(route('orders.accommodation.invoice.download', [
            'order' => $fixture['order_id'],
            'locale' => 'en',
        ]))->assertOk();

        $badReceiptId = $this->createReceipt($fixture['invoice_id'], 'Pending', '../random/secret.jpg');
        $this->actingAs($this->actingUser(260))->get(route('orders.accommodation.payments.receipt', [
            'order' => $fixture['order_id'],
            'payment' => $badReceiptId,
        ]))->assertNotFound();
    }

    public function test_hotel_detail_blade_uses_guarded_routes_instead_of_raw_storage_paths(): void
    {
        $this->fakeFinancialStorage();
        $fixture = $this->createAccommodationOrder(270);
        $receiptId = $this->putPrivateReceipt($fixture['order_id'], $fixture['invoice_id']);
        $this->putPrivateInvoice($fixture['order_id'], $fixture['invoice_id']);

        $response = $this->actingAs($this->actingUser(270))->get(route('view.detail-order-hotel', $fixture['order_id']));

        $response->assertOk();
        $response->assertSee(route('orders.accommodation.payments.receipt', [
            'order' => $fixture['order_id'],
            'payment' => $receiptId,
        ]), false);
        $response->assertSee(route('orders.accommodation.invoice.preview', [
            'order' => $fixture['order_id'],
            'locale' => 'en',
        ]), false);
        $response->assertDontSee('/storage/receipt/', false);
        $response->assertDontSee('/storage/document/', false);
    }

    public function test_new_receipt_upload_is_private_not_public_and_direct_storage_url_is_not_available(): void
    {
        $this->fakeFinancialStorage();
        Mail::fake();
        $fixture = $this->createAccommodationOrder(280);

        $response = $this->actingAs($this->actingUser(280))->postCustomerUpload(
            $fixture['order_id'],
            $this->validUpload('private-upload.jpg')
        );

        $response->assertRedirect('/detail-order-hotel/'.$fixture['order_id']);
        $receipt = DB::table('payment_confirmations')->first();

        Storage::disk('private')->assertExists($receipt->receipt_img);
        Storage::disk('public')->assertMissing('receipt/'.basename($receipt->receipt_img));
        $this->get('/storage/receipt/'.basename($receipt->receipt_img))->assertNotFound();
    }

    public function test_transaction_failure_cleans_new_private_receipt_file(): void
    {
        $this->fakeFinancialStorage();
        Mail::fake();
        $fixture = $this->createAccommodationOrder(290);
        Schema::dropIfExists('order_logs');

        try {
            $this->actingAs($this->actingUser(290))->postCustomerUpload(
                $fixture['order_id'],
                $this->validUpload('rollback.jpg')
            );
        } catch (\Throwable $exception) {
            $this->assertStringContainsString('order_logs', $exception->getMessage());
        }

        $this->assertSame(0, DB::table('payment_confirmations')->count());
        $this->assertSame([], Storage::disk('private')->allFiles(AccommodationFinancialFileService::PAYMENT_ROOT));
    }
}
