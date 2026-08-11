@extends('layouts.head')

@section('title', __('invoices.title'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/finance/invoices/detail.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/finance/invoices/detail.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container invoice-detail-page" data-invoice-detail>
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="invoice-detail-hero"
                    eyebrow="{{ __('invoices.eyebrow') }}"
                    title="{{ $invoiceOverview['reference'] }}"
                    description="{{ __('invoices.description') }}"
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.invoices.index') }}" class="backend-page-primary-action" data-backend-action-loading>
                            <i class="fa fa-arrow-left" aria-hidden="true"></i>{{ __('invoices.back_to_invoices') }}
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <section class="backend-page-toolbar invoice-detail-toolbar">
                    <nav aria-label="{{ __('invoices.invoices') }}">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">{{ __('invoices.admin_panel') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.invoices.index') }}">{{ __('invoices.invoices') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $invoiceOverview['reference'] }}</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--{{ $invoiceOverview['payment_tone'] }}">{{ $invoiceOverview['payment_state'] }}</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('error'))
                    <section class="backend-feedback invoice-detail-feedback">
                        @if ($errors->any())
                            <div class="backend-alert backend-alert--danger">
                                <strong>{{ __('invoices.action_attention') }}</strong>
                                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                        @endif
                        @if (session()->has('success'))
                            <div class="backend-alert backend-alert--success"><strong>{{ session('success') }}</strong></div>
                        @endif
                        @if (session()->has('error'))
                            <div class="backend-alert backend-alert--danger"><strong>{{ session('error') }}</strong></div>
                        @endif
                    </section>
                @endif

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="{{ __('invoices.summary') }}">
                    @foreach ($invoiceStats as $stat)
                        <article class="backend-kpi-card backend-kpi-card--{{ $stat['tone'] }}">
                            <div class="backend-kpi-card__icon" aria-hidden="true"><i class="{{ $stat['icon'] }}"></i></div>
                            <div><span>{{ $stat['label'] }}</span><strong>{{ $stat['value'] }}</strong><small>{{ $stat['meta'] }}</small></div>
                        </article>
                    @endforeach
                </section>

                <x-backend.detail-layout class="invoice-detail-layout">
                    <x-slot name="main">
                        <section class="backend-panel invoice-detail-panel" id="invoice-overview" data-invoice-section>
                            <header class="backend-section-header invoice-detail-panel__header">
                                <div><span class="backend-section-header__label">{{ __('invoices.overview_eyebrow') }}</span><h2>{{ __('invoices.invoice_overview') }}</h2></div>
                                <span class="backend-status-badge backend-status-badge--{{ $invoiceOverview['payment_tone'] }}">{{ $invoiceOverview['payment_state'] }}</span>
                            </header>
                            <div class="invoice-detail-overview-grid">
                                <article class="invoice-detail-info-block">
                                    <h3><i class="fas fa-file-alt" aria-hidden="true"></i>{{ __('invoices.invoice_overview') }}</h3>
                                    <dl class="invoice-detail-definition-list">
                                        <div><dt>{{ __('invoices.invoice_number') }}</dt><dd>{{ $invoiceOverview['reference'] }}</dd></div>
                                        <div><dt>{{ __('invoices.invoice_date') }}</dt><dd>{{ $invoiceOverview['invoice_date'] }}</dd></div>
                                        <div><dt>{{ __('invoices.due_date') }}</dt><dd>{{ $invoiceOverview['due_date'] }}<small class="{{ $invoiceOverview['is_overdue'] ? 'is-overdue' : '' }}">{{ $invoiceOverview['is_overdue'] ? __('invoices.overdue') : ($invoiceOverview['hours_left'] !== null ? __('invoices.hours_left', ['count' => $invoiceOverview['hours_left']]) : '-') }}</small></dd></div>
                                        <div><dt>{{ __('invoices.payment_currency') }}</dt><dd>{{ $invoiceOverview['payment_currency'] }}</dd></div>
                                    </dl>
                                </article>
                                <article class="invoice-detail-info-block">
                                    <h3><i class="fa fa-calendar" aria-hidden="true"></i>{{ __('invoices.reservation') }}</h3>
                                    <dl class="invoice-detail-definition-list">
                                        <div><dt>{{ __('invoices.reference') }}</dt><dd><a href="{{ $invoiceOverview['reservation_url'] }}">{{ $invoiceOverview['reservation_reference'] }}</a></dd></div>
                                        <div><dt>{{ __('invoices.reservation_status') }}</dt><dd>{{ $invoiceOverview['reservation_status'] }}</dd></div>
                                        <div><dt>{{ __('invoices.service') }}</dt><dd>{{ $invoiceOverview['service'] }}</dd></div>
                                        <div><dt>{{ __('invoices.service_period') }}</dt><dd>{{ $invoiceOverview['service_period'] }}</dd></div>
                                    </dl>
                                </article>
                                <article class="invoice-detail-info-block">
                                    <h3><i class="fa fa-user" aria-hidden="true"></i>{{ __('invoices.guest') }}</h3>
                                    <dl class="invoice-detail-definition-list">
                                        <div><dt>{{ __('invoices.guest') }}</dt><dd>{{ $invoiceOverview['guest_name'] }}</dd></div>
                                        <div><dt>{{ __('invoices.phone') }}</dt><dd>{{ $invoiceOverview['guest_phone'] }}</dd></div>
                                    </dl>
                                </article>
                                <article class="invoice-detail-info-block">
                                    <h3><i class="fa fa-briefcase" aria-hidden="true"></i>{{ __('invoices.agent') }}</h3>
                                    <dl class="invoice-detail-definition-list">
                                        <div><dt>{{ __('invoices.agent') }}</dt><dd>{{ $invoiceOverview['agent_name'] }}</dd></div>
                                        <div><dt>{{ __('invoices.office') }}</dt><dd>{{ $invoiceOverview['agent_office'] }}</dd></div>
                                        <div><dt>{{ __('invoices.phone') }}</dt><dd>{{ $invoiceOverview['agent_phone'] }}</dd></div>
                                        <div><dt>{{ __('invoices.email') }}</dt><dd>{{ $invoiceOverview['agent_email'] }}</dd></div>
                                    </dl>
                                </article>
                            </div>
                        </section>

                        <section class="backend-panel invoice-detail-panel" id="invoice-items" data-invoice-section>
                            <header class="backend-section-header invoice-detail-panel__header">
                                <div><span class="backend-section-header__label">{{ __('invoices.items_eyebrow') }}</span><h2>{{ __('invoices.invoice_items') }}</h2><p>{{ __('invoices.items_description') }}</p></div>
                                @if ($canManageAdjustments)
                                    <button type="button" class="backend-button backend-button-primary" data-invoice-modal-open="invoice-adjustment-create-modal"><i class="fa fa-plus" aria-hidden="true"></i>{{ __('invoices.add_adjustment') }}</button>
                                @endif
                            </header>

                            @if ($invoiceRows->isEmpty())
                                <div class="backend-empty-state invoice-detail-empty"><i class="fa fa-list-alt" aria-hidden="true"></i><strong>{{ __('invoices.no_items') }}</strong><span>{{ __('invoices.no_items_description') }}</span></div>
                            @else
                                <div class="backend-table-wrap invoice-detail-table-wrap">
                                    <table class="backend-table invoice-detail-table">
                                        <thead><tr><th>{{ __('invoices.reference') }}</th><th>{{ __('invoices.description_label') }}</th><th>{{ __('invoices.period') }}</th><th>{{ __('invoices.rate') }}</th><th>{{ __('invoices.unit') }}</th><th>{{ __('invoices.times') }}</th><th>{{ __('invoices.amount') }}</th><th>{{ __('invoices.action') }}</th></tr></thead>
                                        <tbody>
                                            @foreach ($invoiceRows as $row)
                                                <tr>
                                                    <td data-label="{{ __('invoices.reference') }}"><strong>{{ $row['reference'] }}</strong></td>
                                                    <td data-label="{{ __('invoices.description_label') }}">{{ $row['description'] }}</td>
                                                    <td data-label="{{ __('invoices.period') }}">{{ $row['period'] }}</td>
                                                    <td data-label="{{ __('invoices.rate') }}">{{ $row['rate'] }}</td>
                                                    <td data-label="{{ __('invoices.unit') }}">{{ $row['unit'] }}</td>
                                                    <td data-label="{{ __('invoices.times') }}">{{ $row['times'] }}</td>
                                                    <td data-label="{{ __('invoices.amount') }}"><strong>{{ $row['amount'] }}</strong></td>
                                                    <td data-label="{{ __('invoices.action') }}">
                                                        <div class="backend-table-actions">
                                                            @if ($row['detail_url'])
                                                                <a href="{{ $row['detail_url'] }}" class="backend-icon-action backend-icon-action--view" aria-label="{{ __('invoices.view_order', ['reference' => $row['reference']]) }}" data-backend-action-loading><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                            @elseif ($canManageAdjustments)
                                                                <button type="button" class="backend-icon-action backend-icon-action--edit" data-invoice-modal-open="invoice-adjustment-edit-{{ $row['id'] }}" aria-label="{{ __('invoices.edit_adjustment', ['description' => $row['description']]) }}"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                                                                <form action="{{ route('admin.invoices.adjustments.destroy', $row['model']) }}" method="post">
                                                                    @csrf @method('delete')
                                                                    <button type="submit" class="backend-icon-action backend-icon-action--delete" data-invoice-delete-confirm="{{ __('invoices.delete_adjustment_confirm', ['description' => $row['description']]) }}" aria-label="{{ __('invoices.delete_adjustment', ['description' => $row['description']]) }}"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="backend-table-card-list invoice-detail-card-list">
                                    @foreach ($invoiceRows as $row)
                                        <article class="backend-table-card invoice-detail-card">
                                            <header class="backend-table-card__header"><div><strong>{{ $row['reference'] }}</strong><span>{{ $row['description'] }}</span></div><strong>{{ $row['amount'] }}</strong></header>
                                            <dl class="backend-table-card-grid"><div><dt>{{ __('invoices.period') }}</dt><dd>{{ $row['period'] }}</dd></div><div><dt>{{ __('invoices.rate') }}</dt><dd>{{ $row['rate'] }}</dd></div><div><dt>{{ __('invoices.unit') }}</dt><dd>{{ $row['unit'] }}</dd></div><div><dt>{{ __('invoices.times') }}</dt><dd>{{ $row['times'] }}</dd></div></dl>
                                            <footer class="backend-table-card__actions">
                                                @if ($row['detail_url'])
                                                    <a href="{{ $row['detail_url'] }}" class="backend-button backend-button-secondary" data-backend-action-loading><i class="fa fa-eye" aria-hidden="true"></i>{{ __('invoices.view_order', ['reference' => $row['reference']]) }}</a>
                                                @elseif ($canManageAdjustments)
                                                    <button type="button" class="backend-button backend-button-secondary" data-invoice-modal-open="invoice-adjustment-edit-{{ $row['id'] }}"><i class="fa fa-pencil" aria-hidden="true"></i>{{ __('invoices.edit') }}</button>
                                                    <form action="{{ route('admin.invoices.adjustments.destroy', $row['model']) }}" method="post">
                                                        @csrf @method('delete')
                                                        <button type="submit" class="backend-button backend-button-danger" data-invoice-delete-confirm="{{ __('invoices.delete_adjustment_confirm', ['description' => $row['description']]) }}"><i class="fa fa-trash" aria-hidden="true"></i>{{ __('invoices.delete') }}</button>
                                                    </form>
                                                @endif
                                            </footer>
                                        </article>
                                    @endforeach
                                </div>
                            @endif

                            <dl class="invoice-detail-total-list">
                                <div><dt>{{ __('invoices.service_subtotal') }}</dt><dd>{{ $invoiceTotals['service_subtotal'] }}</dd></div>
                                <div><dt>{{ __('invoices.adjustment_total') }}</dt><dd>{{ $invoiceTotals['adjustment_total'] }}</dd></div>
                                <div class="is-total"><dt>{{ __('invoices.total_usd') }}</dt><dd>{{ $invoiceTotals['invoice_total'] }}</dd></div>
                            </dl>
                        </section>

                        <section class="backend-panel invoice-detail-panel" id="invoice-payments" data-invoice-section>
                            <header class="backend-section-header invoice-detail-panel__header"><div><span class="backend-section-header__label">{{ __('invoices.payment_eyebrow') }}</span><h2>{{ __('invoices.payment_history') }}</h2><p>{{ __('invoices.payment_history_description') }}</p></div></header>
                            @if ($invoicePayments->isEmpty())
                                <div class="backend-empty-state backend-empty-state--compact"><i class="fa fa-credit-card" aria-hidden="true"></i><strong>{{ __('invoices.no_payments') }}</strong></div>
                            @else
                                <div class="invoice-detail-record-list">
                                    @foreach ($invoicePayments as $payment)
                                        <article class="invoice-detail-record"><div><strong>{{ $payment['amount'] }}</strong><span>{{ $payment['date'] }} &middot; {{ $payment['currency'] }}</span></div><div><span class="backend-status-badge backend-status-badge--{{ $payment['status_tone'] }}">{{ $payment['status'] }}</span><small>{{ $payment['note'] }}</small></div></article>
                                    @endforeach
                                </div>
                            @endif
                        </section>

                        <section class="backend-panel invoice-detail-panel" id="invoice-transactions" data-invoice-section>
                            <header class="backend-section-header invoice-detail-panel__header"><div><span class="backend-section-header__label">{{ __('invoices.transaction_eyebrow') }}</span><h2>{{ __('invoices.transaction_history') }}</h2></div></header>
                            @if ($invoiceTransactions->isEmpty())
                                <div class="backend-empty-state backend-empty-state--compact"><i class="fa fa-exchange" aria-hidden="true"></i><strong>{{ __('invoices.no_transactions') }}</strong></div>
                            @else
                                <div class="invoice-detail-record-list">
                                    @foreach ($invoiceTransactions as $transaction)
                                        <article class="invoice-detail-record"><div><strong>{{ $transaction['reference'] }}</strong><span>{{ $transaction['date'] }} &middot; {{ $transaction['type'] }}</span></div><div><strong>{{ $transaction['amount'] }}</strong><span class="backend-status-badge backend-status-badge--{{ $transaction['status_tone'] }}">{{ $transaction['status'] }}</span></div></article>
                                    @endforeach
                                </div>
                            @endif
                        </section>
                    </x-slot>

                    <x-slot name="side">
                        @include('backend.finance.invoices.partials.context')
                    </x-slot>
                </x-backend.detail-layout>
            </div>
        </main>

        @include('backend.finance.invoices.partials.modals')
    @endcan
@endsection
