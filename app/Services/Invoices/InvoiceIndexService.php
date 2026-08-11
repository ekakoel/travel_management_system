<?php

namespace App\Services\Invoices;

use App\Models\InvoiceAdmin;
use App\Services\Orders\OrderPaymentDeadlineService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InvoiceIndexService
{
    public function __construct(
        private readonly OrderPaymentDeadlineService $deadlineService,
        private readonly InvoicePaymentStateResolver $paymentStateResolver,
    ) {
    }

    public function data(): array
    {
        $now = Carbon::now();
        $invoices = InvoiceAdmin::query()
            ->select([
                'id',
                'inv_no',
                'rsv_id',
                'inv_date',
                'due_date',
                'total_usd',
                'balance',
                'currency_id',
                'created_at',
            ])
            ->with([
                'reservations:id,rsv_no,agn_id,service,status',
                'reservations.agent:id,name,office',
                'currency:id,name',
                'payment:id,inv_id,status',
            ])
            ->withCount('additionalinv')
            ->where('due_date', '>', $now)
            ->orderBy('due_date')
            ->orderBy('id')
            ->get();

        $rows = $invoices->map(fn (InvoiceAdmin $invoice) => $this->row($invoice, $now));

        return [
            'invoiceRows' => $rows,
            'invoiceStats' => $this->stats($rows),
            'invoiceCurrencies' => $rows->pluck('currency')->filter()->unique()->sort()->values(),
            'invoiceStates' => $rows
                ->map(fn (array $row) => ['key' => $row['state_key'], 'label' => $row['state']])
                ->unique('key')
                ->sortBy('label')
                ->values(),
            'now' => $now,
        ];
    }

    private function row(InvoiceAdmin $invoice, Carbon $now): array
    {
        $reservation = $invoice->reservations;
        $deadline = $this->deadlineService->deadlineForInvoice($invoice);
        $balance = $this->decimal($invoice->balance);
        $state = $this->paymentStateResolver->resolve($invoice, $balance);
        $currency = strtoupper((string) ($invoice->currency?->name ?: 'USD'));
        $minutesLeft = $deadline ? $now->diffInMinutes($deadline, false) : null;
        $hoursLeft = $minutesLeft !== null ? (int) ceil($minutesLeft / 60) : null;
        $isOverdue = $deadline ? $deadline->lt($now) && $state['key'] !== 'paid' : false;
        $isDueSoon = ! $isOverdue
            && $state['key'] !== 'paid'
            && $hoursLeft !== null
            && $hoursLeft <= 12;

        return [
            'id' => $invoice->id,
            'reference' => $invoice->inv_no ?: '#'.$invoice->id,
            'invoice_date' => $this->date($invoice->inv_date ?: $invoice->created_at),
            'deadline' => $this->dateTime($deadline),
            'deadline_meta' => $this->deadlineMeta($hoursLeft, $isOverdue),
            'is_overdue' => $isOverdue,
            'is_due_soon' => $isDueSoon,
            'due_bucket' => $isOverdue ? 'overdue' : ($isDueSoon ? 'due-soon' : 'upcoming'),
            'reservation' => $reservation?->rsv_no ?: '-',
            'reservation_status' => $reservation?->status ?: '-',
            'service' => $reservation?->service ?: __('invoices.mixed_services'),
            'agent' => $reservation?->agent?->name ?: __('invoices.unassigned_agent'),
            'agent_office' => $reservation?->agent?->office,
            'total_usd' => $this->usd($invoice->total_usd),
            'balance' => $this->currencyMoney($balance, $currency),
            'currency' => $currency,
            'state' => $state['label'],
            'state_key' => $state['key'],
            'state_tone' => $state['tone'],
            'payment_count' => $invoice->payment->count(),
            'adjustment_count' => (int) $invoice->additionalinv_count,
            'detail_url' => route('admin.invoices.show', $invoice),
            'search' => mb_strtolower(implode(' ', array_filter([
                $invoice->inv_no,
                $reservation?->rsv_no,
                $reservation?->service,
                $reservation?->agent?->name,
                $reservation?->agent?->office,
                $state['label'],
                $currency,
            ]))),
        ];
    }

    private function stats(Collection $rows): array
    {
        return [
            ['label' => __('invoices.open_invoices'), 'value' => $rows->count(), 'meta' => __('invoices.open_invoices_meta'), 'icon' => 'fa fa-file-text-o', 'tone' => 'blue'],
            ['label' => __('invoices.awaiting_payment'), 'value' => $rows->whereIn('state_key', ['unpaid', 'partial'])->count(), 'meta' => __('invoices.awaiting_payment_meta'), 'icon' => 'fa fa-clock-o', 'tone' => 'amber'],
            ['label' => __('invoices.payment_review'), 'value' => $rows->where('state_key', 'review')->count(), 'meta' => __('invoices.payment_review_meta'), 'icon' => 'fa fa-search', 'tone' => 'teal'],
            ['label' => __('invoices.deadline_attention'), 'value' => $rows->whereIn('due_bucket', ['overdue', 'due-soon'])->count(), 'meta' => __('invoices.deadline_attention_meta'), 'icon' => 'fa fa-exclamation-triangle', 'tone' => 'red'],
        ];
    }

    private function deadlineMeta(?int $hoursLeft, bool $isOverdue): string
    {
        if ($hoursLeft === null) {
            return '-';
        }

        if ($isOverdue) {
            return __('invoices.deadline_passed');
        }

        return __('invoices.hours_left', ['count' => max($hoursLeft, 0)]);
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

    private function usd($value): string
    {
        return '$'.number_format($this->decimal($value), 2, '.', ',');
    }

    private function currencyMoney($value, string $currency): string
    {
        $decimals = in_array($currency, ['IDR', 'TWD'], true) ? 0 : 2;

        return $currency.' '.number_format($this->decimal($value), $decimals, '.', ',');
    }
}
