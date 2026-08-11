<?php

namespace App\Services\Invoices;

use App\Models\InvoiceAdmin;
use App\Models\BankAccount;
use App\Models\Orders;
use App\Services\Orders\OrderPaymentDeadlineService;
use App\Services\Pricing\OrderPricingSnapshotReader;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InvoiceDetailService
{
    public function __construct(
        private readonly OrderPricingSnapshotReader $pricingSnapshotReader,
        private readonly OrderPaymentDeadlineService $deadlineService,
        private readonly InvoicePaymentStateResolver $paymentStateResolver,
    ) {
    }

    public function data(InvoiceAdmin $invoice): array
    {
        $invoice->loadMissing([
            'reservations.agent',
            'bank',
            'currency',
            'additionalinv',
            'payment.kurs',
            'transactions.wallet',
        ]);

        abort_unless($invoice->reservations, 404);

        $orders = $this->billableOrders($invoice);
        $orderRows = $orders->map(fn (Orders $order) => $this->orderRow($order, $invoice));
        $adjustmentRows = $invoice->additionalinv
            ->sortBy('date')
            ->map(fn ($adjustment) => [
                'id' => $adjustment->id,
                'kind' => 'adjustment',
                'reference' => __('invoices.adjustment'),
                'description' => $adjustment->description ?: '-',
                'period' => $this->date($adjustment->date),
                'rate' => $this->money($adjustment->rate),
                'rate_value' => $this->decimal($adjustment->rate),
                'unit' => $this->decimal($adjustment->unit),
                'times' => $this->decimal($adjustment->times),
                'amount' => $this->money($adjustment->amount),
                'amount_value' => $this->decimal($adjustment->amount),
                'detail_url' => null,
                'model' => $adjustment,
            ])
            ->values();
        $rows = $orderRows->concat($adjustmentRows)->values();
        $serviceSubtotal = $orderRows->sum('amount_value');
        $adjustmentTotal = $adjustmentRows->sum('amount_value');
        $calculatedTotal = round($serviceSubtotal + $adjustmentTotal, 2);
        $invoiceTotal = $rows->isNotEmpty()
            ? $calculatedTotal
            : $this->positiveDecimal($invoice->total_usd);
        $balance = $invoice->balance !== null ? $this->decimal($invoice->balance) : $invoiceTotal;
        $deadline = $this->deadlineService->deadlineForInvoice($invoice);
        $paymentState = $this->paymentStateResolver->resolve($invoice, $balance);
        $canManageAdjustments = ! in_array($invoice->reservations->status, ['Active', 'Completed'], true);
        $canChangeBank = $paymentState['key'] !== 'paid' && $invoice->reservations->status !== 'Completed';
        $paymentCurrency = $invoice->currency?->name ?: 'USD';

        return [
            'invoice' => $invoice,
            'invoiceOverview' => $this->overview($invoice, $deadline, $paymentState),
            'invoiceStats' => $this->stats($invoiceTotal, $balance, $paymentCurrency, $rows, $invoice),
            'invoiceRows' => $rows,
            'invoiceTotals' => [
                'service_subtotal' => $this->money($serviceSubtotal),
                'adjustment_total' => $this->money($adjustmentTotal),
                'invoice_total' => $this->money($invoiceTotal),
                'balance' => $this->currencyMoney($balance, $paymentCurrency),
                'currencies' => $this->currencyTotals($invoice, $invoiceTotal),
            ],
            'invoicePayments' => $this->paymentRows($invoice),
            'invoiceTransactions' => $this->transactionRows($invoice),
            'invoiceBankOptions' => $canChangeBank
                ? BankAccount::query()
                    ->orderBy('bank')
                    ->orderBy('currency')
                    ->get(['id', 'bank', 'currency', 'account_number'])
                : collect(),
            'canManageAdjustments' => $canManageAdjustments,
            'canChangeBank' => $canChangeBank,
        ];
    }

    private function billableOrders(InvoiceAdmin $invoice): Collection
    {
        return Orders::query()
            ->with('activePricingSnapshot')
            ->where('rsv_id', $invoice->rsv_id)
            ->where(function ($query) {
                $query->whereIn('status', ['Active', 'Approved', 'Paid', 'Completed']);

                $query->orWhereNotNull('completed_at');
            })
            ->where(function ($query) {
                $query->whereNull('status')->orWhere('status', '!=', 'Deleted');
            })
            ->orderByRaw('CASE WHEN checkin IS NULL THEN 1 ELSE 0 END')
            ->orderBy('checkin')
            ->orderBy('id')
            ->get();
    }

    private function orderRow(Orders $order, InvoiceAdmin $invoice): array
    {
        $tourPricing = $order->service === Orders::PUBLIC_TOUR_SERVICE
            ? $this->pricingSnapshotReader->historicalValues($order, $invoice)
            : null;
        $amount = $tourPricing['total_usd']
            ?? ($order->final_total_usd_minor !== null ? $order->final_total_usd_minor / 100 : null)
            ?? $order->final_price
            ?? $order->price_total
            ?? 0;
        $isAccommodation = in_array($order->service, Orders::ACCOMMODATION_SERVICES, true);
        $isTransport = $order->service === Orders::PUBLIC_TRANSPORT_SERVICE;
        $quantity = $isAccommodation
            ? max(1, (int) $order->number_of_room)
            : ($isTransport ? 1 : max(1, (int) ($order->number_of_guests ?: 1)));
        $times = $isAccommodation ? max(1, (int) $order->duration) : 1;
        $rate = $tourPricing['unit_price_usd']
            ?? ($isAccommodation || $isTransport
                ? $this->decimal($amount) / max(1, $quantity * $times)
                : ($order->price_pax ?? $this->decimal($amount) / max(1, $quantity)));

        return [
            'id' => $order->id,
            'kind' => 'order',
            'reference' => $order->orderno ?: '#'.$order->id,
            'description' => collect([$order->service, $order->servicename, $order->subservice])->filter()->implode(' / ') ?: '-',
            'period' => $this->period($order),
            'rate' => $this->money($rate),
            'rate_value' => $this->decimal($rate),
            'unit' => $quantity,
            'times' => $times,
            'amount' => $this->money($amount),
            'amount_value' => $this->decimal($amount),
            'detail_url' => route('admin.order.show', $order->id),
            'model' => null,
        ];
    }

    private function overview(InvoiceAdmin $invoice, ?Carbon $deadline, array $paymentState): array
    {
        $reservation = $invoice->reservations;
        $hoursLeft = $deadline ? max((int) ceil(now()->diffInMinutes($deadline, false) / 60), 0) : null;

        return [
            'reference' => $invoice->inv_no ?: '#'.$invoice->id,
            'invoice_date' => $this->dateTime($invoice->inv_date ?: $invoice->created_at),
            'due_date' => $deadline ? $deadline->format('d M Y, H:i') : '-',
            'hours_left' => $hoursLeft,
            'is_overdue' => $deadline ? $deadline->isPast() && $paymentState['key'] !== 'paid' : false,
            'payment_state' => $paymentState['label'],
            'payment_tone' => $paymentState['tone'],
            'reservation_reference' => $reservation->rsv_no ?: '#'.$reservation->id,
            'reservation_status' => $reservation->status ?: '-',
            'reservation_url' => route('view.reservation.detail', $reservation),
            'service' => $reservation->service ?: __('invoices.mixed_services'),
            'service_period' => $this->periodValues($reservation->checkin, $reservation->checkout),
            'guest_name' => $reservation->customer_name ?: $reservation->pickup_name ?: '-',
            'guest_phone' => $reservation->phone ?: '-',
            'agent_name' => $reservation->agent?->name ?: '-',
            'agent_office' => $reservation->agent?->office ?: '-',
            'agent_phone' => $reservation->agent?->phone ?: '-',
            'agent_email' => $reservation->agent?->email ?: '-',
            'bank_name' => $invoice->bank?->bank ?: __('invoices.not_selected'),
            'bank_currency' => $invoice->bank?->currency ?: '-',
            'bank_account_name' => $invoice->bank?->account_name ?: '-',
            'bank_account_number' => $invoice->bank?->account_number ?: '-',
            'bank_swift' => $invoice->bank?->swift_code ?: '-',
            'payment_currency' => $invoice->currency?->name ?: 'USD',
        ];
    }

    private function stats(
        float $total,
        float $balance,
        string $paymentCurrency,
        Collection $rows,
        InvoiceAdmin $invoice
    ): array
    {
        return [
            ['label' => __('invoices.invoice_total'), 'value' => $this->money($total), 'meta' => __('invoices.authoritative_total'), 'icon' => 'fa fa-usd', 'tone' => 'blue'],
            ['label' => __('invoices.balance_due'), 'value' => $this->currencyMoney($balance, $paymentCurrency), 'meta' => __('invoices.outstanding_balance'), 'icon' => 'fa fa-credit-card', 'tone' => $balance <= 0 ? 'green' : 'amber'],
            ['label' => __('invoices.line_items'), 'value' => number_format($rows->count()), 'meta' => __('invoices.services_and_adjustments'), 'icon' => 'fa fa-list-alt', 'tone' => 'teal'],
            ['label' => __('invoices.payments'), 'value' => number_format($invoice->payment->count()), 'meta' => __('invoices.payment_submissions'), 'icon' => 'fa fa-check-circle', 'tone' => $invoice->payment->isEmpty() ? 'slate' : 'green'],
        ];
    }

    private function paymentRows(InvoiceAdmin $invoice): Collection
    {
        return $invoice->payment->sortByDesc('payment_date')->map(fn ($payment) => [
            'date' => $this->date($payment->payment_date ?: $payment->created_at),
            'currency' => $payment->kurs?->name ?: '-',
            'amount' => $this->currencyMoney($payment->amount, $payment->kurs?->name),
            'status' => $payment->status ?: '-',
            'status_tone' => $this->statusTone($payment->status),
            'note' => $payment->note ?: '-',
        ])->values();
    }

    private function transactionRows(InvoiceAdmin $invoice): Collection
    {
        return $invoice->transactions->sortByDesc('transaction_date')->map(fn ($transaction) => [
            'reference' => $transaction->transaction_code ?: '#'.$transaction->id,
            'date' => $this->date($transaction->transaction_date ?: $transaction->created_at),
            'type' => $transaction->type ?: '-',
            'amount' => $this->currencyMoney($transaction->amount, $transaction->kurs),
            'status' => $transaction->status ?: '-',
            'status_tone' => $this->statusTone($transaction->status),
        ])->values();
    }

    private function currencyTotals(InvoiceAdmin $invoice, float $fallbackUsd): Collection
    {
        return collect([
            ['code' => 'USD', 'amount' => $this->positiveDecimal($invoice->total_usd) ?: $fallbackUsd],
            ['code' => 'IDR', 'amount' => $this->positiveDecimal($invoice->total_idr)],
            ['code' => 'CNY', 'amount' => $this->positiveDecimal($invoice->total_cny)],
            ['code' => 'TWD', 'amount' => $this->positiveDecimal($invoice->total_twd)],
        ])->filter(fn ($row) => $row['amount'] > 0)->map(fn ($row) => [
            'code' => $row['code'],
            'formatted' => $this->currencyMoney($row['amount'], $row['code']),
        ])->values();
    }

    private function period(Orders $order): string
    {
        return $this->periodValues($order->travel_date ?: $order->checkin ?: $order->pickup_date, $order->checkout ?: $order->dropoff_date);
    }

    private function periodValues($start, $end): string
    {
        $from = $this->date($start);
        $until = $this->date($end);

        return $until !== '-' && $until !== $from ? $from.' - '.$until : $from;
    }

    private function date($value): string
    {
        try {
            return $value ? Carbon::parse($value)->format('d M Y') : '-';
        } catch (\Throwable) {
            return (string) ($value ?: '-');
        }
    }

    private function dateTime($value): string
    {
        try {
            return $value ? Carbon::parse($value)->format('d M Y, H:i') : '-';
        } catch (\Throwable) {
            return (string) ($value ?: '-');
        }
    }

    private function decimal($value): float
    {
        return is_numeric($value) ? round((float) $value, 2) : 0.0;
    }

    private function positiveDecimal($value): float
    {
        return max(0, $this->decimal($value));
    }

    private function money($value): string
    {
        return '$'.number_format($this->decimal($value), 2, '.', ',');
    }

    private function currencyMoney($value, ?string $currency): string
    {
        $code = strtoupper((string) ($currency ?: 'USD'));
        $decimals = in_array($code, ['IDR', 'TWD'], true) ? 0 : 2;

        return $code.' '.number_format($this->decimal($value), $decimals, '.', ',');
    }

    private function statusTone(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'active', 'approved', 'valid', 'paid', 'completed' => 'success',
            'pending', 'waiting', 'partial', 'partially paid' => 'warning',
            'rejected', 'invalid', 'canceled', 'cancelled' => 'danger',
            default => 'muted',
        };
    }
}
