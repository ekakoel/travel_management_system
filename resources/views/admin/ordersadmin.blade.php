@extends('layouts.head')

@section('title', __('admin-orders.title'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/orders-admin/index.css') }}">
@endpush

@section('content')
    @php
        $formatMoney = fn ($amount) => '$ ' . number_format((float) $amount, 0, ',', '.');
        $formatDateRange = function ($order) {
            if ($order->service === 'Transport') {
                return trim(($order->pickup_date ? dateTimeFormat($order->pickup_date) : '-') . ' - ' . ($order->dropoff_date ? dateTimeFormat($order->dropoff_date) : '-'));
            }

            if ($order->service === 'Activity') {
                return $order->travel_date ? dateTimeFormat($order->travel_date) : '-';
            }

            return trim(($order->checkin ? dateFormat($order->checkin) : '-') . ' - ' . ($order->checkout ? dateFormat($order->checkout) : '-'));
        };
        $formatGuests = function ($order) {
            if ($order->service === 'Wedding Package') {
                return trim(($order->groom_name ?: '-') . ' & ' . ($order->bride_name ?: '-'));
            }

            $guestRecords = $order->relationLoaded('guests') ? $order->guests : collect();

            if ($guestRecords->isEmpty() && $order->relationLoaded('reservation_guests')) {
                $guestRecords = $order->reservation_guests;
            }

            $guestNames = $guestRecords
                ->map(function ($guest) {
                    return trim(implode(' ', array_filter([
                        $guest->name,
                        $guest->name_mandarin ? '(' . $guest->name_mandarin . ')' : null,
                    ])));
                })
                ->filter()
                ->values();

            if ($guestNames->isNotEmpty()) {
                return $guestNames->implode(', ');
            }

            $decoded = json_decode($order->guest_detail, true);

            if (is_array($decoded)) {
                return implode(', ', array_filter($decoded)) ?: '-';
            }

            return strip_tags($order->guest_detail ?: '-') ?: '-';
        };
        $statusClass = fn ($status) => \Illuminate\Support\Str::slug($status ?: 'unknown');
        $tourSections = $tourOrderSections ?? collect();
        $weddingSections = $weddingOrderSections ?? collect();
        $focusItems = $orderAdminFocus ?? collect();
        $translateWithFallback = fn ($key, $fallback) => __($key) === $key ? $fallback : __($key);
    @endphp

    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <main class="main-container orders-admin-page" data-orders-admin>
            <div class="pd-ltr-20">
                <x-backend.page-hero class="orders-admin-hero">
                    <x-slot name="kicker">
                        @lang('admin-orders.eyebrow')
                    </x-slot>
                    <x-slot name="heading">
                        @lang('admin-orders.title')
                    </x-slot>
                    <x-slot name="copy">
                        <p>
                            @lang('admin-orders.subtitle')
                        </p>
                    </x-slot>
                </x-backend.page-hero>

                <div class="backend-page-toolbar orders-admin-toolbar">
                    <nav aria-label="{{ __('admin-orders.breadcrumb.label') }}">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">@lang('admin-orders.breadcrumb.admin')</a></li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('admin-orders.breadcrumb.orders')</li>
                        </ol>
                    </nav>
                    <div class="orders-admin-toolbar__actions">
                        <span>@lang('admin-orders.window', ['date' => dateFormat($listed)])</span>
                    </div>
                </div>

                @if(session('success') || session('error_messages') || session('warning') || $errors->any())
                    <div class="backend-feedback orders-admin-feedback">
                        @if(session('success'))
                            <div class="backend-alert backend-alert--success orders-admin-alert orders-admin-alert--success">
                                <strong>@lang('admin-orders.feedback.success')</strong>
                                <span>{!! session('success') !!}</span>
                            </div>
                        @endif
                        @if(session('error_messages'))
                            <div class="backend-alert backend-alert--danger orders-admin-alert orders-admin-alert--danger">
                                <strong>@lang('admin-orders.feedback.error')</strong>
                                <span>{!! session('error_messages') !!}</span>
                            </div>
                        @endif
                        @if(session('warning'))
                            <div class="backend-alert backend-alert--warning orders-admin-alert orders-admin-alert--warning">
                                <strong>@lang('admin-orders.feedback.warning')</strong>
                                <span>{{ session('warning') }}</span>
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="backend-alert backend-alert--danger orders-admin-alert orders-admin-alert--danger">
                                <strong>@lang('admin-orders.feedback.validation')</strong>
                                <span>{{ $errors->first() }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="{{ __('admin-orders.summary.label') }}">
                    <article class="backend-kpi-card backend-kpi-card--teal">
                        <div class="backend-kpi-card__icon">
                            <i class="fa fa-briefcase" aria-hidden="true"></i>
                        </div>
                        <div>
                            <span>@lang('admin-orders.summary.tour')</span>
                            <strong>{{ $orderAdminSummary['tour_total'] ?? 0 }}</strong>
                            <small>@lang('admin-orders.summary.active_window')</small>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--blue">
                        <div class="backend-kpi-card__icon">
                            <i class="fa fa-heart" aria-hidden="true"></i>
                        </div>
                        <div>
                            <span>@lang('admin-orders.summary.wedding')</span>
                            <strong>{{ $orderAdminSummary['wedding_total'] ?? 0 }}</strong>
                            <small>@lang('admin-orders.summary.active_window')</small>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--amber">
                        <div class="backend-kpi-card__icon">
                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                        </div>
                        <div>
                            <span>@lang('admin-orders.summary.pending')</span>
                            <strong>{{ $orderAdminSummary['pending_total'] ?? 0 }}</strong>
                            <small>@lang('admin-orders.summary.needs_action')</small>
                        </div>
                    </article>
                    <article class="backend-kpi-card backend-kpi-card--green">
                        <div class="backend-kpi-card__icon">
                            <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                        </div>
                        <div>
                            <span>@lang('admin-orders.summary.attention')</span>
                            <strong>{{ $orderAdminSummary['attention_total'] ?? 0 }}</strong>
                            <small>@lang('admin-orders.summary.review_required')</small>
                        </div>
                    </article>
                </section>

                <section class="backend-filter-panel orders-admin-filter">
                    <label class="backend-filter-field">
                        <span class="backend-filter-label">@lang('admin-orders.filter.search')</span>
                        <span class="backend-filter-search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            <input
                                class="backend-filter-control"
                                id="ordersAdminSearch"
                                type="search"
                                placeholder="{{ __('admin-orders.filter.placeholder') }}"
                                data-orders-search
                            >
                        </span>
                    </label>
                </section>
                <div class="orders-admin-layout">
                    <aside class="orders-admin-side" aria-label="{{ $translateWithFallback('admin-orders.navigation.label', 'Orders navigation') }}">
                        <section class="orders-admin-side-card orders-admin-focus">
                            <div class="orders-admin-side-card__header">
                                <span>{{ $translateWithFallback('admin-orders.focus.eyebrow', 'Recommended') }}</span>
                                <h2>{{ $translateWithFallback('admin-orders.focus.title', 'Work Focus') }}</h2>
                            </div>
                            <div class="orders-admin-focus-list">
                                @foreach($focusItems as $item)
                                    <a class="orders-admin-focus-item orders-admin-focus-item--{{ $item['tone'] }}" href="{{ $item['href'] }}">
                                        <strong>{{ $item['count'] }}</strong>
                                        <span>{{ $item['label'] }}</span>
                                        <small>{{ $item['description'] }}</small>
                                    </a>
                                @endforeach
                            </div>
                        </section>

                        <section class="orders-admin-side-card orders-admin-nav">
                            <div class="orders-admin-side-card__header">
                                <span>{{ $translateWithFallback('admin-orders.navigation.eyebrow', 'Quick Jump') }}</span>
                                <h2>{{ $translateWithFallback('admin-orders.navigation.title', 'Order Sections') }}</h2>
                            </div>
                            @canany(['posDev','posAuthor','posRsv'])
                                @foreach($tourSections as $section)
                                    @if($section['orders']->count())
                                        <a href="#{{ $section['id'] }}">
                                            <span class="orders-admin-status-dot orders-admin-status-dot--{{ $section['status'] }}"></span>
                                            {{ $section['label'] }}
                                            <strong>{{ $section['orders']->count() }}</strong>
                                        </a>
                                    @endif
                                @endforeach
                            @endcanany
                            @canany(['posDev','weddingSls','weddingRsv','weddingDvl','weddingAuthor'])
                                @foreach($weddingSections as $section)
                                    @if($section['orders']->count())
                                        <a href="#{{ $section['id'] }}">
                                            <span class="orders-admin-status-dot orders-admin-status-dot--wedding"></span>
                                            {{ $section['label'] }}
                                            <strong>{{ $section['orders']->count() }}</strong>
                                        </a>
                                    @endif
                                @endforeach
                            @endcanany
                        </section>
                    </aside>

                    <div class="orders-admin-sections">
                        @canany(['posDev','posAuthor','posRsv'])
                            @foreach($tourSections as $section)
                                @continue(!$section['orders']->count())
                                <section id="{{ $section['id'] }}" class="backend-panel orders-admin-panel" data-orders-section>
                                    <div class="backend-section-header">
                                        <div>
                                            <span class="backend-section-header__label">@lang('admin-orders.tour.eyebrow')</span>
                                            <h2>{{ $section['label'] }}</h2>
                                            <p>{{ $section['description'] }}</p>
                                        </div>
                                        <span class="orders-admin-count">{{ $section['orders']->count() }}</span>
                                    </div>

                                    <div class="backend-table-wrap orders-admin-desktop-table">
                                        <table class="backend-table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('admin-orders.table.agent')</th>
                                                    <th>@lang('admin-orders.table.order')</th>
                                                    <th>@lang('admin-orders.table.schedule')</th>
                                                    <th>@lang('admin-orders.table.guests')</th>
                                                    <th>@lang('admin-orders.table.price')</th>
                                                    <th class="text-right">@lang('admin-orders.table.action')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($section['orders'] as $order)
                                                    @php
                                                        $agent = $order->user;
                                                        $searchText = implode(' ', [
                                                            $agent?->name,
                                                            $order->orderno,
                                                            $order->service,
                                                            $order->subservice,
                                                            $order->servicename,
                                                            $formatGuests($order),
                                                            $order->status,
                                                        ]);
                                                    @endphp
                                                    <tr data-order-row data-order-search="{{ \Illuminate\Support\Str::lower($searchText) }}">
                                                        <td>
                                                            <strong>{{ $agent?->name ?? '-' }}</strong>
                                                            <small>{{ $order->created_at ? dateFormat($order->created_at) : '-' }}</small>
                                                        </td>
                                                        <td>
                                                            <strong>{{ $order->orderno ?? '-' }}</strong>
                                                            <small>{{ $order->service }}{{ $order->subservice ? ' / ' . $order->subservice : '' }}</small>
                                                            <span class="backend-status-badge backend-status-badge--{{ $statusClass($order->status) }} orders-admin-status orders-admin-status--{{ $statusClass($order->status) }}">{{ $order->status ?? '-' }}</span>
                                                        </td>
                                                        <td>{{ $formatDateRange($order) }}</td>
                                                        <td>{{ $formatGuests($order) }}</td>
                                                        <td>
                                                            @if($order->request_quotation === 'Yes')
                                                                <span class="orders-admin-muted">@lang('admin-orders.table.quote')</span>
                                                            @else
                                                                <strong>{{ $formatMoney($order->final_price) }}</strong>
                                                            @endif
                                                        </td>
                                                        <td class="text-right">
                                                            <div class="backend-table-actions">
                                                                <a class="backend-icon-action" href="{{ url('/orders-admin-' . $order->id) }}" aria-label="{{ __('admin-orders.actions.detail') }}">
                                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="backend-table-card-list orders-admin-mobile-list">
                                        @foreach($section['orders'] as $order)
                                            @php
                                                $agent = $order->user;
                                                $searchText = implode(' ', [$agent?->name, $order->orderno, $order->service, $order->subservice, $formatGuests($order), $order->status]);
                                            @endphp
                                            <article class="backend-table-card orders-admin-card" data-order-row data-order-search="{{ \Illuminate\Support\Str::lower($searchText) }}">
                                                <div class="backend-table-card__header">
                                                    <div>
                                                        <span>{{ $agent?->name ?? '-' }}</span>
                                                        <strong>{{ $order->orderno ?? '-' }}</strong>
                                                    </div>
                                                    <span class="backend-status-badge backend-status-badge--{{ $statusClass($order->status) }} orders-admin-status orders-admin-status--{{ $statusClass($order->status) }}">{{ $order->status ?? '-' }}</span>
                                                </div>
                                                <dl class="backend-table-card-grid">
                                                    <div><dt>@lang('admin-orders.table.order')</dt><dd>{{ $order->service }}{{ $order->subservice ? ' / ' . $order->subservice : '' }}</dd></div>
                                                    <div><dt>@lang('admin-orders.table.schedule')</dt><dd>{{ $formatDateRange($order) }}</dd></div>
                                                    <div><dt>@lang('admin-orders.table.guests')</dt><dd>{{ $formatGuests($order) }}</dd></div>
                                                    <div><dt>@lang('admin-orders.table.price')</dt><dd>{{ $order->request_quotation === 'Yes' ? __('admin-orders.table.quote') : $formatMoney($order->final_price) }}</dd></div>
                                                </dl>
                                                <div class="backend-table-actions">
                                                    <a class="backend-button backend-button-secondary" href="{{ url('/orders-admin-' . $order->id) }}">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                        @lang('admin-orders.actions.detail')
                                                    </a>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </section>
                            @endforeach
                        @endcanany

                        @canany(['posDev','weddingSls','weddingRsv','weddingDvl','weddingAuthor'])
                            @foreach($weddingSections as $section)
                                @continue(!$section['orders']->count())
                                <section id="{{ $section['id'] }}" class="backend-panel orders-admin-panel" data-orders-section>
                                    <div class="backend-section-header">
                                        <div>
                                            <span class="backend-section-header__label">@lang('admin-orders.wedding.eyebrow')</span>
                                            <h2>{{ $section['label'] }}</h2>
                                            <p>{{ $section['description'] }}</p>
                                        </div>
                                        <span class="orders-admin-count">{{ $section['orders']->count() }}</span>
                                    </div>

                                    <div class="backend-table-wrap orders-admin-desktop-table">
                                        <table class="backend-table">
                                            <thead>
                                                <tr>
                                                    <th>@lang('admin-orders.table.agent')</th>
                                                    <th>@lang('admin-orders.table.order')</th>
                                                    <th>@lang('admin-orders.table.couple')</th>
                                                    <th>@lang('admin-orders.table.schedule')</th>
                                                    <th>@lang('admin-orders.table.price')</th>
                                                    <th class="text-right">@lang('admin-orders.table.action')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($section['orders'] as $order)
                                                    @php
                                                        $couple = $order->bride ? trim($order->bride->groom . ' & ' . $order->bride->bride) : '-';
                                                        $searchText = implode(' ', [$order->agent?->name, $order->orderno, $order->service, $couple, $order->status]);
                                                    @endphp
                                                    <tr data-order-row data-order-search="{{ \Illuminate\Support\Str::lower($searchText) }}">
                                                        <td>
                                                            <strong>{{ $order->agent?->name ?? '-' }}</strong>
                                                            <small>{{ $order->created_at ? dateFormat($order->created_at) : '-' }}</small>
                                                        </td>
                                                        <td>
                                                            <strong>{{ $order->orderno ?? '-' }}</strong>
                                                            <small>{{ $order->hotel?->name ?? $order->service }}</small>
                                                            <span class="backend-status-badge backend-status-badge--{{ $statusClass($order->status) }} orders-admin-status orders-admin-status--{{ $statusClass($order->status) }}">{{ $order->status ?? '-' }}</span>
                                                        </td>
                                                        <td>
                                                            <strong>{{ $couple }}</strong>
                                                            <small>{{ $order->number_of_invitation ?? 0 }} @lang('admin-orders.table.invitations')</small>
                                                        </td>
                                                        <td>{{ $order->checkin ? dateFormat($order->checkin) : '-' }} - {{ $order->checkout ? dateFormat($order->checkout) : '-' }}</td>
                                                        <td><strong>{{ $formatMoney($order->final_price) }}</strong></td>
                                                        <td class="text-right">
                                                            <div class="backend-table-actions">
                                                                <a class="backend-icon-action" href="{{ url('/validate-orders-wedding-' . $order->id) }}" aria-label="{{ __('admin-orders.actions.detail') }}">
                                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="backend-table-card-list orders-admin-mobile-list">
                                        @foreach($section['orders'] as $order)
                                            @php
                                                $couple = $order->bride ? trim($order->bride->groom . ' & ' . $order->bride->bride) : '-';
                                                $searchText = implode(' ', [$order->agent?->name, $order->orderno, $order->service, $couple, $order->status]);
                                            @endphp
                                            <article class="backend-table-card orders-admin-card" data-order-row data-order-search="{{ \Illuminate\Support\Str::lower($searchText) }}">
                                                <div class="backend-table-card__header">
                                                    <div>
                                                        <span>{{ $order->agent?->name ?? '-' }}</span>
                                                        <strong>{{ $order->orderno ?? '-' }}</strong>
                                                    </div>
                                                    <span class="backend-status-badge backend-status-badge--{{ $statusClass($order->status) }} orders-admin-status orders-admin-status--{{ $statusClass($order->status) }}">{{ $order->status ?? '-' }}</span>
                                                </div>
                                                <dl class="backend-table-card-grid">
                                                    <div><dt>@lang('admin-orders.table.couple')</dt><dd>{{ $couple }}</dd></div>
                                                    <div><dt>@lang('admin-orders.table.schedule')</dt><dd>{{ $order->checkin ? dateFormat($order->checkin) : '-' }} - {{ $order->checkout ? dateFormat($order->checkout) : '-' }}</dd></div>
                                                    <div><dt>@lang('admin-orders.table.price')</dt><dd>{{ $formatMoney($order->final_price) }}</dd></div>
                                                </dl>
                                                <div class="backend-table-actions">
                                                    <a class="backend-button backend-button-secondary" href="{{ url('/validate-orders-wedding-' . $order->id) }}">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                        @lang('admin-orders.actions.detail')
                                                    </a>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </section>
                            @endforeach
                        @endcanany

                        @if(!$tourSections->sum(fn ($section) => $section['orders']->count()) && !$weddingSections->sum(fn ($section) => $section['orders']->count()))
                            <section class="backend-panel orders-admin-panel">
                                <div class="backend-table-empty">
                                    <i class="fa fa-tags" aria-hidden="true"></i>
                                    <strong>@lang('admin-orders.empty.title')</strong>
                                    <span>@lang('admin-orders.empty.description')</span>
                                </div>
                            </section>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    @endcan
@endsection

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/orders-admin/index.js') }}"></script>
@endpush
