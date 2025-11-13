@extends('frontend.layouts.app')
@section('title', __('messages.Accommodations'))
@section('content')
    <!-- Header Start -->
    <div class="container-fluid hero-header header-img-hotel bg-light py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6 animated fadeIn">
                    <h1 class="display-4 mb-3 animated slideInDown">@lang('messages.Accommodation')</h1>
                    <p class="text-primary fs-5 mb-4 animated slideInDown">@lang('messages.Partner with us and gain exclusive access to a selection of premium accommodations. We offer a curated collection of luxury hotels and resorts that ensure comfort and unforgettable experiences for your clients. Book with Bali Kami Tour for a seamless travel experience.')</p>
                </div>
            </div>
        </div>
    </div>
    <div class="container my-5">
        <div class="search-container flex-end mb-3">
            <div class="search-item">
                <input type="text" id="searchHotel" class="form-control" placeholder="@lang('messages.Search hotel by name')">
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
        @if($hotels->count() > 0)
        <div class="card-box-content">
            @foreach($hotels as $hotel)
                <div class="card hotel-item" data-region="{{ strtolower($hotel->region) }}">
                    <img src="{{ getThumbnail('/hotels/hotels-cover/' . $hotel->cover,380,200) }}" class="thumbnail-image" loading="lazy">
                    <div class="card-body">
                        <div class="card-title">{{ $hotel->name }}</div>
                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('view.accommodation-detail', $hotel->code) }}" class="btn btn-primary">@lang('messages.View Details')</a>
                    </div>
                </div>
            @endforeach
        </div>
        @else
            <p>@lang('messages.No service available at the moment.')</p>
        @endif
    </div>
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            function filterHotels() {
                let query = $("#searchHotel").val().toLowerCase();
                let region = $("#searchRegion").val().toLowerCase();
    
                $(".hotel-item").each(function () {
                    let hotelName = $(this).find(".card-title").text().toLowerCase();
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