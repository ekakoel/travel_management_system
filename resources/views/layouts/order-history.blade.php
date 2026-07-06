@extends('frontend.layouts.app')

@section('title', __('messages.Order History'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/frontend-orders-entry.css') }}">
@endpush

@php
    $statusToneMap = [
        'Draft' => 'draft',
        'Pending' => 'pending',
        'Approved' => 'approved',
        'Confirmed' => 'confirmed',
        'Active' => 'active',
        'Paid' => 'paid',
        'Rejected' => 'rejected',
        'Invalid' => 'invalid',
        'Canceled' => 'canceled',
    ];

    $serviceIconMap = [
        'Hotel' => 'fa-hotel',
        'Hotel Promo' => 'fa-tags',
        'Hotel Package' => 'fa-box-open',
        'Tour Package' => 'fa-map-marked-alt',
        'Activity' => 'fa-person-hiking',
        'Transport' => 'fa-car-side',
        'Private Villa' => 'fa-house',
        'Wedding' => 'fa-heart',
    ];
@endphp

@section('content')
    <div class="frontend-page-shell orders-dashboard-page orders-history-page">
        <section class="orders-dashboard-hero orders-history-hero">
            <div class="container">
                @include('partials.breadcrumbs', [
                    'breadcrumbs' => [
                        ['url' => route('home'), 'label' => __('messages.Home')],
                        ['url' => route('view.orders'), 'label' => __('messages.Orders')],
                        ['label' => __('messages.Order History')],
                    ],
                    'variant' => 'dark',
                ])

                <div class="orders-dashboard-hero__content">
                    <div>
                        <span class="orders-dashboard-hero__eyebrow">
                            <i class="fa fa-history" aria-hidden="true"></i>
                            @lang('messages.Order History')
                        </span>
                        <h1 class="orders-dashboard-hero__title">@lang('messages.Booking archive')</h1>
                        <p class="orders-dashboard-hero__text">
                            @lang('messages.Review completed and past orders with international date formatting, searchable records, invoice access, and clear booking status.')
                        </p>
                    </div>
                    <div class="orders-dashboard-hero__meta">
                        <div class="orders-dashboard-hero__metric">
                            <span>@lang('messages.Total')</span>
                            <strong>{{ $summary['total'] }}</strong>
                        </div>
                        <div class="orders-dashboard-hero__metric">
                            <span>@lang('messages.Invoices')</span>
                            <strong>{{ $summary['with_invoice'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="frontend-content-section orders-dashboard-main">
            <div class="container">
                @include('partials.alerts')

                <div class="orders-summary-grid orders-history-summary">
                    <div class="orders-summary-card orders-summary-card--history">
                        <span>@lang('messages.Past orders')</span>
                        <strong>{{ $summary['total'] }}</strong>
                        <p>@lang('messages.All completed, past, or archived booking references available to this agent.')</p>
                    </div>
                    <div class="orders-summary-card orders-summary-card--current">
                        <span>@lang('messages.Paid')</span>
                        <strong>{{ $summary['paid'] }}</strong>
                        <p>@lang('messages.Orders marked as paid and ready for financial reference.')</p>
                    </div>
                    <div class="orders-summary-card orders-summary-card--draft">
                        <span>{{ now()->year }}</span>
                        <strong>{{ $summary['this_year'] }}</strong>
                        <p>@lang('messages.History records from the current calendar year.')</p>
                    </div>
                    <div class="orders-summary-card orders-summary-card--attention">
                        <span>@lang('messages.Invoice')</span>
                        <strong>{{ $summary['with_invoice'] }}</strong>
                        <p>@lang('messages.Available invoice documents are linked directly without loading heavy PDF previews.')</p>
                    </div>
                </div>

                <form class="orders-toolbar orders-history-toolbar" action="{{ route('orders.history') }}" method="get">
                    <div class="orders-toolbar__search">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <input
                            type="search"
                            class="form-control"
                            name="q"
                            value="{{ $filters['query'] }}"
                            placeholder="@lang('messages.Search order number, service, destination, or guest')"
                        >
                    </div>

                    <div class="orders-history-filter-grid">
                        <select class="form-select" name="service" aria-label="@lang('messages.Service')">
                            <option value="all">@lang('messages.All Services')</option>
                            @foreach ($serviceOptions as $serviceOption)
                                <option value="{{ $serviceOption }}" @selected($filters['service'] === $serviceOption)>
                                    {{ __('messages.' . $serviceOption) !== 'messages.' . $serviceOption ? __('messages.' . $serviceOption) : $serviceOption }}
                                </option>
                            @endforeach
                        </select>

                        <select class="form-select" name="status" aria-label="@lang('messages.Status')">
                            <option value="all">@lang('messages.All Statuses')</option>
                            @foreach ($statusOptions as $statusOption)
                                <option value="{{ $statusOption }}" @selected($filters['status'] === $statusOption)>
                                    {{ __('messages.' . $statusOption) !== 'messages.' . $statusOption ? __('messages.' . $statusOption) : $statusOption }}
                                </option>
                            @endforeach
                        </select>

                        <select class="form-select" name="year" aria-label="@lang('messages.Year')">
                            <option value="all">@lang('messages.All Years')</option>
                            @foreach ($availableYears as $availableYear)
                                <option value="{{ $availableYear }}" @selected((string) $filters['year'] === (string) $availableYear)>{{ $availableYear }}</option>
                            @endforeach
                        </select>

                        <select class="form-select" name="sort" aria-label="@lang('messages.Sort')">
                            <option value="recent" @selected($filters['sort'] === 'recent')>@lang('messages.Newest first')</option>
                            <option value="oldest" @selected($filters['sort'] === 'oldest')>@lang('messages.Oldest first')</option>
                            <option value="highest" @selected($filters['sort'] === 'highest')>@lang('messages.Highest value')</option>
                            <option value="lowest" @selected($filters['sort'] === 'lowest')>@lang('messages.Lowest value')</option>
                        </select>
                    </div>

                    <div class="orders-history-toolbar__actions">
                        <button class="btn btn-primary" type="submit">@lang('messages.Apply Filter')</button>
                        <a class="btn btn-outline-secondary" href="{{ route('orders.history') }}">@lang('messages.Reset')</a>
                    </div>
                </form>

                <section class="orders-section orders-history-results">
                    <div class="orders-section__header">
                        <div>
                            <span class="orders-section__eyebrow">@lang('messages.Archive')</span>
                            <h2 class="orders-section__title">@lang('messages.History records')</h2>
                        </div>
                        <div class="orders-section__count">{{ $historyItems->total() }}</div>
                    </div>

                    @if ($historyItems->isEmpty())
                        <div class="orders-empty">
                            <h3>@lang('messages.No order history found.')</h3>
                            <p>@lang('messages.Try a different keyword, status, service, or year filter.')</p>
                        </div>
                    @else
                        <div class="orders-history-list">
                            @foreach ($historyItems as $item)
                                <article class="orders-card orders-history-card">
                                    <div class="orders-card__top">
                                        <div class="orders-card__service">
                                            <span class="orders-card__icon">
                                                <i class="fa {{ $serviceIconMap[$item['service']] ?? 'fa-briefcase' }}" aria-hidden="true"></i>
                                            </span>
                                            <div>
                                                <div class="orders-card__orderno">{{ $item['orderno'] }}</div>
                                                <div class="orders-card__service-name">{{ $item['service_label'] }}</div>
                                            </div>
                                        </div>
                                        <span class="orders-status orders-status--{{ $statusToneMap[$item['status']] ?? 'history' }}">
                                            {{ $item['status_label'] }}
                                        </span>
                                        @if ($item['is_quote'])
                                            <span class="orders-status orders-status--quote">
                                                @lang('messages.Quote request')
                                            </span>
                                        @endif
                                    </div>

                                    <div class="orders-card__body">
                                        <h3 class="orders-card__headline">{{ $item['title'] }}</h3>
                                        <p class="orders-card__location">{{ $item['subtitle'] ?: $item['location'] ?: '-' }}</p>

                                        <div class="orders-card__facts orders-history-facts">
                                            <div class="orders-card__fact">
                                                <span>@lang('messages.Service Date')</span>
                                                <strong>{{ $item['date_label'] }}</strong>
                                            </div>
                                            <div class="orders-card__fact">
                                                <span>@lang('messages.Guests')</span>
                                                <strong>{{ $item['guest_label'] }}</strong>
                                            </div>
                                            <div class="orders-card__fact">
                                                <span>@lang('messages.Created')</span>
                                                <strong>{{ $item['created_label'] }}</strong>
                                            </div>
                                            <div class="orders-card__fact">
                                                <span>@lang('messages.Total Price')</span>
                                                <strong>{{ currencyFormatUsd($item['price']) }}</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="orders-card__bottom">
                                        <div class="orders-history-invoices">
                                            @if (count($item['invoice_links']) > 0)
                                                @foreach ($item['invoice_links'] as $invoiceLink)
                                                    <a class="orders-history-invoice-link" href="{{ $invoiceLink['url'] }}" target="_blank" rel="noopener">
                                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                        @lang('messages.Invoice') {{ $invoiceLink['label'] }}
                                                    </a>
                                                @endforeach
                                            @else
                                                <span class="orders-history-muted">@lang('messages.No invoice document available')</span>
                                            @endif
                                        </div>

                                        <div class="orders-card__actions">
                                            <a class="btn btn-primary" href="{{ $item['detail_url'] }}">
                                                @lang('messages.View Order')
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="orders-history-pagination">
                            {{ $historyItems->links() }}
                        </div>
                    @endif
                </section>
            </div>
        </section>
    </div>
@endsection
