@extends('frontend.layouts.header')
@section('title', $hotel->name)
@section('content')
    <div class="mobile-menu-overlay"></div>
    @include('frontend.hotels.partials.hotel-condition-order')
    <div class="body-container">
            <section id="hotelDetail" class="mb-4">
            <nav class="breadcrumb-nav text-center m-b-18">
                <ol class="breadcrumb-list">
                    <li><a href="{{ route('dashboard.index') }}">@lang('messages.Dashboard')</a></li>
                    <li><a href="{{ route('view.hotels') }}">@lang('messages.Hotel')</a></li>
                    <li class="active">{{ $hotel->name }}</li>
                </ol>
            </nav>
            @include('frontend.partials.alert')
            <div class="heading-page-detail mb-3">
                <img src="{{ $hotel->cover ? asset('storage/hotels/hotels-cover/' . $hotel->cover) : asset('storage/images/default.webp') }}"
                    onerror="this.onerror=null;this.src='{{ asset('storage/images/default.webp') }}';" loading="lazy">
            </div>
            <div class="content-container mb-5">
                <div class="w-100 mb-3">
                    <p class="mb-18">
                        @if (config('app.locale') == 'zh')
                            {!! $hotel->description_traditional !!}
                        @elseif(config('app.locale') == 'zh-CN')
                            {!! $hotel->description_simplified !!}
                        @else
                            {!! $hotel->description !!}
                        @endif
                    </p>
                    @if ($hotel->facility)
                        <b>@lang('messages.Amenities')</b>
                        <p>
                            @if (config('app.locale') == 'zh')
                                {!! $hotel->facility_traditional !!}
                            @elseif(config('app.locale') == 'zh-CN')
                                {!! $hotel->facility_simplified !!}
                            @else
                                {!! $hotel->facility !!}
                            @endif
                        </p>
                    @endif
                </div>
                <div class="main-content">
                    <div class="content-title mb-3">@lang('messages.Suites & Villas')</div>
                    <div class="card-box-content">
                        @foreach ($hotel->rooms as $room)
                            @include('frontend.partials.modals.room-detail', [
                                'room' => $room,
                                'promoImages' => $promoImages,
                                'now' => $now,
                            ])
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="similiar-container">
        <section id="similiarHotels">
            <div class="content-container service-title-container mb-1">
                <div class="service-title">
                    <i class="icon-copy dw dw-hotel"></i>
                    @lang('messages.Hotels Around') {{ $hotel->region }}
                </div>
            </div>
            <div class="content-container">
                <div class="card-grid-container">
                    @foreach ($hotels as $hotel)
                        <div class="card-services">
                            <a href="{{ route('view.hotel-detail', $hotel->code) }}">
                                <div class="image-container">
                                    <div class="card-label"><i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>
                                        {{ $hotel->region }} </div>
                                    <img src="{{ $hotel->cover ? getThumbnail('/hotels/hotels-cover/' . $hotel->cover, 380, 200) : getThumbnail('/images/default.webp', 380, 200) }}"
                                        onerror="this.onerror=null;this.src='{{ asset('storage/images/default.webp') }}';"
                                        class="thumbnail-image" loading="lazy">
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
