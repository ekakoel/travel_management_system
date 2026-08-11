<?php

namespace App\Services\Invoices;

use App\Models\InvoiceAdmin;

class InvoicePaymentStateResolver
{
    public function resolve(InvoiceAdmin $invoice, float $balance): array
    {
        if ($balance <= 0) {
            return ['key' => 'paid', 'label' => __('invoices.paid'), 'tone' => 'success'];
        }

        if ($invoice->payment->where('status', 'Pending')->isNotEmpty()) {
            return ['key' => 'review', 'label' => __('invoices.under_review'), 'tone' => 'info'];
        }

        if ($invoice->payment->whereIn('status', ['Valid', 'Paid'])->isNotEmpty()) {
            return ['key' => 'partial', 'label' => __('invoices.partially_paid'), 'tone' => 'warning'];
        }

        return ['key' => 'unpaid', 'label' => __('invoices.unpaid'), 'tone' => 'danger'];
    }
}
