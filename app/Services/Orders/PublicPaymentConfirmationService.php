<?php

namespace App\Services\Orders;

use App\Models\InvoiceAdmin;
use App\Models\Orders;
use App\Models\PaymentConfirmation;
use Carbon\Carbon;

class PublicPaymentConfirmationService
{
    public const ORDER_STATUS_PAYABLE = 'Approved';
    public const REVIEW_STATUS = 'Pending';
    public const DEADLINE_PROTECTING_STATUSES = ['Pending', 'Valid', 'Paid'];

    public function resolveInvoice(Orders $order): ?InvoiceAdmin
    {
        $reservation = $order->reservations;

        if (!$reservation || (int) $reservation->id !== (int) $order->rsv_id) {
            return null;
        }

        $invoice = $reservation->invoice;

        if (!$invoice || (int) $invoice->rsv_id !== (int) $reservation->id) {
            return null;
        }

        return $invoice;
    }

    public function isPayable(Orders $order, ?InvoiceAdmin $invoice): bool
    {
        return $order->status === self::ORDER_STATUS_PAYABLE
            && $invoice
            && (float) $invoice->balance > 0;
    }

    public function hasDeadlineProtectingSubmission(?InvoiceAdmin $invoice): bool
    {
        return $invoice
            ? $invoice->payment()->whereIn('status', self::DEADLINE_PROTECTING_STATUSES)->exists()
            : false;
    }

    public function hasOpenReview(?InvoiceAdmin $invoice): bool
    {
        return $invoice
            ? $invoice->payment()->where('status', self::REVIEW_STATUS)->exists()
            : false;
    }

    public function latestReceipt(?InvoiceAdmin $invoice): ?PaymentConfirmation
    {
        if (!$invoice) {
            return null;
        }

        if ($invoice->relationLoaded('payment')) {
            return $invoice->payment->sortByDesc('id')->first();
        }

        return $invoice->payment()->latest('id')->first();
    }

    public function paymentDeadline(?InvoiceAdmin $invoice): ?Carbon
    {
        return app(OrderPaymentDeadlineService::class)->deadlineForInvoice($invoice);
    }

    public function state(Orders $order, ?InvoiceAdmin $invoice): array
    {
        $deadline = $this->paymentDeadline($invoice);
        $hasSubmission = $this->hasDeadlineProtectingSubmission($invoice);
        $expired = $order->status === 'Canceled'
            || ($order->status === self::ORDER_STATUS_PAYABLE && $deadline && $deadline->isPast() && !$hasSubmission);
        $latestReceipt = $this->latestReceipt($invoice);
        $hasOpenReview = $this->hasOpenReview($invoice);

        return [
            'deadline' => $deadline,
            'expired' => $expired,
            'has_submission' => $hasSubmission,
            'has_open_review' => $hasOpenReview,
            'latest_receipt' => $latestReceipt,
            'can_submit' => $this->isPayable($order, $invoice) && !$expired && !$hasOpenReview,
        ];
    }

    public function detailUrl(Orders $order): string
    {
        if (in_array($order->service, Orders::ACCOMMODATION_SERVICES, true)
            || $order->service === Orders::PUBLIC_ACTIVITY_SERVICE) {
            return route('view.detail-order-hotel', ['id' => $order->id]);
        }

        if ($order->service === 'Private Villa') {
            return route('view.detail-order-villa', ['id' => $order->id]);
        }

        if ($order->service === Orders::PUBLIC_TRANSPORT_SERVICE) {
            return route('view.detail-order-transport', ['id' => $order->id]);
        }

        return route('view.detail-order-tour', ['id' => $order->id]);
    }
}
