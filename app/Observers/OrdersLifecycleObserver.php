<?php

namespace App\Observers;

use App\Models\Orders;
use App\Services\OrderLifecycleNotificationService;
use Illuminate\Support\Facades\DB;

class OrdersLifecycleObserver
{
    public function updated(Orders $order): void
    {
        $oldStatus = $order->getOriginal('status');
        $newStatus = $order->status;
        $statusChanged = $order->wasChanged('status') && $oldStatus !== $newStatus;
        $wasCompleted = $order->getOriginal('completed_at');
        $isNewlyCompleted = $order->wasChanged('completed_at') && !$wasCompleted && $order->completed_at;

        if (!$statusChanged && !$isNewlyCompleted) {
            return;
        }

        DB::afterCommit(function () use ($order, $statusChanged, $oldStatus, $newStatus, $isNewlyCompleted): void {
            $service = app(OrderLifecycleNotificationService::class);
            if ($statusChanged) {
                $service->orderStatusChanged($order, $oldStatus, $newStatus);
            }
            if ($isNewlyCompleted) {
                $service->orderCompleted($order);
            }
        });
    }
}
