@extends('layouts.app')

@section('title', 'Hotel Listings')

@section('content')
<div class="container">
    <div class="section-bg-light">
        <h1 class="text-center mb-5">Our Hotel Collection</h1>
        <div class="search-container flex-end">
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
                    <img src="{{ $hotel->cover?asset('storage/hotels/hotels-cover/' . $hotel->cover):asset('images/default.webp') }}" class="thumbnail-image" loading="lazy">
                    <div class="card-body">
                        <h5 class="card-title">{{ $hotel->name }}</h5>
                        <p class="card-text">
                            {{ $hotel->address }}
                            {{-- <strong>Facilities:</strong> {{ Str::limit($hotel->facility, 100) }}<br> --}}
                        </p>
                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('hotel.show', $hotel->id) }}" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            @endforeach
        </div>
        @else
            <p>No hotels available at the moment.</p>
        @endif
    </div>
</div>
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
