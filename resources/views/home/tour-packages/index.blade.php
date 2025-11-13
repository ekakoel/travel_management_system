@extends('layouts.app')
@section('title', 'Tour Packages')
@section('content')
    <!-- Header Start -->
    <div class="container-fluid hero-header header-img-tour bg-light py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6 animated fadeIn">
                    <h1 class="display-4 mb-3 animated slideInDown">@lang('messages.Tour Packages')</h1>
                    <p class="text-primary fs-5 mb-4 animated slideInDown">@lang('messages.We offer a wide range of private and customizable tour packages designed to deliver authentic and memorable experiences. From scenic landscapes to cultural explorations, each itinerary is thoughtfully crafted to suit your clients’ interests, preferences, and travel goals. With expert guides and seamless service, we ensure every journey is unique, comfortable, and enriching.')</p>
                </div>
            </div>
        </div>
    </div>
    <div class="container py-5 my-5">
        <div class="search-container flex-end">
            <div class="search-item">
                <select id="searchType" class="custom-select">
                    <option value="">@lang('messages.All Type')</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
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
        @if($tours->count() > 0)
            <div class="card-box-content">
                @foreach($tours as $tour)
                    <div class="card hotel-item" data-type="{{ strtolower($tour->type) }}" data-region="{{ strtolower($tour->region) }}">
                        <img src="{{ $tour->cover?asset('storage/tours/tours-cover/' . $tour->cover):asset('images/default.webp') }}" class="thumbnail-image" loading="lazy">
                        <div class="card-body">
                            <h5 class="card-title">{{ $tour->name }}</h5>
                            <p class="card-text">
                                {{ $tour->address }}
                            </p>
                        </div>
                        <div class="card-footer text-right">
                            <a href="{{ route('tour-package.show', $tour->id) }}" class="btn btn-primary">@lang('messages.View Details')</a>
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
                let query = $("#searchType").val().toLowerCase();
                let region = $("#searchRegion").val().toLowerCase();
    
                $(".hotel-item").each(function () {
                    let tourType = $(this).data("type");
                    let tourRegion = $(this).data("region");
    
                    if ((tourType.includes(query) || query === "") && 
                        (tourRegion.includes(region) || region === "")) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
    
            $("#searchType, #searchRegion").on("input change", filterHotels);
        });
    </script>
@endsection