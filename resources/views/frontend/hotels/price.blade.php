@extends('frontend.layouts.header')
@section('title', $hotel->name)
@section('content')
    <div class="mobile-menu-overlay"></div>
    @include('frontend.hotels.partials.hotel-condition-order')
    <div class="body-container mb-9">
        <section id="hotelDetail" class="mb-3">
            <nav class="breadcrumb-nav text-center m-b-18">
                <ol class="breadcrumb-list">
                    <li><a href="{{ route('dashboard.index') }}">@lang('messages.Dashboard')</a></li>
                    <li><a href="{{ route('view.hotels') }}">@lang('messages.Hotel')</a></li>
                    <li><a href="{{ route('view.hotel-detail',$hotel->code) }}">{{ $hotel->name }}</a></li>
                    <li class="active">@lang('messages.Check Price') ({{ dateFormat($checkin)." - ".dateFormat($checkout) }})</li>
                </ol>
            </nav>
            <div class="m-b-8">
                @include('frontend.partials.alert')
            </div>
            <hr>
            <div class="border-bottom">
                @if (count($processedPromos) > 0 || count($packages) > 0 || count($normalPriceData) > 0)
                    <div class="main-content-price">
                        @include('frontend.hotels.partials.hotel-promo-prices')
                        @include('frontend.hotels.partials.hotel-package-prices')
                        @include('frontend.hotels.partials.hotel-normal-prices')
                    </div>
                @else
                    <div class="notification-container">
                        <p>{{ dateFormat($checkin)." - ".dateFormat($checkout) }} ({{ $duration }} @lang('messages.nights'))</p>
                        <div class="notification-blue">@lang('messages.empty_price_notification')</div>
                    </div>
                @endif
                
            </div>
            <div class="content-title mt-4">
                <p>
                    @lang('messages.hotel_terms_title')
                </p>
                <ul>
                    <li>
                        <i>
                            @lang('messages.rates_usd_desc')
                        </i>
                    </li>
                    <li>
                        <i>
                            @lang('messages.room_availability_desc')
                        </i>
                    </li>
                    <li>
                        <i>
                            @lang('messages.special_periods_desc')
                        </i>
                    </li>
                    <li>
                        <i>
                            @lang('messages.cancellation_policy_desc')
                        </i>
                    </li>
                </ul>
            </div>
        </section>
    </div>
    <div class="similiar-container">
        <section id="similiarHotels">
            <div class="subtitle">
                <i class="icon-copy dw dw-hotel"></i> 
                @lang('messages.Hotels Around') {{ $hotel->region }}
            </div>
            <div class="content-container">
                <div class="card-grid-container">
                    @foreach ($hotels as $hotel)
                        <div class="card-services">
                            <a href="{{ route('view.transport.detail',$hotel->code) }}">
                                <div class="image-container">
                                    <div class="card-label"><i class="icon-copy fa fa-map-marker" aria-hidden="true"></i> {{ $hotel->region }} </div>
                                    <img src="{{ $hotel->cover?asset('storage/hotels/hotels-cover/' . $hotel->cover):asset('storage/images/default.webp') }}" onerror="this.onerror=null;this.src='{{ asset('storage/images/default.webp') }}';" class="thumbnail-image" loading="lazy">
                                    <div class="service-card-title">{{ $hotel->name }}</div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection





