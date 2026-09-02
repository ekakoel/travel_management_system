@extends('layouts.head')
@section('title', 'Admin Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/admin/dashboard/index.css') }}">
@endpush

@section('content')
    <div class="mobile-menu-overlay"></div>
    <main class="main-container admin-dashboard-page">
        <div class="pd-ltr-20">
            <x-backend.page-hero class="admin-dashboard-hero">
                <x-slot name="kicker">
                    Operations Center
                </x-slot>
                <x-slot name="heading">
                    Admin Dashboard
                </x-slot>
                <x-slot name="copy">
                    <p>
                        Monitor service inventory, bookings, reservations, upcoming work, and data that needs attention from one internal workspace.
                    </p>
                </x-slot>
            </x-backend.page-hero>

            <div class="backend-page-toolbar admin-dashboard-toolbar">
                <nav aria-label="Breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $periodLabel }}</li>
                    </ol>
                </nav>
                <form action="{{ route('admin.dashboard') }}" method="get" class="backend-toolbar-filter admin-dashboard-filter">
                    <label class="backend-toolbar-filter__label" for="dashboard-period">Period</label>
                    <select class="backend-toolbar-filter__control" id="dashboard-period" name="period" onchange="this.form.submit()">
                        @foreach ($periodOptions as $value => $label)
                            <option value="{{ $value }}" @selected($period === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <section class="backend-kpi-grid backend-kpi-grid--6" aria-label="Dashboard KPI">
                @foreach ($kpis as $stat)
                    <article class="backend-kpi-card backend-kpi-card--{{ $stat['tone'] }}">
                        <div class="backend-kpi-card__icon"><i class="{{ $stat['icon'] }}"></i></div>
                        <div>
                            <span>{{ $stat['label'] }}</span>
                            <strong>{{ number_format($stat['value']) }}</strong>
                            <small>{{ $stat['meta'] }}</small>
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="backend-panel m-b-18">
                <div class="backend-section-header m-b-18">
                    <div>
                        <span class="backend-section-header__label">Service Inventory</span>
                        <h2>Available Services</h2>
                    </div>
                    <p>Compact overview of active and inactive records across services currently available in the system.</p>
                </div>

                <div class="admin-dashboard-service-grid">
                    @foreach ($services as $service)
                        <a class="admin-dashboard-service" href="{{ $service['route'] ?: '#' }}" aria-disabled="{{ $service['route'] ? 'false' : 'true' }}">
                            <div class="admin-dashboard-service__icon"><i class="{{ $service['icon'] }}"></i></div>
                            <div>
                                <strong>{{ $service['label'] }}</strong>
                                <span>{{ number_format($service['data']['total']) }} total</span>
                                <small>{{ number_format($service['data']['active']) }} active / {{ number_format($service['data']['inactive']) }} inactive</small>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="admin-dashboard-grid">
                <div class="backend-panel">
                    <div class="backend-section-header m-b-18">
                        <div>
                            <span class="backend-section-header__label">Latest Movement</span>
                            <h2>Recent Activity</h2>
                        </div>
                    </div>

                    @if (count($recentActivities) > 0)
                        <div class="backend-list admin-dashboard-list">
                            @foreach ($recentActivities as $item)
                                <a class="backend-list-item admin-dashboard-list__item" href="{{ $item['route'] ?: '#' }}">
                                    <div>
                                        <strong>{{ $item['code'] }}</strong>
                                        <span>{{ $item['name'] }} · {{ $item['type'] }}</span>
                                    </div>
                                    <div class="backend-list-item__meta admin-dashboard-list__meta">
                                        <span class="backend-status-badge backend-status-badge--info admin-dashboard-badge">{{ $item['status'] }}</span>
                                        <small>{{ $item['date'] }}</small>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="backend-empty-state backend-empty-state--compact admin-dashboard-empty">No recent activity available.</div>
                    @endif
                </div>

                <div class="backend-panel">
                    <div class="backend-section-header m-b-18">
                        <div>
                            <span class="backend-section-header__label">Next 14 Days</span>
                            <h2>Upcoming Services</h2>
                        </div>
                    </div>

                    @if (count($upcomingServices) > 0)
                        <div class="backend-list admin-dashboard-list">
                            @foreach ($upcomingServices as $item)
                                <a class="backend-list-item admin-dashboard-list__item" href="{{ $item['route'] ?: '#' }}">
                                    <div>
                                        <strong>{{ $item['code'] }}</strong>
                                        <span>{{ $item['name'] }} · {{ $item['type'] }}</span>
                                    </div>
                                    <div class="backend-list-item__meta admin-dashboard-list__meta">
                                        <span class="backend-status-badge backend-status-badge--info admin-dashboard-badge">{{ $item['status'] }}</span>
                                        <small>{{ $item['date'] }}</small>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="backend-empty-state backend-empty-state--compact admin-dashboard-empty">No upcoming services in the next 14 days.</div>
                    @endif
                </div>
            </section>

            <section class="admin-dashboard-grid">
                <div class="backend-panel">
                    <div class="backend-section-header m-b-18">
                        <div>
                            <span class="backend-section-header__label">Needs Attention</span>
                            <h2>Data Quality</h2>
                        </div>
                    </div>

                    <div class="admin-dashboard-attention-grid">
                        @foreach ($attentionItems as $item)
                            <article class="admin-dashboard-attention admin-dashboard-attention--{{ $item['tone'] }}">
                                <strong>{{ number_format($item['value']) }}</strong>
                                <span>{{ $item['label'] }}</span>
                                <small>{{ $item['meta'] }}</small>
                            </article>
                        @endforeach
                    </div>
                </div>

                <div class="backend-panel">
                    <div class="backend-section-header m-b-18">
                        <div>
                            <span class="backend-section-header__label">Status Distribution</span>
                            <h2>Bookings & Reservations</h2>
                        </div>
                    </div>

                    <div class="admin-dashboard-status">
                        <div>
                            <h3>Bookings</h3>
                            @foreach ($orderStatus as $status => $count)
                                <div class="admin-dashboard-status__row">
                                    <span>{{ $status }}</span>
                                    <strong>{{ number_format($count) }}</strong>
                                </div>
                            @endforeach
                        </div>
                        <div>
                            <h3>Reservations</h3>
                            @foreach ($reservationStatus as $status => $count)
                                <div class="admin-dashboard-status__row">
                                    <span>{{ $status }}</span>
                                    <strong>{{ number_format($count) }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
@endsection
