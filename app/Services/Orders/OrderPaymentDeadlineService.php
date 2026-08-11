<?php

namespace App\Services\Orders;

use App\Models\InvoiceAdmin;
use App\Models\OrderLog;
use App\Models\Orders;
use App\Models\PaymentConfirmation;
use App\Models\Reservation;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class OrderPaymentDeadlineService
{
    public const PAYMENT_WINDOW_HOURS = 48;
    public const PROTECTING_PAYMENT_STATUSES = ['Pending', 'Valid', 'Paid'];
    public const AUTO_CANCEL_MESSAGE = 'Automatically canceled because no payment confirmation was submitted within 48 hours after approval.';

    public function deadlineFrom(?CarbonInterface $approvedAt = null): Carbon
    {
        $start = $approvedAt ? Carbon::parse($approvedAt) : Carbon::now();

        return $start->copy()->addHours(self::PAYMENT_WINDOW_HOURS);
    }

    public function deadlineForInvoice(?InvoiceAdmin $invoice): ?Carbon
    {
        if (!$invoice) {
            return null;
        }

        $storedDeadline = $this->parseDate($invoice->due_date);
        $invoiceStartedAt = $this->parseDate($invoice->inv_date)
            ?: $this->parseDate($invoice->created_at);
        $standardDeadline = $invoiceStartedAt
            ? $this->deadlineFrom($invoiceStartedAt)
            : null;

        return $standardDeadline ?: $storedDeadline;
    }

    public function cancelExpiredOrders(?CarbonInterface $now = null): int
    {
        $effectiveNow = $now ? Carbon::parse($now) : Carbon::now();
        $canceled = 0;

        Orders::query()
            ->where('status', 'Approved')
            ->whereNotNull('rsv_id')
            ->whereHas('reservations.invoice', function ($query) use ($effectiveNow) {
                $standardCutoff = $effectiveNow->copy()->subHours(self::PAYMENT_WINDOW_HOURS)->toDateTimeString();
                $query->where('balance', '>', 0)
                    ->where(function ($deadlineQuery) use ($effectiveNow, $standardCutoff) {
                        $deadlineQuery->where('due_date', '<=', $effectiveNow->toDateTimeString())
                            ->orWhere('inv_date', '<=', $standardCutoff)
                            ->orWhere(function ($createdQuery) use ($standardCutoff) {
                                $createdQuery->whereNull('inv_date')
                                    ->where('created_at', '<=', $standardCutoff);
                            });
                    });
            })
            ->select('orders.id')
            ->chunkById(100, function ($orders) use ($effectiveNow, &$canceled) {
                foreach ($orders as $order) {
                    if ($this->cancelIfExpired($order, $effectiveNow, [
                        'url' => 'scheduler',
                        'admin' => 0,
                    ])) {
                        $canceled++;
                    }
                }
            });

        return $canceled;
    }

    public function cancelIfExpired(
        Orders $order,
        ?CarbonInterface $now = null,
        array $audit = []
    ): bool {
        $effectiveNow = $now ? Carbon::parse($now) : Carbon::now();

        return DB::transaction(function () use ($order, $effectiveNow, $audit) {
            $lockedOrder = Orders::query()->lockForUpdate()->find($order->id);

            if (!$lockedOrder
                || $lockedOrder->status !== 'Approved'
                || $lockedOrder->completed_at
                || !$lockedOrder->rsv_id) {
                return false;
            }

            $reservation = Reservation::query()
                ->whereKey($lockedOrder->rsv_id)
                ->lockForUpdate()
                ->first();

            if (!$reservation || $reservation->status === 'Completed') {
                return false;
            }

            $invoice = InvoiceAdmin::query()
                ->where('rsv_id', $reservation->id)
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if (!$invoice) {
                return false;
            }

            if ((float) $invoice->balance <= 0) {
                return false;
            }

            $deadline = $this->deadlineForInvoice($invoice);
            if (!$deadline) {
                return false;
            }

            $storedDeadline = $this->parseDate($invoice->due_date);
            if (!$storedDeadline || !$storedDeadline->equalTo($deadline)) {
                $invoice->update(['due_date' => $deadline]);
            }

            if ($deadline->gt($effectiveNow)) {
                return false;
            }

            $hasPaymentSubmission = PaymentConfirmation::query()
                ->where('inv_id', $invoice->id)
                ->whereIn('status', self::PROTECTING_PAYMENT_STATUSES)
                ->lockForUpdate()
                ->get(['id'])
                ->isNotEmpty();

            if ($hasPaymentSubmission) {
                return false;
            }

            $lockedOrder->update([
                'status' => 'Canceled',
                'msg' => self::AUTO_CANCEL_MESSAGE,
            ]);

            Reservation::query()
                ->whereKey($reservation->id)
                ->whereIn('status', ['Pending', 'Active'])
                ->update(['status' => 'Canceled']);

            OrderLog::create([
                'order_id' => $lockedOrder->id,
                'action' => 'Auto Cancel Payment Deadline',
                'url' => $audit['url'] ?? 'system',
                'method' => 'Update',
                'agent' => (string) $lockedOrder->name,
                'admin' => $audit['admin'] ?? 0,
            ]);

            return true;
        }, 3);
    }

    private function parseDate(mixed $value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
