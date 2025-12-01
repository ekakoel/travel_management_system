@extends('frontend.layouts.header')
@section('title','Dashboard')
@section('content')
    <section id="hotels">
        <div class="container pt-5">
            <div class="service-title-container m-b-8">
                <div class="service-title"><i class="icon-copy fa fa-certificate" aria-hidden="true"></i> @lang('messages.Hotel Promotions')</div>
                <a href="{{ route('view.hotel-promotions') }}">
                    <button class="btn btn-primary">@lang('messages.Show All')</button>
                </a>
            </div>
            <div class="card-grid-container">
                @foreach ($hotels as $hotel)
                    @php
                        $latestPromo = $hotel->promos->sortByDesc('created_at')->first();
                    @endphp
                    <div class="card-services">
                        <a href="{{ route('view.hotel-detail',$hotel->code) }}">
                            <div class="image-container">
                                <img src="{{ getThumbnail('hotels/hotels-cover/' . $hotel->cover,380,200) }}" loading="lazy">
                                <div class="service-card-title">{{ $hotel->name }}</div>
                            </div>
                        </a>
                        <div class="promo-label">
                            <img src="{{ asset('storage/icon/' . $promoImages[$latestPromo->promotion_type]) }}" alt="{{ $latestPromo->promotion_type }} Promotion">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <section id="transportations">
        <div class="container py-5">
            <div class="service-title-container m-b-8">
                <div class="service-title"><i class="micon fa dw dw-bus" aria-hidden="true"></i> @lang('messages.Transports')</div>
                <a href="{{ route('view.transports') }}">
                    <button class="btn btn-primary">@lang('messages.Show All')</button>
                </a>
            </div>
            <div class="card-grid-container">
                @foreach ($transports as $transport)
                    <div class="card-transport-services">
                        <a href="{{ route('view.transport.detail',$transport->code) }}">
                            <div class="image-container">
                                <div class="card-label">{{ $transport->capacity }} @lang('messages.passengers')</div>
                                <img src="{{ getThumbnail('transports/transports-cover/' . $transport->cover, 380, 200) }}" alt="Transport Image" loading="lazy">
                                <div class="service-card-title">{{ $transport->name }}</div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection