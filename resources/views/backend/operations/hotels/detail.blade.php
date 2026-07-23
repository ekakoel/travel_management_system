@php
    $hotelStats = $hotelDetail->stats();
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
                                <i class="fa fa-pencil"></i>
                                Edit Hotel
                            </a>
                        </x-slot>
                    @endcanany
                </x-backend.page-hero>

                <section class="backend-page-toolbar hotel-detail-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('hotels-admin.index') }}">Hotel Manager</a></li>
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
                    @foreach ($hotelStats as $stat)
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

                        @if ($hotel->rooms->count() > 0)
                            @include('backend.operations.hotels.partials.additional-charges')
                            @include('backend.operations.hotels.partials.normal-prices')
                            @include('backend.operations.hotels.partials.promo-prices')
                            @include('backend.operations.hotels.partials.package-prices')
                        @endif
                    </x-slot>


                </x-backend.detail-layout>

                @include('backend.operations.hotels.modals.contract-preview')
                @include('backend.operations.hotels.modals.room-preview')

                @include('layouts.footer')
            </div>
        </main>
    @endcan
@endsection
