@extends('layouts.head')

@section('title', __('messages.Hotel Promotions'))
@section('content')

<div class="mobile-menu-overlay"></div>
<div class="main-container">
    <div class="pd-ltr-20">
        <div class="info-action">
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (\Session::has('success'))
                <div class="alert alert-success">
                    <ul>
                        <li>{!! \Session::get('success') !!}</li>
                    </ul>
                </div>
            @endif
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle">
                            <i class="dw dw-hotel"></i> @lang('messages.Hotel Promotions')
                        </div>
                    </div>
                    <div class="search-container flex-end">
                        <div class="search-item">
                            <input type="text" id="searchHotel" class="form-control mb-4" placeholder="@lang('messages.Search hotel by name')">
                        </div>
                        <div class="search-item">
                            <select id="searchRegion" class="custom-select">
                                <option value="">@lang('messages.All Regions')</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region }}">{{ $region }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-box-content">
                        @foreach ($hotels as $hotel)
                            <div class="card hotel-item" data-region="{{ strtolower($hotel->region) }}">
                                @if ($hotel->promos->isNotEmpty())
                                    <div class="persen-promo" data-toggle="tooltip" data-placement="top" title="Promotion" aria-hidden="true">
                                        <img src="{{ asset('storage/icon/persen.png') }}" alt="Promo discount" loading="lazy">
                                    </div>
                                    @php
                                        $promoImages = [
                                            'Hot Deal' => 'hot_deal_promo.png',
                                            'Best Choice' => 'best_choice_promo.png',
                                            'Best Price' => 'best_price_promo.png',
                                            'Special Offer' => 'special_offer_promo.png',
                                        ];
                                    @endphp
                                    @foreach ($hotel->promos as $promo)
                                        @isset($promoImages[$promo->promotion_type])
                                            <div class="promo-hot-deal">
                                                <img src="{{ asset('storage/icon/' . $promoImages[$promo->promotion_type]) }}" alt="{{ $promo->promotion_type }} Promotion">
                                            </div>
                                        @endisset
                                    @endforeach
                                @endif
                                <div class="image-container">
                                    <div class="top-lable">
                                        <i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>
                                        <a target="__blank" href="{{ $hotel->map }}">
                                            {{ $hotel->region }}
                                        </a>
                                    </div>
                                    
                                    <a href="{{ route('view.hotel-detail', $hotel->code) }}">
                                        <img src="{{ $hotel->cover ? getThumbnail('/hotels/hotels-cover/' . $hotel->cover,380,200) : getThumbnail('images/default.webp',380,200) }}" class="thumbnail-image" loading="lazy">
                                        <div class="card-detail-title">{{ $hotel->name }}</div>
                                    </a>
                                </div>
                                <div class="card-action">
                                    <a href="{{ route('view.flyers-detail', $hotel->id) }}" class="btn-download">
                                        <i class="fa fa-download"></i> @lang('messages.Download Flyer')
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>

<style>
    .hotel-card {
        width: 100%;
        aspect-ratio: 3/2;
        overflow: hidden;
        position: relative;
    }

    .hotel-image {
        position: relative;
        height: 100%;
    }

    .hotel-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .hotel-name {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background: rgba(0, 0, 0, 0.5);
        padding: 10px;
        text-align: center;
        border-radius: 0 0 8px 8px;
    }

    .hotel-name h5 {
        margin: 0;
        font-size: 18px;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        function filterHotels() {
            let query = $("#searchHotel").val().toLowerCase();
            let region = $("#searchRegion").val().toLowerCase();

            $(".hotel-item").each(function () {
                let hotelName = $(this).find(".card-detail-title").text().toLowerCase();
                let hotelRegion = $(this).data("region");

                if ((hotelName.includes(query) || query === "") && 
                    (hotelRegion.includes(region) || region === "")) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        $("#searchHotel, #searchRegion").on("input change", filterHotels);
    });
</script>

@endsection
