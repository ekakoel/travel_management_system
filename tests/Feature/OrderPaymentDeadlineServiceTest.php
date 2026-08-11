<?php

namespace Tests\Feature;

use App\Models\InvoiceAdmin;
use App\Models\Orders;
use App\Models\PaymentConfirmation;
use App\Models\Reservation;
use App\Services\Orders\OrderPaymentDeadlineService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OrderPaymentDeadlineServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:') {
            $this->markTestSkipped('Payment deadline tests require sqlite :memory:.');
        }

        $this->prepareSchema();
        Carbon::setTestNow('2026-08-10 09:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_deadline_is_exactly_forty_eight_hours_after_approval(): void
    {
        $deadline = app(OrderPaymentDeadlineService::class)->deadlineFrom(Carbon::now());

        $this->assertSame('2026-08-12 09:00:00', $deadline->toDateTimeString());
    }

    public function test_scheduler_cancels_expired_unpaid_orders_for_every_shared_service_idempotently(): void
    {
        foreach (['Hotel', 'Hotel Promo', 'Hotel Package', 'Transport', 'Tour Package', 'Activity', 'Private Villa'] as $index => $service) {
            $this->createApprovedOrder($index + 1, $service, Carbon::now()->subMinute());
        }

        $this->artisan('orders:auto-cancel-expired-payments')
            ->expectsOutput('Canceled 7 expired unpaid order(s).')
            ->assertSuccessful();

        $this->assertSame(7, DB::table('orders')->where('status', 'Canceled')->count());
        $this->assertSame(7, DB::table('reservations')->where('status', 'Canceled')->count());
        $this->assertSame(7, DB::table('order_logs')->where('action', 'Auto Cancel Payment Deadline')->count());

        $this->artisan('orders:auto-cancel-expired-payments')
            ->expectsOutput('Canceled 0 expired unpaid order(s).')
            ->assertSuccessful();

        $this->assertSame(7, DB::table('order_logs')->count());
    }

    public function test_deadline_does_not_cancel_future_paid_or_under_review_orders(): void
    {
        $future = $this->createApprovedOrder(1, 'Hotel', Carbon::now()->addMinute());
        $pending = $this->createApprovedOrder(2, 'Transport', Carbon::now()->subMinute());
        $valid = $this->createApprovedOrder(3, 'Tour Package', Carbon::now()->subMinute());
        $paid = $this->createApprovedOrder(4, 'Activity', Carbon::now()->subMinute());
        $settledBalance = $this->createApprovedOrder(5, 'Hotel Promo', Carbon::now()->subMinute());
        $settledBalance['invoice']->update(['balance' => 0]);

        foreach ([[$pending, 'Pending'], [$valid, 'Valid'], [$paid, 'Paid']] as [$fixture, $status]) {
            PaymentConfirmation::create([
                'inv_id' => $fixture['invoice']->id,
                'status' => $status,
            ]);
        }

        $this->assertSame(0, app(OrderPaymentDeadlineService::class)->cancelExpiredOrders(Carbon::now()));
        $this->assertSame(5, Orders::query()->where('status', 'Approved')->count());
        $this->assertSame('Active', $future['reservation']->fresh()->status);
        $this->assertSame(0, DB::table('order_logs')->count());
    }

    public function test_invalid_receipt_does_not_extend_payment_deadline(): void
    {
        $fixture = $this->createApprovedOrder(1, 'Hotel', Carbon::now()->subMinute());
        PaymentConfirmation::create([
            'inv_id' => $fixture['invoice']->id,
            'status' => 'Invalid',
        ]);

        $this->assertTrue(app(OrderPaymentDeadlineService::class)->cancelIfExpired($fixture['order'], Carbon::now()));
        $this->assertSame('Canceled', $fixture['order']->fresh()->status);
    }

    public function test_legacy_long_due_date_is_capped_at_forty_eight_hours_from_invoice(): void
    {
        $fixture = $this->createApprovedOrder(1, 'Tour Package', Carbon::now()->addWeek());
        $fixture['invoice']->update(['inv_date' => Carbon::now()->subHours(49)]);

        $this->assertSame(1, app(OrderPaymentDeadlineService::class)->cancelExpiredOrders(Carbon::now()));
        $this->assertSame('Canceled', $fixture['order']->fresh()->status);
        $this->assertSame(
            Carbon::now()->subHour()->toDateTimeString(),
            Carbon::parse($fixture['invoice']->fresh()->due_date)->toDateTimeString()
        );
    }

    public function test_legacy_short_due_date_is_normalized_without_early_cancel(): void
    {
        $fixture = $this->createApprovedOrder(1, 'Hotel', Carbon::now()->subMinute());
        $fixture['invoice']->update([
            'inv_date' => Carbon::now()->subHours(24),
            'due_date' => Carbon::now()->subDay(),
        ]);

        $this->assertSame(0, app(OrderPaymentDeadlineService::class)->cancelExpiredOrders(Carbon::now()));
        $this->assertSame('Approved', $fixture['order']->fresh()->status);
        $this->assertSame(
            Carbon::now()->addHours(24)->toDateTimeString(),
            Carbon::parse($fixture['invoice']->fresh()->due_date)->toDateTimeString()
        );
    }

    public function test_malformed_legacy_due_date_does_not_break_scheduler(): void
    {
        $fixture = $this->createApprovedOrder(1, 'Transport', Carbon::now()->subMinute());
        $fixture['invoice']->update(['due_date' => 'not-a-date']);

        $this->assertSame(1, app(OrderPaymentDeadlineService::class)->cancelExpiredOrders(Carbon::now()));
        $this->assertSame('Canceled', $fixture['order']->fresh()->status);
        $this->assertSame(
            Carbon::now()->subMinute()->toDateTimeString(),
            Carbon::parse($fixture['invoice']->fresh()->due_date)->toDateTimeString()
        );
    }

    private function createApprovedOrder(int $id, string $service, Carbon $deadline): array
    {
        $reservation = Reservation::create([
            'rsv_no' => 'RSV-'.$id,
            'service' => $service,
            'status' => 'Active',
        ]);
        $order = Orders::create([
            'orderno' => 'ORD-'.$id,
            'service' => $service,
            'name' => 'Agent '.$id,
            'status' => 'Approved',
            'rsv_id' => $reservation->id,
        ]);
        $invoice = InvoiceAdmin::create([
            'rsv_id' => $reservation->id,
            'inv_no' => 'INV-'.$id,
            'inv_date' => $deadline->copy()->subHours(OrderPaymentDeadlineService::PAYMENT_WINDOW_HOURS),
            'due_date' => $deadline->toDateTimeString(),
            'balance' => 100,
        ]);

        return compact('order', 'reservation', 'invoice');
    }

    private function prepareSchema(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('orderno')->nullable();
            $table->string('confirmation_order')->nullable();
            $table->string('service')->nullable();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->text('msg')->nullable();
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('rsv_no')->nullable();
            $table->string('service')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
        Schema::create('invoice_admins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->string('inv_no')->nullable();
            $table->dateTime('inv_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->decimal('balance', 18, 2)->nullable();
            $table->timestamps();
        });
        Schema::create('payment_confirmations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inv_id')->nullable();
            $table->string('status')->nullable();
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
}
