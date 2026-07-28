<?php

namespace App\Services;

use App\Models\InvoiceAdmin;
use App\Models\OrderLog;
use App\Models\Orders;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AccommodationOrderLifecycleService
{
    public const CURRENT_STATUSES = ['Draft', 'Pending', 'Approved'];
    public const PAID_STATUS = 'Paid';
    public const COMPLETED_STATUS = 'Completed';
    public const CLOSED_STATUSES = ['Completed', 'Canceled', 'Rejected', 'Deleted'];
    public const ATTENTION_STATUSES = ['Invalid'];

    public function isAccommodation(Orders $order): bool
    {
        return in_array($order->service, Orders::ACCOMMODATION_SERVICES, true);
    }

    public function applyAccommodationHistoryScope(Builder $query, Carbon $now): Builder
    {
        $hasCompletionSource = $this->canWriteCompletionSource();

        return $query->whereIn('service', Orders::ACCOMMODATION_SERVICES)
            ->where(function (Builder $builder) use ($hasCompletionSource) {
                if ($hasCompletionSource) {
                    $builder->whereNotNull('completed_at');
                } else {
                    $builder->whereRaw('1 = 0');
                }

                $builder->orWhereIn('status', ['Canceled', 'Rejected', 'Deleted'])
                    ->orWhere('status', self::COMPLETED_STATUS);
            });
    }

    public function applyAccommodationCurrentScope(Builder $query, Carbon $now): Builder
    {
        $query->whereIn('service', Orders::ACCOMMODATION_SERVICES);

        if ($this->canWriteCompletionSource()) {
            $query->whereNull('completed_at');
        }

        return $query->where(function (Builder $builder) {
                $builder->whereIn('status', array_merge(self::CURRENT_STATUSES, self::ATTENTION_STATUSES))
                    ->orWhere(function (Builder $paidNotCompleted) {
                        $paidNotCompleted->where('status', self::PAID_STATUS);
                    });
            });
    }

    public function displayGroup(Orders $order, ?Carbon $now = null): string
    {
        $now = $now ?: Carbon::now();

        if (!$this->isAccommodation($order)) {
            return 'standard';
        }

        if ($order->status === 'Draft') {
            return 'draft';
        }

        if (in_array($order->status, self::ATTENTION_STATUSES, true)) {
            return 'attention';
        }

        if ($order->completed_at || in_array($order->status, self::CLOSED_STATUSES, true)) {
            return 'history';
        }

        if ($order->status !== self::PAID_STATUS) {
            return 'current';
        }

        $checkin = $order->checkin ? Carbon::parse($order->checkin)->startOfDay() : null;
        $checkout = $order->checkout ? Carbon::parse($order->checkout)->startOfDay() : null;
        $today = $now->copy()->startOfDay();

        if ($checkin && $checkin->gt($today)) {
            return 'upcoming';
        }

        return 'in_service';
    }

    public function canWriteCompletionSource(): bool
    {
        return Schema::hasTable('orders') && Schema::hasColumn('orders', 'completed_at');
    }

    public function completeEligiblePaidOrders(?int $ownerId = null, ?Carbon $now = null): int
    {
        $now = $now ?: Carbon::now();

        if (!$this->canWriteCompletionSource()) {
            return 0;
        }

        return DB::transaction(function () use ($ownerId, $now) {
            $query = Orders::query()
                ->accommodationService()
                ->where('status', self::PAID_STATUS)
                ->whereNull('completed_at')
                ->whereNotNull('checkout')
                ->whereDate('checkout', '<', $now->toDateString());

            if ($ownerId !== null) {
                $query->where('sales_agent', $ownerId);
            }

            $orders = $query->lockForUpdate()->get();

            foreach ($orders as $order) {
                $order->update([
                    'completed_at' => $now,
                    'completed_by' => 0,
                ]);

                if ($order->rsv_id) {
                    Reservation::where('id', $order->rsv_id)
                        ->whereIn('status', ['Pending', 'Active'])
                        ->update(['status' => self::COMPLETED_STATUS]);
                }

                OrderLog::create([
                    'order_id' => $order->id,
                    'action' => 'Complete Accommodation Order',
                    'url' => 'system',
                    'method' => 'Complete',
                    'agent' => (string) $order->name,
                    'admin' => 0,
                ]);
            }

            return $orders->count();
        });
    }

    public function invoicePaymentState(?InvoiceAdmin $invoice): string
    {
        if (!$invoice) {
            return 'Not Generated';
        }

        $balance = (float) $invoice->balance;

        if ($balance <= 0) {
            return 'Paid';
        }

        $validPaymentTotal = $invoice->payment()
            ->where('status', 'Valid')
            ->sum('amount');

        return (float) $validPaymentTotal > 0 ? 'Partially Paid' : 'Unpaid';
    }
}
