@extends('layouts.head')

@section('title', __('invoices.index_title'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/finance/invoices/index.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/finance/invoices/index.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container invoice-index-page" data-invoice-index>
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="invoice-index-hero"
                    eyebrow="{{ __('invoices.eyebrow') }}"
                    title="{{ __('invoices.index_title') }}"
                    description="{{ __('invoices.index_description') }}"
                />

                <section class="backend-page-toolbar invoice-index-toolbar">
                    <nav aria-label="{{ __('invoices.breadcrumb') }}">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">{{ __('invoices.admin_panel') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('invoices.invoices') }}</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--info">{{ $now->format('d M Y') }}</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('invalid') || session()->has('error'))
                    <section class="backend-feedback invoice-index-feedback">
                        @if ($errors->any())
                            <div class="backend-alert backend-alert--danger">
                                <strong>{{ __('invoices.action_attention') }}</strong>
                                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                        @endif
                        @if (session()->has('success'))
                            <div class="backend-alert backend-alert--success"><strong>{{ session('success') }}</strong></div>
                        @endif
                        @if (session()->has('invalid') || session()->has('error'))
                            <div class="backend-alert backend-alert--danger"><strong>{{ session('invalid') ?? session('error') }}</strong></div>
                        @endif
                    </section>
                @endif

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="{{ __('invoices.summary') }}">
                    @foreach ($invoiceStats as $stat)
                        <article class="backend-kpi-card backend-kpi-card--{{ $stat['tone'] }}">
                            <div class="backend-kpi-card__icon" aria-hidden="true"><i class="{{ $stat['icon'] }}"></i></div>
                            <div><span>{{ $stat['label'] }}</span><strong>{{ number_format($stat['value']) }}</strong><small>{{ $stat['meta'] }}</small></div>
                        </article>
                    @endforeach
                </section>

                <section class="backend-filter-panel invoice-index-filter" aria-label="{{ __('invoices.filters') }}">
                    <label class="backend-filter-field" for="invoiceSearch">
                        <span class="backend-filter-label">{{ __('invoices.search') }}</span>
                        <span class="backend-filter-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input id="invoiceSearch" class="backend-filter-control" type="search" placeholder="{{ __('invoices.search_placeholder') }}" data-invoice-filter="search">
                        </span>
                    </label>
                    <label class="backend-filter-field" for="invoiceStateFilter">
                        <span class="backend-filter-label">{{ __('invoices.payment_status') }}</span>
                        <select id="invoiceStateFilter" class="backend-filter-control" data-invoice-filter="state">
                            <option value="">{{ __('invoices.all_statuses') }}</option>
                            @foreach ($invoiceStates as $state)
                                <option value="{{ $state['key'] }}">{{ $state['label'] }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="backend-filter-field" for="invoiceCurrencyFilter">
                        <span class="backend-filter-label">{{ __('invoices.currency') }}</span>
                        <select id="invoiceCurrencyFilter" class="backend-filter-control" data-invoice-filter="currency">
                            <option value="">{{ __('invoices.all_currencies') }}</option>
                            @foreach ($invoiceCurrencies as $currency)
                                <option value="{{ strtolower($currency) }}">{{ $currency }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="backend-filter-field" for="invoiceDueFilter">
                        <span class="backend-filter-label">{{ __('invoices.deadline') }}</span>
                        <select id="invoiceDueFilter" class="backend-filter-control" data-invoice-filter="due">
                            <option value="">{{ __('invoices.all_deadlines') }}</option>
                            <option value="overdue">{{ __('invoices.overdue') }}</option>
                            <option value="due-soon">{{ __('invoices.due_soon') }}</option>
                            <option value="upcoming">{{ __('invoices.upcoming') }}</option>
                        </select>
                    </label>
                    <div class="backend-filter-actions invoice-index-filter__actions">
                        <button type="button" class="backend-button backend-button-secondary" data-invoice-filter-reset><i class="fa fa-refresh" aria-hidden="true"></i>{{ __('invoices.reset_filters') }}</button>
                    </div>
                </section>

                <section class="backend-panel invoice-index-panel" aria-labelledby="invoiceQueueTitle">
                    <header class="backend-section-header invoice-index-panel__heading">
                        <div><span class="backend-section-header__label">{{ __('invoices.work_queue') }}</span><h2 id="invoiceQueueTitle">{{ __('invoices.open_invoice_queue') }}</h2></div>
                        <p><span data-invoice-visible-count>{{ $invoiceRows->count() }}</span> {{ __('invoices.records_visible') }}</p>
                    </header>

                    <div class="backend-table-wrap invoice-index-table-wrap">
                        <table class="backend-table invoice-index-table">
                            <thead><tr><th>{{ __('invoices.invoice') }}</th><th>{{ __('invoices.agent') }}</th><th>{{ __('invoices.reservation') }}</th><th>{{ __('invoices.due_date') }}</th><th>{{ __('invoices.invoice_total') }}</th><th>{{ __('invoices.balance_due') }}</th><th>{{ __('invoices.status') }}</th><th>{{ __('invoices.action') }}</th></tr></thead>
                            <tbody>
                                @forelse ($invoiceRows as $row)
                                    <tr data-invoice-row data-invoice-search="{{ $row['search'] }}" data-invoice-state="{{ $row['state_key'] }}" data-invoice-currency="{{ strtolower($row['currency']) }}" data-invoice-due="{{ $row['due_bucket'] }}">
                                        <td data-label="{{ __('invoices.invoice') }}"><strong>{{ $row['reference'] }}</strong><small>{{ $row['invoice_date'] }}</small></td>
                                        <td data-label="{{ __('invoices.agent') }}"><strong>{{ $row['agent'] }}</strong>@if ($row['agent_office'])<small>{{ $row['agent_office'] }}</small>@endif</td>
                                        <td data-label="{{ __('invoices.reservation') }}"><strong>{{ $row['reservation'] }}</strong><small>{{ $row['service'] }} / {{ $row['reservation_status'] }}</small></td>
                                        <td data-label="{{ __('invoices.due_date') }}"><strong>{{ $row['deadline'] }}</strong><small class="{{ $row['is_overdue'] ? 'is-overdue' : ($row['is_due_soon'] ? 'is-due-soon' : '') }}">{{ $row['deadline_meta'] }}</small></td>
                                        <td data-label="{{ __('invoices.invoice_total') }}"><strong>{{ $row['total_usd'] }}</strong><small>{{ trans_choice('invoices.adjustment_count', $row['adjustment_count'], ['count' => $row['adjustment_count']]) }}</small></td>
                                        <td data-label="{{ __('invoices.balance_due') }}"><strong>{{ $row['balance'] }}</strong><small>{{ trans_choice('invoices.payment_count', $row['payment_count'], ['count' => $row['payment_count']]) }}</small></td>
                                        <td data-label="{{ __('invoices.status') }}"><span class="backend-status-badge backend-status-badge--{{ $row['state_tone'] }}">{{ $row['state'] }}</span></td>
                                        <td data-label="{{ __('invoices.action') }}"><div class="backend-table-actions"><a href="{{ $row['detail_url'] }}" class="backend-icon-action backend-icon-action--view" aria-label="{{ __('invoices.view_invoice', ['reference' => $row['reference']]) }}" data-backend-action-loading><i class="fa fa-eye" aria-hidden="true"></i></a></div></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8"><div class="backend-table-empty"><i class="fa fa-file-text-o" aria-hidden="true"></i><strong>{{ __('invoices.empty_title') }}</strong><span>{{ __('invoices.empty_description') }}</span></div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="backend-table-card-list invoice-index-card-list">
                        @foreach ($invoiceRows as $row)
                            <article class="backend-table-card invoice-index-card" data-invoice-row data-invoice-search="{{ $row['search'] }}" data-invoice-state="{{ $row['state_key'] }}" data-invoice-currency="{{ strtolower($row['currency']) }}" data-invoice-due="{{ $row['due_bucket'] }}">
                                <header class="backend-table-card__header"><div><span class="backend-table-card__label">{{ __('invoices.invoice') }}</span><strong>{{ $row['reference'] }}</strong></div><span class="backend-status-badge backend-status-badge--{{ $row['state_tone'] }}">{{ $row['state'] }}</span></header>
                                <dl class="backend-table-card-grid">
                                    <div><dt>{{ __('invoices.agent') }}</dt><dd>{{ $row['agent'] }}</dd></div>
                                    <div><dt>{{ __('invoices.reservation') }}</dt><dd>{{ $row['reservation'] }} / {{ $row['service'] }}</dd></div>
                                    <div><dt>{{ __('invoices.due_date') }}</dt><dd>{{ $row['deadline'] }}<small class="{{ $row['is_overdue'] ? 'is-overdue' : ($row['is_due_soon'] ? 'is-due-soon' : '') }}">{{ $row['deadline_meta'] }}</small></dd></div>
                                    <div><dt>{{ __('invoices.invoice_total') }}</dt><dd>{{ $row['total_usd'] }}</dd></div>
                                    <div><dt>{{ __('invoices.balance_due') }}</dt><dd>{{ $row['balance'] }}</dd></div>
                                </dl>
                                <footer class="backend-table-card__actions invoice-index-card__actions"><a href="{{ $row['detail_url'] }}" class="backend-button backend-button-secondary" data-backend-action-loading><i class="fa fa-eye" aria-hidden="true"></i>{{ __('invoices.view') }}</a></footer>
                            </article>
                        @endforeach
                        <div class="backend-empty-state invoice-index-filter-empty" data-invoice-filter-empty hidden><i class="fa fa-search" aria-hidden="true"></i><strong>{{ __('invoices.no_matches') }}</strong><span>{{ __('invoices.adjust_filters') }}</span></div>
                    </div>
                </section>
            </div>
        </main>
    @endcan
@endsection
