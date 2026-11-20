<?php

namespace App\Services\Payments;

use App\Exceptions\DokuException;
use App\Models\DokuPayment;
use App\Models\InvoiceAdmin;
use App\Models\OrderLog;
use App\Models\Orders;
use App\Models\PaymentConfirmation;
use App\Models\UsdRates;
use App\Services\Orders\OrderPaymentDeadlineService;
use App\Services\Orders\PublicPaymentConfirmationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DokuPaymentService
{
    public function __construct(
        private readonly DokuService $doku,
        private readonly PublicPaymentConfirmationService $payments,
        private readonly OrderPaymentDeadlineService $deadlines,
    ) {
    }

    public function supportsOrder(Orders $order): bool
    {
        return in_array($order->service, Orders::ACCOMMODATION_SERVICES, true)
            || in_array($order->service, [
                Orders::PUBLIC_TRANSPORT_SERVICE,
                Orders::PUBLIC_TOUR_SERVICE,
                Orders::PUBLIC_ACTIVITY_SERVICE,
            ], true);
    }

    public function activePayment(?InvoiceAdmin $invoice): ?DokuPayment
    {
        if (!$invoice) {
            return null;
        }

        return DokuPayment::query()
            ->where('invoice_id', $invoice->id)
            ->where('status', DokuPayment::STATUS_PENDING)
            ->latest('id')
            ->first();
    }

    public function startCheckout(Orders $order, int $userId): DokuPayment
    {
        $this->doku->assertConfigured();

        if (!$this->supportsOrder($order)) {
            throw ValidationException::withMessages([
                'payment' => __('messages.Online payment is not available for this service.'),
            ]);
        }

        $invoice = $this->payments->resolveInvoice($order);
        $deadline = $this->deadlines->deadlineForInvoice($invoice);

        if ($invoice && $deadline && $deadline->isPast()) {
            $this->deadlines->cancelIfExpired($order);
            $order->refresh();
        }

        $payment = DB::transaction(function () use ($order, $invoice, $deadline, $userId) {
            $lockedOrder = Orders::query()->lockForUpdate()->findOrFail($order->id);
            $lockedInvoice = $invoice
                ? InvoiceAdmin::query()->lockForUpdate()->findOrFail($invoice->id)
                : null;
            $lockedInvoice?->loadMissing('currency');

            if (!$this->payments->isPayable($lockedOrder, $lockedInvoice)
                || $lockedOrder->completed_at
                || ($deadline && $deadline->isPast())) {
                throw ValidationException::withMessages([
                    'payment' => __('messages.This order is no longer available for payment confirmation.'),
                ]);
            }

            if ($this->manualPending($lockedInvoice)->exists()) {
                throw ValidationException::withMessages([
                    'payment' => __('messages.A payment confirmation is already under review.'),
                ]);
            }

            if (DokuPayment::query()
                ->where('invoice_id', $lockedInvoice->id)
                ->where('status', DokuPayment::STATUS_REVIEW)
                ->exists()) {
                throw ValidationException::withMessages([
                    'payment' => __('messages.An online payment is under review. Please contact support before trying again.'),
                ]);
            }

            $existing = DokuPayment::query()
                ->where('invoice_id', $lockedInvoice->id)
                ->where('status', DokuPayment::STATUS_PENDING)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                if ($existing->checkout_url && (!$existing->expired_at || $existing->expired_at->isFuture())) {
                    return $existing;
                }

                $existing->update([
                    'status' => DokuPayment::STATUS_EXPIRED,
                    'review_reason' => 'Superseded by a new DOKU checkout attempt.',
                ]);
            }

            $amount = $this->amountForInvoice($lockedInvoice);
            $invoiceNumber = $this->uniqueInvoiceNumber($lockedOrder, $lockedInvoice);

            return DokuPayment::create([
                'order_id' => $lockedOrder->id,
                'invoice_id' => $lockedInvoice->id,
                'user_id' => $userId,
                'invoice_number' => $invoiceNumber,
                'request_id' => (string) Str::uuid(),
                'amount_idr' => $amount['amount_idr'],
                'invoice_currency' => $amount['currency'],
                'sell_rate' => $amount['sell_rate'],
                'status' => DokuPayment::STATUS_PENDING,
            ]);
        });

        if ($payment->checkout_url && (!$payment->expired_at || $payment->expired_at->isFuture())) {
            return $payment;
        }

        try {
            $payload = $this->checkoutPayload($payment, $order);
            $response = $this->doku->createCheckout($payload);

            $payment->update([
                'checkout_url' => data_get($response, 'response.payment.url'),
                'expired_at' => $this->doku->expiredAt($response)
                    ?: $this->deadlines->deadlineForInvoice($invoice),
                'raw_response' => $response,
            ]);

            return $payment->fresh();
        } catch (\Throwable $exception) {
            $payment->update([
                'status' => DokuPayment::STATUS_FAILED,
                'review_reason' => Str::limit($exception->getMessage(), 1000),
            ]);

            if ($exception instanceof DokuException) {
                throw $exception;
            }

            throw DokuException::invalidResponse();
        }
    }

    public function applyNotification(array $notification, ?string $notificationRequestId = null): ?DokuPayment
    {
        if (strtoupper((string) data_get($notification, 'transaction.status')) !== 'SUCCESS') {
            return null;
        }

        $invoiceNumber = (string) data_get($notification, 'order.invoice_number');

        if ($invoiceNumber === '') {
            return null;
        }

        $payment = DokuPayment::query()->where('invoice_number', $invoiceNumber)->first();

        if (!$payment) {
            return null;
        }

        return $this->applySuccess($payment, $notification, $notificationRequestId);
    }

    public function applySuccess(
        DokuPayment $payment,
        array $notification,
        ?string $notificationRequestId = null
    ): DokuPayment {
        return DB::transaction(function () use ($payment, $notification, $notificationRequestId) {
            $lockedPayment = DokuPayment::query()->lockForUpdate()->findOrFail($payment->id);

            if ($lockedPayment->status === DokuPayment::STATUS_SUCCESS) {
                return $lockedPayment;
            }

            if ($lockedPayment->status === DokuPayment::STATUS_REVIEW) {
                $lockedPayment->update([
                    'raw_notification' => $notification,
                    'notification_request_id' => $notificationRequestId ?: $lockedPayment->notification_request_id,
                ]);

                return $lockedPayment;
            }

            $order = Orders::query()->lockForUpdate()->find($lockedPayment->order_id);
            $invoice = InvoiceAdmin::query()->lockForUpdate()->find($lockedPayment->invoice_id);
            $invoice?->loadMissing('currency');
            $paidAt = $this->paidAt($notification) ?: Carbon::now();
            $deadline = $invoice ? $this->deadlines->deadlineForInvoice($invoice) : null;

            try {
                $reason = $this->reviewReason($lockedPayment, $order, $invoice, $paidAt, $deadline);
            } catch (ValidationException $exception) {
                return $this->markReview(
                    $lockedPayment,
                    $exception->getMessage(),
                    $notification,
                    $notificationRequestId
                );
            }

            if ($reason !== null) {
                return $this->markReview($lockedPayment, $reason, $notification, $notificationRequestId);
            }

            $idrRate = UsdRates::query()->firstOrCreate(
                ['name' => 'IDR'],
                [
                    'rate' => 1,
                    'sell' => 1,
                    'buy' => 1,
                    'difference' => 0,
                    'retrieved_at' => Carbon::now(),
                    'retrieval_source' => 'system',
                ]
            );

            $credited = min(
                (float) $invoice->balance,
                $this->creditInInvoiceCurrency(
                    (int) $lockedPayment->amount_idr,
                    (string) $lockedPayment->invoice_currency,
                    (float) $lockedPayment->sell_rate
                )
            );

            if ($credited <= 0) {
                return $this->markReview(
                    $lockedPayment,
                    'Payment credit is not positive.',
                    $notification,
                    $notificationRequestId
                );
            }

            $this->manualPending($invoice)->update([
                'status' => 'Invalid',
                'note' => 'Superseded by a confirmed DOKU payment.',
            ]);

            $confirmation = PaymentConfirmation::create([
                'kurs_id' => $idrRate->id,
                'receipt_img' => null,
                'inv_id' => $invoice->id,
                'payment_date' => $paidAt->toDateString(),
                'note' => 'DOKU Checkout payment '.$lockedPayment->invoice_number,
                'amount' => (string) $lockedPayment->amount_idr,
                'status' => 'Valid',
                'source' => PaymentConfirmation::SOURCE_DOKU,
                'gateway_reference' => $lockedPayment->invoice_number,
            ]);

            $newBalance = max(0, (float) $invoice->balance - $credited);
            $invoice->update(['balance' => $newBalance]);

            if ($newBalance < 1 && $order->status !== 'Paid') {
                $order->update(['status' => 'Paid']);
            }

            $lockedPayment->update([
                'status' => DokuPayment::STATUS_SUCCESS,
                'credited_amount' => $credited,
                'payment_confirmation_id' => $confirmation->id,
                'paid_at' => $paidAt,
                'notification_request_id' => $notificationRequestId ?: $lockedPayment->notification_request_id,
                'original_request_id' => data_get($notification, 'transaction.original_request_id'),
                'channel' => data_get($notification, 'channel.id') ?: data_get($notification, 'channel'),
                'raw_notification' => $notification,
                'review_reason' => null,
            ]);

            DokuPayment::query()
                ->where('invoice_id', $invoice->id)
                ->whereKeyNot($lockedPayment->id)
                ->where('status', DokuPayment::STATUS_PENDING)
                ->update([
                    'status' => DokuPayment::STATUS_EXPIRED,
                    'review_reason' => 'Another DOKU payment was completed for this invoice.',
                ]);

            OrderLog::create([
                'order_id' => $order->id,
                'action' => 'DOKU Payment Success',
                'url' => 'doku',
                'method' => 'Notification',
                'agent' => (string) $order->name,
                'admin' => 0,
            ]);

            return $lockedPayment->fresh();
        }, 3);
    }

    public function reconcilePending(int $limit = 50): int
    {
        if (!$this->doku->enabled()) {
            return 0;
        }

        $processed = 0;

        DokuPayment::query()
            ->where('status', DokuPayment::STATUS_PENDING)
            ->orderBy('id')
            ->limit($limit)
            ->get()
            ->each(function (DokuPayment $payment) use (&$processed) {
                try {
                    $status = $this->doku->checkStatus($payment->invoice_number);
                    $state = strtoupper((string) (
                        data_get($status, 'transaction.status')
                        ?: data_get($status, 'response.transaction.status')
                    ));

                    if ($state === 'SUCCESS') {
                        $this->applySuccess($payment, $status);
                        $processed++;
                    } elseif (in_array($state, ['EXPIRED', 'FAILED'], true)) {
                        $payment->update([
                            'status' => $state === 'EXPIRED'
                                ? DokuPayment::STATUS_EXPIRED
                                : DokuPayment::STATUS_FAILED,
                            'raw_response' => $status,
                        ]);
                        $processed++;
                    } else {
                        $payment->update(['raw_response' => $status]);
                    }
                } catch (\Throwable $exception) {
                    report($exception);
                }
            });

        return $processed;
    }

    public function amountForInvoice(InvoiceAdmin $invoice): array
    {
        $currency = strtoupper((string) ($invoice->currency?->name ?: 'USD'));
        $sellRate = $this->sellRate($invoice, $currency);
        $amountIdr = (int) ceil((float) $invoice->balance * $sellRate);

        if ($amountIdr < 1 || $amountIdr > 999999999999) {
            throw ValidationException::withMessages([
                'payment' => __('messages.The outstanding balance cannot be paid online at this time.'),
            ]);
        }

        return [
            'amount_idr' => $amountIdr,
            'currency' => $currency,
            'sell_rate' => $sellRate,
        ];
    }

    private function checkoutPayload(DokuPayment $payment, Orders $order): array
    {
        $invoice = $payment->invoice;
        $deadline = $this->deadlines->deadlineForInvoice($invoice);
        $minutes = $deadline
            ? max(1, min((int) ceil(now()->diffInMinutes($deadline, false)), (int) config('doku.max_payment_due_minutes', 8640)))
            : (int) config('doku.max_payment_due_minutes', 8640);

        $customer = array_filter([
            'name' => $order->name,
            'email' => $order->email,
        ], fn ($value) => filled($value));

        return [
            'order' => [
                'amount' => (int) $payment->amount_idr,
                'invoice_number' => $payment->invoice_number,
                'callback_url' => $this->payments->detailUrl($order),
                'line_items' => [[
                    'name' => Str::limit('Outstanding balance '.($invoice?->inv_no ?: $order->orderno), 255, ''),
                    'price' => (int) $payment->amount_idr,
                    'quantity' => 1,
                ]],
            ],
            'payment' => [
                'payment_due_date' => $minutes,
            ],
            'customer' => $customer,
        ];
    }

    private function reviewReason(
        DokuPayment $payment,
        ?Orders $order,
        ?InvoiceAdmin $invoice,
        Carbon $paidAt,
        ?Carbon $deadline
    ): ?string {
        if (!$order || !$invoice) {
            return 'Order or invoice is no longer available.';
        }

        if (in_array($order->status, ['Canceled', 'Rejected', 'Invalid', 'Deleted'], true)) {
            return 'Payment arrived after the order was closed.';
        }

        if ($order->status !== 'Approved' || $order->completed_at) {
            return 'Order is not in a payable state.';
        }

        if ($deadline && $paidAt->gt($deadline)) {
            return 'Payment arrived after the payment deadline.';
        }

        $expected = $this->amountForInvoice($invoice);

        if ((int) $payment->amount_idr !== (int) $expected['amount_idr']) {
            return 'Payment amount does not match the current outstanding balance.';
        }

        return null;
    }

    private function markReview(
        DokuPayment $payment,
        string $reason,
        array $notification,
        ?string $notificationRequestId
    ): DokuPayment {
        $payment->update([
            'status' => DokuPayment::STATUS_REVIEW,
            'review_reason' => $reason,
            'raw_notification' => $notification,
            'notification_request_id' => $notificationRequestId ?: $payment->notification_request_id,
        ]);

        OrderLog::create([
            'order_id' => $payment->order_id,
            'action' => Str::limit('DOKU Payment Review: '.$reason, 250),
            'url' => 'doku',
            'method' => 'Notification',
            'agent' => (string) $payment->invoice_number,
            'admin' => 0,
        ]);

        return $payment->fresh();
    }

    private function manualPending(InvoiceAdmin $invoice)
    {
        return PaymentConfirmation::query()
            ->where('inv_id', $invoice->id)
            ->where('status', 'Pending')
            ->where(function ($query) {
                $query->whereNull('source')
                    ->orWhere('source', PaymentConfirmation::SOURCE_MANUAL);
            });
    }

    private function sellRate(InvoiceAdmin $invoice, string $currency): float
    {
        $rate = match ($currency) {
            'IDR' => 1.0,
            'USD' => (float) $invoice->sell_usd,
            'CNY' => (float) $invoice->sell_cny,
            'TWD' => (float) $invoice->sell_twd,
            default => 0.0,
        };

        if ($rate <= 0) {
            throw ValidationException::withMessages([
                'payment' => __('messages.The invoice exchange rate is not available.'),
            ]);
        }

        return $rate;
    }

    private function creditInInvoiceCurrency(int $amountIdr, string $currency, float $sellRate): float
    {
        if ($currency === 'IDR') {
            return (float) $amountIdr;
        }

        return ceil($amountIdr / max($sellRate, 0.00000001));
    }

    private function paidAt(array $notification): ?Carbon
    {
        $value = data_get($notification, 'transaction.date')
            ?: data_get($notification, 'response.transaction.date');

        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function uniqueInvoiceNumber(Orders $order, InvoiceAdmin $invoice): string
    {
        do {
            $number = 'DK'.sprintf('%06d%06d', $order->id, $invoice->id).Str::upper(Str::random(8));
        } while (DokuPayment::query()->where('invoice_number', $number)->exists());

        return $number;
    }
}
