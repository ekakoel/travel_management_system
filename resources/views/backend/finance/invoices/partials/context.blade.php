<section class="backend-panel backend-detail-side-card invoice-detail-side-card">
    <header class="backend-section-header"><div><span>{{ __('invoices.actions_eyebrow') }}</span><h2>{{ __('invoices.quick_actions') }}</h2></div></header>
    <nav class="invoice-detail-section-nav m-b-18" aria-label="{{ __('invoices.invoice_sections') }}">
        <a href="#invoice-overview"><i class="fas fa-file-alt" aria-hidden="true"></i>{{ __('invoices.invoice_overview') }}</a>
        <a href="#invoice-items"><i class="fa fa-list-alt" aria-hidden="true"></i>{{ __('invoices.invoice_items') }}</a>
        <a href="#invoice-payments"><i class="fa fa-credit-card" aria-hidden="true"></i>{{ __('invoices.payment_history') }}</a>
        <a href="#invoice-transactions"><i class="fas fa-exchange-alt" aria-hidden="true"></i>{{ __('invoices.transaction_history') }}</a>
    </nav>
    <div class="backend-detail-side-actions">
        <a href="{{ $invoiceOverview['reservation_url'] }}" class="backend-button backend-button-primary" data-backend-action-loading><i class="fa fa-calendar" aria-hidden="true"></i>{{ __('invoices.open_reservation') }}</a>
        <a href="{{ route('admin.invoices.index') }}" class="backend-button backend-button-secondary" data-backend-action-loading><i class="fa fa-list" aria-hidden="true"></i>{{ __('invoices.open_invoice_list') }}</a>
    </div>
</section>

<section class="backend-panel backend-detail-side-card invoice-detail-side-card">
    <header class="backend-section-header"><div><span>{{ __('invoices.context_eyebrow') }}</span><h2>{{ __('invoices.billing_context') }}</h2></div></header>
    <ul class="backend-detail-side-list">
        <li><span>{{ __('invoices.bank') }}</span><strong>{{ $invoiceOverview['bank_name'] }}</strong><small>{{ $invoiceOverview['bank_currency'] }}</small></li>
        <li><span>{{ __('invoices.account_name') }}</span><strong>{{ $invoiceOverview['bank_account_name'] }}</strong><small>{{ $invoiceOverview['bank_account_number'] }}</small></li>
        <li><span>{{ __('invoices.swift_code') }}</span><strong>{{ $invoiceOverview['bank_swift'] }}</strong></li>
    </ul>
    @if ($canChangeBank)
        <div class="backend-detail-side-actions"><button type="button" class="backend-button backend-button-secondary" data-invoice-modal-open="invoice-bank-modal"><i class="fa fa-bank" aria-hidden="true"></i>{{ __('invoices.change_bank') }}</button></div>
    @endif
</section>

<section class="backend-panel backend-detail-side-card invoice-detail-side-card">
    <header class="backend-section-header"><div><span>{{ __('invoices.amount_eyebrow') }}</span><h2>{{ __('invoices.amount_summary') }}</h2></div></header>
    <ul class="backend-detail-side-list">
        @foreach ($invoiceTotals['currencies'] as $currencyTotal)
            <li><span>{{ $currencyTotal['code'] }}</span><strong>{{ $currencyTotal['formatted'] }}</strong></li>
        @endforeach
        <li><span>{{ __('invoices.balance_due') }}</span><strong>{{ $invoiceTotals['balance'] }}</strong><small>{{ $invoiceOverview['payment_state'] }}</small></li>
    </ul>
</section>
