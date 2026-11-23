<?php

namespace App\Observers;

use App\Models\PaymentConfirmation;
use App\Services\OrderLifecycleNotificationService;
use Illuminate\Support\Facades\DB;

class PaymentConfirmationLifecycleObserver
{
    public function created(PaymentConfirmation $confirmation): void
    {
        $this->notifyAfterCommit($confirmation, true);
    }

    public function updated(PaymentConfirmation $confirmation): void
    {
        if ($confirmation->wasChanged('status')) {
            $this->notifyAfterCommit($confirmation, false);
        }
    }

    private function notifyAfterCommit(PaymentConfirmation $confirmation, bool $created): void
    {
        DB::afterCommit(function () use ($confirmation, $created): void {
            try {
                $freshConfirmation = $confirmation->fresh(['invoice.reservation.orders']);
                if ($freshConfirmation) {
                    app(OrderLifecycleNotificationService::class)
                        ->paymentConfirmationChanged($freshConfirmation, $created);
                }
            } catch (\Throwable $exception) {
                report($exception);
            }
        });
    }
}
