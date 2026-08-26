@extends('layouts.head')

@section('title', __('reservations.detail_title'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/reservations/detail.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/reservations/detail.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main
            class="main-container reservation-detail-page print-area"
            data-reservation-detail
            data-reservation-print-area
        >
            <div class="pd-ltr-20">
                <header class="reservation-detail-print-header">
                    <div>
                        <span>{{ __('reservations.summary') }}</span>
                        <h1>{{ $reservationOverview['reference'] }}</h1>
                        <p>{{ $reservationOverview['service'] }}</p>
                    </div>
                    <dl>
                        <div><dt>{{ __('reservations.status') }}</dt><dd>{{ $reservationOverview['status'] }}</dd></div>
                        <div><dt>{{ __('reservations.agent') }}</dt><dd>{{ $reservationOverview['agent_name'] }}</dd></div>
                        <div><dt>{{ __('reservations.service_period') }}</dt><dd>{{ $reservationOverview['checkin'] }} — {{ $reservationOverview['checkout'] }}</dd></div>
                    </dl>
                </header>

                <x-backend.page-hero
                    class="reservation-detail-hero"
                    eyebrow="{{ __('reservations.detail_eyebrow') }}"
                    title="{{ $reservationOverview['reference'] }}"
                    description="{{ __('reservations.detail_description') }}"
                >
                    <x-slot name="action">
                        <a href="{{ route('view.reservation') }}" class="backend-page-primary-action">
                            <i class="fa fa-arrow-left" aria-hidden="true"></i>
                            {{ __('reservations.back_to_queue') }}
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <section class="backend-page-toolbar reservation-detail-toolbar">
                    <nav aria-label="{{ __('reservations.breadcrumb') }}">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.panel-main.view') }}">{{ __('reservations.admin_panel') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('view.reservation') }}">{{ __('reservations.title') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $reservationOverview['reference'] }}</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--success">{{ $reservationOverview['status'] }}</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('invalid') || session()->has('error'))
                    <section class="backend-feedback reservation-detail-feedback">
                        @if ($errors->any())
                            <div class="backend-alert backend-alert--danger">
                                <strong>{{ __('reservations.action_attention') }}</strong>
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

                <section class="backend-kpi-grid backend-kpi-grid--4" aria-label="{{ __('reservations.detail_summary') }}">
                    @foreach ($reservationStats as $stat)
                        <article class="backend-kpi-card backend-kpi-card--{{ $stat['tone'] }}">
                            <div class="backend-kpi-card__icon" aria-hidden="true"><i class="{{ $stat['icon'] }}"></i></div>
                            <div><span>{{ $stat['label'] }}</span><strong>{{ number_format($stat['value']) }}</strong><small>{{ $stat['meta'] }}</small></div>
                        </article>
                    @endforeach
                </section>

                <x-backend.detail-layout class="reservation-detail-layout">
                    <x-slot name="main">
                        @include('backend.operations.reservations.partials.overview')
                        @include('backend.operations.reservations.partials.manifest')
                        @include('backend.operations.reservations.partials.services')
                        @include('backend.operations.reservations.partials.trip-notes')
                    </x-slot>

                    <x-slot name="side">
                        @include('backend.operations.reservations.partials.context')
                    </x-slot>
                </x-backend.detail-layout>
            </div>
        </main>
    @endcan
@endsection
