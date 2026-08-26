@php
    $hotelStats = $hotelDetail->stats();
    $pricingAgentRateChart = $hotelDetail->pricingAgentRateChart();
@endphp

@extends('layouts.head')

@section('title', __('messages.Hotel Detail'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/hotels/detail.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/hotels/detail.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container hotel-detail-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="hotel-detail-hero"
                    eyebrow="Operations Inventory"
                    title="{{ $hotel->name }}"
                    description="Review hotel profile, active rooms, contracts, pricing, promo, package inventory, and additional charges from one standardized backend detail page."
                >
                @canany(['posDev','posAuthor'])
                    <x-slot name="action">
                        <a href="{{ route('admin.hotels.edit', $hotel->id) }}" class="backend-page-primary-action">
                            <i class="fa fa-pencil-alt"></i>
                            Edit Hotel
                        </a>
                    </x-slot>
                @endcanany
                </x-backend.page-hero>

                <section class="backend-page-toolbar hotel-detail-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.panel-main.view') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.hotels.index') }}">Hotel Manager</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $hotel->name }}</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--info">{{ dateFormat($now) }}</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('invalid') || session()->has('error'))
                    <section class="backend-feedback hotel-detail-feedback">
                        @if ($errors->any())
                            <div class="backend-alert backend-alert--danger">
                                <strong>Action needs attention.</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session()->has('success'))
                            <div class="backend-alert backend-alert--success">
                                <strong>{{ session('success') }}</strong>
                            </div>
                        @endif

                        @if (session()->has('invalid') || session()->has('error'))
                            <div class="backend-alert backend-alert--danger">
                                <strong>{{ session('invalid') ?? session('error') }}</strong>
                            </div>
                        @endif
                    </section>
                @endif

                <section class="backend-kpi-grid backend-kpi-grid--5" aria-label="Hotel detail summary">
                    @foreach ($hotelStats as $index => $stat)
                        @if ($index === 0)
                            <article class="backend-kpi-card hotel-pricing-agent-chart-card" aria-label="Hotel pricing agent rate chart">
                                <div class="hotel-pricing-agent-chart-card__summary">
                                    <div class="hotel-pricing-agent-chart-card__icon"><i class="fa fa-line-chart"></i></div>
                                    <div>
                                        <span>{{ $pricingAgentRateChart['title'] }}</span>
                                        <strong>{{ $pricingAgentRateChart['value'] }}</strong>
                                        <small>{{ $pricingAgentRateChart['meta'] }}</small>
                                        <small class="hotel-pricing-agent-chart-card__delta hotel-pricing-agent-chart-card__delta--{{ $pricingAgentRateChart['delta_direction'] }}">
                                            <i class="fa fa-arrow-{{ $pricingAgentRateChart['delta_direction'] === 'down' ? 'down' : 'up' }}"></i>
                                            {{ $pricingAgentRateChart['delta'] }} {{ $pricingAgentRateChart['delta_label'] }}
                                        </small>
                                    </div>
                                </div>
                                <div class="hotel-pricing-agent-chart-card__visual">
                                    <svg viewBox="{{ $pricingAgentRateChart['view_box'] }}" role="img" aria-label="Monthly pricing agent rate for {{ $hotel->name }}">
                                        @foreach ($pricingAgentRateChart['scale_labels'] as $scaleLabel)
                                            <text x="0" y="{{ $scaleLabel['y'] + 4 }}" class="hotel-pricing-agent-chart-card__scale">{{ $scaleLabel['label'] }}</text>
                                        @endforeach
                                        @foreach ($pricingAgentRateChart['grid_lines'] as $gridLine)
                                            <line x1="42" x2="304" y1="{{ $gridLine }}" y2="{{ $gridLine }}" class="hotel-pricing-agent-chart-card__grid" />
                                        @endforeach
                                        @foreach ($pricingAgentRateChart['series'] as $series)
                                            <path d="{{ $series['line_path'] }}" class="hotel-pricing-agent-chart-card__line" style="stroke: {{ $series['color'] }}" />
                                            @foreach ($series['points'] as $pointIndex => $point)
                                                <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="2.4" class="hotel-pricing-agent-chart-card__point" style="stroke: {{ $series['color'] }}">
                                                    <title>{{ $series['label'] }} {{ $pricingAgentRateChart['month_labels'][$pointIndex]['label'] ?? '' }}: {{ $point['value_label'] }}</title>
                                                </circle>
                                            @endforeach
                                        @endforeach
                                        @foreach ($pricingAgentRateChart['month_labels'] as $monthLabel)
                                            <text x="{{ $monthLabel['x'] }}" y="111" class="hotel-pricing-agent-chart-card__month-label">{{ $monthLabel['label'] }}</text>
                                        @endforeach
                                    </svg>
                                    <div class="hotel-pricing-agent-chart-card__legend">
                                        @foreach ($pricingAgentRateChart['series'] as $series)
                                            <span><i style="background: {{ $series['color'] }}"></i>{{ $series['label'] }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </article>
                        @endif
                        <article class="backend-kpi-card backend-kpi-card--{{ $stat['tone'] }}">
                            <div class="backend-kpi-card__icon"><i class="{{ $stat['icon'] }}"></i></div>
                            <div>
                                <span>{{ $stat['label'] }}</span>
                                <strong>{{ $stat['value'] }}</strong>
                                <small>{{ $stat['meta'] }}</small>
                            </div>
                        </article>
                    @endforeach
                </section>

                <x-backend.detail-layout class="hotel-detail-layout">
                    <x-slot name="side">
                        @include('backend.operations.hotels.partials.audit-summary')
                    </x-slot>
                    <x-slot name="main">
                        @include('backend.operations.hotels.partials.profile-summary')
                        @include('backend.operations.hotels.partials.contracts')
                        @include('backend.operations.hotels.partials.rooms')
                        @include('backend.operations.hotels.partials.additional-charges')

                        @if ($hotel->rooms->count() > 0)
                            @include('backend.operations.hotels.partials.extra-beds')
                            @include('backend.operations.hotels.partials.normal-prices')
                            @include('backend.operations.hotels.partials.promo-prices')
                            @include('backend.operations.hotels.partials.package-prices')
                        @endif
                    </x-slot>
                </x-backend.detail-layout>
                @include('backend.operations.hotels.modals.contract-preview')
                @include('backend.operations.hotels.modals.room-preview')
            </div>
        </main>
    @endcan
@endsection
