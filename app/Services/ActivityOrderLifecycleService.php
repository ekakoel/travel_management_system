<?php

namespace App\Services;

use App\Models\OrderLog;
use App\Models\Orders;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ActivityOrderLifecycleService
{
    private const CURRENT_STATUSES = ['Draft', 'Pending', 'Approved', 'Paid'];
    private const CLOSED_STATUSES = ['Canceled', 'Rejected', 'Invalid', 'Deleted'];

    public function applyActivityCurrentScope(Builder $query, Carbon $now): Builder
    {
        return $query
            ->where('service', Orders::PUBLIC_ACTIVITY_SERVICE)
            ->whereIn('status', self::CURRENT_STATUSES)
            ->when(Schema::hasColumn('orders', 'completed_at'), fn ($builder) => $builder->whereNull('completed_at'));
    }

    public function applyActivityHistoryScope(Builder $query, Carbon $now): Builder
    {
        return $query
            ->where('service', Orders::PUBLIC_ACTIVITY_SERVICE)
            ->where(function ($builder) {
                $builder->whereIn('status', self::CLOSED_STATUSES);

                if (Schema::hasColumn('orders', 'completed_at')) {
                    $builder->orWhere(function ($completed) {
                        $completed->where('status', 'Paid')
                            ->whereNotNull('completed_at');
                    });
                }

                // Read compatibility only; new fulfillment writes keep the order Paid.
                $builder->orWhere('status', 'Completed');
            });
    }

    public function completeEligiblePaidOrders(?int $ownerId = null, ?Carbon $now = null): int
    {
        if (!Schema::hasColumn('orders', 'completed_at')) {
            return 0;
        }

        $now ??= Carbon::now();
        $completed = 0;

        Orders::query()
            ->where('service', Orders::PUBLIC_ACTIVITY_SERVICE)
            ->where('status', 'Paid')
            ->whereNull('completed_at')
            ->whereNotNull('checkout')
            ->where('checkout', '<', $now)
            ->when($ownerId, fn ($query) => $query->where('sales_agent', $ownerId))
            ->orderBy('id')
            ->chunkById(100, function ($orders) use ($now, &$completed) {
                foreach ($orders as $order) {
                    DB::transaction(function () use ($order, $now, &$completed) {
                        $lockedOrder = Orders::query()
                            ->whereKey($order->id)
                            ->where('service', Orders::PUBLIC_ACTIVITY_SERVICE)
                            ->where('status', 'Paid')
                            ->whereNull('completed_at')
                            ->lockForUpdate()
                            ->first();

                        if (!$lockedOrder || !$lockedOrder->checkout || !Carbon::parse($lockedOrder->checkout)->lt($now)) {
                            return;
                        }

                        $lockedOrder->forceFill([
                            'completed_at' => $now,
                            'completed_by' => 0,
                        ])->save();

                        if ($lockedOrder->rsv_id) {
                            Reservation::where('id', $lockedOrder->rsv_id)
                                ->whereIn('status', ['Pending', 'Active'])
                                ->update(['status' => 'Completed']);
                        }

                        OrderLog::create([
                            'order_id' => $lockedOrder->id,
                            'action' => 'Complete Activity Order',
                            'url' => 'activity:complete-eligible-orders',
                            'method' => 'Update',
                            'agent' => $lockedOrder->name,
                            'admin' => null,
                        ]);

                        $completed++;
                    });
                }
            });

        return $completed;
    }
}
