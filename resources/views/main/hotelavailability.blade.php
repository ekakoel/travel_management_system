@extends('frontend.layouts.app')
@section('title', __('messages.Hotel Availability'))

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('panel/styles/icon-font.min.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/hotel-availability-entry.css') }}">
@endpush

@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ mix('build/frontend/js/pages/hotel-availability.js') }}" defer></script>
@endpush

@section('content')
    <div class="frontend-page-shell hotel-availability-page">
        <section class="container-fluid frontend-page-topband availability-topband py-5">
            <div class="container py-4">
                <nav aria-label="breadcrumb" class="frontend-breadcrumb-wrap">
                    <ol class="breadcrumb frontend-breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('messages.Home')</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('view.accommodation-service') }}">@lang('messages.Accommodation')</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('view.accommodation-detail', $hotel->code) }}">{{ $hotel->name }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">@lang('messages.Check Price')</li>
                    </ol>
                </nav>

                <div class="frontend-page-intro">
                    <div class="frontend-page-intro__copy">
                        <h1 class="frontend-page-intro__title">{{ $hotel->name }}</h1>
                        <p class="frontend-page-intro__text">
                            @lang('messages.Live hotel pricing for partner agents, calculated from standard contract rates, hotel promotions, and active packages based on the dates selected by the user.')
                        </p>
                    </div>
                    <div class="frontend-page-summary">
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Region')</span>
                            <strong>{{ $hotel->region }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Stay')</span>
                            <strong>{{ $duration }} @lang('messages.nights')</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Dates')</span>
                            <strong>{{ dateFormat($checkin) }} - {{ dateFormat($checkout) }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Minimum stay')</span>
                            <strong>{{ $hotel->min_stay }} @lang('messages.nights')</strong>
                        </div>
                    </div>
                </div>

                <section class="availability-hero availability-hero--merged">
                    <div class="availability-hero__media">
                        <img
                            src="{{ $hotel->cover ? asset('storage/hotels/hotels-cover/' . $hotel->cover) : asset('storage/images/default.webp') }}"
                            alt="{{ $hotel->name }}"
                            loading="lazy"
                            onerror="this.onerror=null;this.src='{{ asset('storage/images/default.webp') }}';"
                        >
                    </div>
                    <div class="availability-hero__content">
                        <div class="availability-kicker">@lang('messages.Selected Stay')</div>
                        <h2 class="availability-title">@lang('messages.Choose dates and compare available room rates')</h2>
                        <p class="availability-subtitle">
                            @lang('messages.Partner-facing hotel pricing from contract rates, promotions, and packages for the selected stay window.')
                        </p>

                        <p class="availability-description">
                            @lang('messages.This page shows hotel rates pulled from hotel contract prices, hotel promotions, and hotel packages based on the stay dates you selected.')
                        </p>
                    </div>
                </section>
            </div>
        </section>

        <div class="container frontend-content-section">
            @include('partials.alerts')
            @if ($promotions->count() > 0)
                <div class="promotion-container">
                    @foreach ($promotions as $promotion)
                        @include('partials.promotion-card', compact('promotion'))
                    @endforeach
                </div>
            @endif

            <div class="row g-4">
                <div class="col-xl-4 col-lg-5 order-1 order-lg-2">
                    <div class="availability-sidebar availability-sidebar--desktop">
                        @include('partials.check-price', ['formId' => 'hotelCheckPriceFrontend'])
                    </div>
                </div>

                <div class="col-xl-8 col-lg-7 order-2 order-lg-1">
                    @if ($hasAnyResults)
                        <section class="availability-results">
                            @foreach ($rateSections as $section)
                                <div class="availability-section frontend-surface-card">
                                    <div class="availability-section__header">
                                        <div>
                                            <div class="availability-section__eyebrow">{{ $section['eyebrow'] }}</div>
                                            <h2 class="availability-section__title">{{ $section['title'] }}</h2>
                                        </div>
                                        <div class="availability-section__range">
                                            {{ count($section['cards']) }} @lang('messages.options')
                                        </div>
                                    </div>

                                    <div class="availability-card-list">
                                        @foreach ($section['cards'] as $card)
                                            @include('partials.hotel-availability-rate-card', ['card' => $card])
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </section>
                    @else
                        <div class="availability-empty">
                            <div class="availability-empty__icon"><i class="fa fa-search"></i></div>
                            <h2>@lang('messages.No rates found for the selected dates')</h2>
                            <p>@lang('messages.Price cannot be found, please try another date')</p>
                        </div>
                    @endif
                </div>
            </div>

            @if (count($nearhotels) > 0)
                <div class="mt-5">
                    @include('partials.near-hotel', compact('nearhotels'))
                </div>
            @endif
        </div>
        @include('partials.hotel-rate-detail-modal')
        <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>
@endsection
