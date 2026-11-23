<?php

namespace App\Services;

use App\Models\Orders;
use App\Models\PaymentConfirmation;
use App\Models\User;
use App\Notifications\OrderLifecycleNotification;
use Illuminate\Support\Facades\Log;

class OrderLifecycleNotificationService
{
    private const INCLUDED_SERVICES = [
        ...Orders::ACCOMMODATION_SERVICES,
        Orders::PUBLIC_TRANSPORT_SERVICE,
        Orders::PUBLIC_TOUR_SERVICE,
        Orders::PUBLIC_ACTIVITY_SERVICE,
    ];

    public function orderStatusChanged(Orders $order, ?string $oldStatus, ?string $newStatus): void
    {
        if (!$this->isIncludedOrder($order) || !$newStatus || $oldStatus === $newStatus) {
            return;
        }

        $event = match ($newStatus) {
            'Pending' => $oldStatus === 'Draft' ? 'submitted' : 'pending',
            'Approved' => 'approved',
            'Paid' => 'paid',
            'Canceled' => 'canceled',
            'Rejected' => 'rejected',
            'Invalid' => 'invalid',
            default => null,
        };

        if ($event) {
            $this->notifyOrderAccounts($order, $event);
        }
    }

    public function orderCompleted(Orders $order): void
    {
        if ($this->isIncludedOrder($order)) {
            $this->notifyOrderAccounts($order, 'completed');
        }
    }

    public function paymentConfirmationChanged(PaymentConfirmation $confirmation, bool $created): void
    {
        $order = $confirmation->invoice?->reservation?->orders;
        if (!$order || !$this->isIncludedOrder($order)) {
            return;
        }

        if ($created && $confirmation->status === 'Pending') {
            $this->notifyOrderAccounts($order, 'payment_submitted');
            return;
        }

        if ($confirmation->status === 'Invalid') {
            $this->notifyOrderAccounts($order, 'payment_invalid');
            return;
        }

        // The Paid order transition already sends the settlement notification.
        if ($confirmation->status === 'Valid' && $order->status !== 'Paid') {
            $this->notifyOrderAccounts($order, 'payment_verified');
        }
    }

    private function isIncludedOrder(Orders $order): bool
    {
        return in_array($order->service, self::INCLUDED_SERVICES, true);
    }

    private function notifyOrderAccounts(Orders $order, string $event): void
    {
        $copy = $this->copyFor($event, $order);
        $recipientIds = array_unique(array_filter([
            $order->user_id,
            $order->sales_agent,
        ], static fn ($id) => $id !== null));

        if (!$recipientIds) {
            return;
        }

        $users = User::query()->whereIn('id', $recipientIds)->get();
        foreach ($users as $user) {
            try {
                $user->notify(new OrderLifecycleNotification([
                    'kind' => 'order',
                    'event' => $event,
                    'order_id' => $order->id,
                    'order_number' => (string) ($order->orderno ?: $order->id),
                    'service' => (string) $order->service,
                    'title' => $copy['title'],
                    'message' => $copy['message'],
                    'url' => route('view.orders'),
                ]));
            } catch (\Throwable $exception) {
                Log::warning('Order lifecycle notification delivery failed.', [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'event' => $event,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }
    }

    private function copyFor(string $event, Orders $order): array
    {
        $service = (string) ($order->servicename ?: $order->service);
        $reference = (string) ($order->orderno ?: $order->id);

        $messages = [
            'submitted' => ['Order submitted', 'Order :reference (:service) has been submitted and is waiting for review.'],
            'pending' => ['Order status updated', 'Order :reference (:service) is now pending.'],
            'approved' => ['Order approved', 'Order :reference (:service) has been approved.'],
            'paid' => ['Payment complete', 'Payment for order :reference (:service) is complete.'],
            'canceled' => ['Order canceled', 'Order :reference (:service) has been canceled.'],
            'rejected' => ['Order rejected', 'Order :reference (:service) has been rejected.'],
            'invalid' => ['Order needs attention', 'Order :reference (:service) has been marked invalid.'],
            'completed' => ['Service completed', 'Service for order :reference (:service) has been marked complete.'],
            'payment_submitted' => ['Payment received', 'Payment proof for order :reference (:service) was received and is being reviewed.'],
            'payment_verified' => ['Payment verified', 'A payment for order :reference (:service) has been verified.'],
            'payment_invalid' => ['Payment needs attention', 'The payment proof for order :reference (:service) could not be verified.'],
        ];
        [$title, $message] = $messages[$event];

        return [
            'title' => __($title),
            'message' => __($message, ['reference' => $reference, 'service' => $service]),
        ];
    }
}
