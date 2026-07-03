@php
    $currentPageLabel = $currentPageLabel ?? __('messages.Order');
    $bookingBadge = $bookingBadge ?? __('messages.Order');
    $bookingDescription = $bookingDescription ?? __('messages.Complete guest details, stay preferences, transfer options, and booking notes before sending the hotel booking request.');
    $useAvailabilityFamily = $useAvailabilityFamily ?? false;
    $hotelCover = $hotel->cover
        ? asset('storage/hotels/hotels-cover/' . $hotel->cover)
        : asset('storage/images/default.webp');
    $topbandClasses = 'container-fluid frontend-page-topband hotel-booking-topband py-5';
    $heroClasses = 'hotel-booking-hero frontend-surface-card';

    if ($useAvailabilityFamily) {
        $topbandClasses .= ' frontend-availability-family-topband';
        $heroClasses .= ' frontend-availability-family-hero frontend-availability-family-hero--merged';
    }
@endphp

<section class="{{ $topbandClasses }}">
    <div class="container py-4">
        <nav aria-label="breadcrumb" class="frontend-breadcrumb-wrap">
            <ol class="breadcrumb frontend-breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('messages.Home')</a></li>
                <li class="breadcrumb-item"><a href="{{ route('view.accommodation-service') }}">@lang('messages.Accommodation')</a></li>
                <li class="breadcrumb-item"><a href="{{ route('view.accommodation-detail', $hotel->code) }}">{{ $hotel->name }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('view.hotel-prices.page', ['code' => $hotel->code]) }}">@lang('messages.Check Price')</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $currentPageLabel }}</li>
            </ol>
        </nav>

        <section class="{{ $heroClasses }}">
            <div class="hotel-booking-hero__media">
                <img
                    src="{{ $hotelCover }}"
                    alt="{{ $hotel->name }}"
                    loading="lazy"
                    onerror="this.onerror=null;this.src='{{ asset('storage/images/default.webp') }}';"
                >
            </div>
            <div class="hotel-booking-hero__content">
                <div class="hotel-booking-kicker">{{ $bookingBadge }}</div>
                <h2 class="hotel-booking-hero__title">{{ $hotel->name }}</h2>
                <p class="hotel-booking-hero__text">
                    {{ $bookingDescription }}
                </p>

                <div class="frontend-page-summary hotel-booking-summary">
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
        </section>
    </div>
</section>
