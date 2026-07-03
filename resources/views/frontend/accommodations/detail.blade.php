@extends('frontend.layouts.app')
@section('title', $hotel->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/accommodation-detail.css') }}">
@endpush

@section('content')
    <div class="frontend-page-shell accommodation-detail-page">
        <section class="container-fluid frontend-page-topband accommodation-detail-topband py-5">
            <div class="container py-4">
                <nav aria-label="breadcrumb" class="frontend-breadcrumb-wrap">
                    <ol class="breadcrumb frontend-breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('messages.Home')</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('view.accommodation-service') }}">@lang('messages.Accommodation')</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $hotel->name }}</li>
                    </ol>
                </nav>

                <div class="frontend-page-intro">
                    <div class="frontend-page-intro__copy">
                        <span class="frontend-page-intro__eyebrow">@lang('messages.Accommodation Detail')</span>
                        <h1 class="frontend-page-intro__title">{{ $hotel->name }}</h1>
                        <p class="frontend-page-intro__text">
                            @lang('messages.Review hotel highlights, room collection, and partner-ready property information before continuing to the dedicated accommodation check price flow.')
                        </p>
                    </div>
                    <div class="frontend-page-summary">
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Region')</span>
                            <strong>{{ $hotel->region ?: '-' }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Airport Distance')</span>
                            <strong>{{ $hotel->airport_distance ? $hotel->airport_distance . ' ' . __('messages.Km') : '-' }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Airport Duration')</span>
                            <strong>{{ $hotel->airport_duration ? $hotel->airport_duration . ' ' . __('messages.Hours') : '-' }}</strong>
                        </div>
                        <div class="frontend-page-summary__item">
                            <span>@lang('messages.Rooms')</span>
                            <strong>{{ $hotel->rooms->count() }} @lang('messages.collections')</strong>
                        </div>
                    </div>
                </div>

                @if (request()->boolean('check_price'))
                    <div class="alert alert-info mt-4 mb-0" role="status" aria-live="polite">
                        <strong>@lang('messages.Please select stay dates to continue.')</strong>
                        <div>@lang('messages.Choose check-in and check-out dates below to see live accommodation pricing.')</div>
                    </div>
                @endif
            </div>
        </section>

        <div class="container frontend-content-section">
            @include('partials.alerts')

            @if (session('bookingcode'))
                <section class="frontend-surface-card mb-4">
                    <div class="bookingcode-card mb-0">
                        <div class="icon-card bookingcode">
                            <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                        </div>
                        <div class="content-card">
                            <div class="code">{{ session('bookingcode.code') }}</div>
                            <div class="text-card">@lang('messages.Booking Code') @lang('messages.Aplied')</div>
                            <div class="text-card">@lang('messages.Expired') {{ dateFormat(session('bookingcode.expired_date')) }}</div>
                        </div>
                        <div class="content-card-price">
                            <div class="price"><span>$</span>{{ session('bookingcode.discounts') }}</div>
                            <form id="removeBookingCode" action="{{ route('bookingcode.remove') }}" method="POST" style="display: inline;">
                                @csrf
                            </form>
                            <button type="submit" form="removeBookingCode" class="btn-remove-code">
                                <i class="fa fa-close" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </section>
            @endif

            @if ($errors->any() || session('warning') || session('error'))
                <section class="frontend-surface-card mb-4">
                    @if ($errors->any())
                        <div class="alert alert-danger mb-0">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-danger mb-0">
                            {!! session('warning') !!}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger mb-0">
                            @if (is_array(session('error')))
                                @foreach (session('error') as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            @else
                                {{ session('error') }}
                            @endif
                        </div>
                    @endif
                </section>
            @endif

            <section class="accommodation-hero frontend-surface-card">
                <div class="accommodation-hero__media">
                    <img
                        src="{{ $hotel->cover ? asset('storage/hotels/hotels-cover/' . $hotel->cover) : asset('images/default.webp') }}"
                        alt="{{ $hotel->name }}"
                        loading="lazy"
                        onerror="this.onerror=null;this.src='{{ asset('images/default.webp') }}';"
                    >
                </div>
                <div class="accommodation-hero__content">
                    <div class="accommodation-kicker">@lang('messages.Property Overview')</div>
                    <h2 class="accommodation-title">@lang('messages.Partner-ready accommodation profile')</h2>
                    <p class="accommodation-subtitle">
                        @lang('messages.A concise property snapshot for travel agents, with quick access to room previews and the dedicated price-check flow.')
                    </p>

                    <div class="accommodation-meta">
                        <div class="accommodation-meta__item">
                            <span class="accommodation-meta__label">@lang('messages.Address')</span>
                            <strong>{{ $hotel->address ?: '-' }}</strong>
                        </div>
                        <div class="accommodation-meta__item">
                            <span class="accommodation-meta__label">@lang('messages.Content Sections')</span>
                            <strong>{{ 1 + ($hotel->localized_facility ? 1 : 0) + ($hotel->localized_benefits ? 1 : 0) + ($hotel->rooms->count() > 0 ? 1 : 0) }} @lang('messages.sections')</strong>
                        </div>
                        <div class="accommodation-meta__item">
                            <span class="accommodation-meta__label">@lang('messages.Room Preview')</span>
                            <strong>{{ $hotel->rooms->count() > 0 ? __('messages.Available') : __('messages.Not available') }}</strong>
                        </div>
                        <div class="accommodation-meta__item">
                            <span class="accommodation-meta__label">@lang('messages.Partner Flow')</span>
                            <strong>@lang('messages.Detail to check price')</strong>
                        </div>
                    </div>
                </div>
            </section>

            <div class="accommodation-layout">
                <div class="accommodation-main">
                    <section class="accommodation-section frontend-surface-card">
                        <div class="accommodation-section__header">
                            <div>
                                    <div class="accommodation-section__eyebrow">@lang('messages.About This Property')</div>
                                    <h2 class="accommodation-section__title">@lang('messages.Essential property information')</h2>
                            </div>
                        </div>

                        <div class="accommodation-richtext">
                            @if ($hotel->localized_description)
                                {!! $hotel->localized_description !!}
                            @else
                                <p>@lang('messages.Property description is not available yet.')</p>
                            @endif
                        </div>
                    </section>

                    @if ($hotel->localized_facility)
                        <section class="accommodation-section frontend-surface-card">
                            <div class="accommodation-section__header">
                                <div>
                                    <div class="accommodation-section__eyebrow">@lang('messages.Facilities')</div>
                                    <h2 class="accommodation-section__title">@lang('messages.On-property facilities and amenities')</h2>
                                </div>
                            </div>

                            <div class="accommodation-richtext accommodation-richtext--compact">
                                {!! $hotel->localized_facility !!}
                            </div>
                        </section>
                    @endif

                    @if ($hotel->localized_benefits)
                        <section class="accommodation-section frontend-surface-card">
                            <div class="accommodation-section__header">
                                <div>
                                    <div class="accommodation-section__eyebrow">@lang('messages.Benefits')</div>
                                    <h2 class="accommodation-section__title">@lang('messages.Partner-selling highlights')</h2>
                                </div>
                            </div>

                            <div class="accommodation-benefits">
                                @foreach (explode(',', $hotel->localized_benefits) as $benefit)
                                    <div class="accommodation-benefit">
                                        <span class="accommodation-benefit__icon"><i class="fa fa-check"></i></span>
                                        <div class="accommodation-benefit__text">{!! trim($benefit) !!}</div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($hotel->rooms->count() > 0)
                        <section class="accommodation-section frontend-surface-card">
                            <div class="accommodation-section__header">
                                <div>
                                    <div class="accommodation-section__eyebrow">@lang('messages.Suites and Villas')</div>
                                    <h2 class="accommodation-section__title">@lang('messages.Room collection preview')</h2>
                                </div>
                                <div class="accommodation-section__range">{{ $hotel->rooms->count() }} @lang('messages.room types')</div>
                            </div>

                            <div class="accommodation-room-grid">
                                @foreach ($hotel->rooms as $room)
                                    <article
                                        class="accommodation-room-card"
                                        data-bs-toggle="modal"
                                        data-bs-target="#roomModal"
                                        data-image="{{ asset('storage/hotels/hotels-room/' . $room->cover) }}"
                                        data-room-name="{{ $room->rooms }}"
                                    >
                                        <div class="accommodation-room-card__image">
                                            <img
                                                src="{{ getThumbnail('/hotels/hotels-room/' . $room->cover, 380, 200) }}"
                                                alt="{{ $room->rooms }}"
                                                loading="lazy"
                                            >
                                        </div>
                                        <div class="accommodation-room-card__body">
                                            <h3 class="accommodation-room-card__title">{{ $room->rooms }}</h3>
                                            <div class="accommodation-room-card__link">@lang('messages.View room preview')</div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>

                        @include('home.partials.room-modal')
                    @endif
                </div>

                <aside class="accommodation-sidebar">
                    @if ($canUseCheckPriceForm)
                        <div id="check-price-panel" class="hotel-detail-check-price-panel">
                            @include('partials.hotel-check-price-card', [
                                'formId' => 'accommodationDetailCheckPrice',
                            ])
                        </div>
                    @else
                        <div class="accommodation-cta frontend-surface-card">
                            <h2 class="accommodation-cta__title">@lang('messages.Check Price')</h2>
                            <p class="accommodation-cta__text">
                                {{ $checkPriceCta['text'] }}
                            </p>
                            <a href="{{ $checkPriceCta['url'] }}" class="btn-primary btn-book accommodation-cta__button">
                                {{ $checkPriceCta['button_label'] }}
                            </a>
                        </div>
                    @endif
                </aside>
            </div>
        </div>

        <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    @push('scripts')
        <script src="{{ asset('frontend/js/pages/accommodation-detail.js') }}?v={{ filemtime(public_path('frontend/js/pages/accommodation-detail.js')) }}"></script>
    @endpush
@endsection
