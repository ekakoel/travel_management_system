@extends('layouts.app')
@section('title', 'Transports')
@section('content')
    <!-- Header Start -->
    <div class="container-fluid hero-header header-img-transport bg-light py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6 animated fadeIn">
                    <h1 class="display-4 mb-3 animated slideInDown">@lang('messages.Transports')</h1>
                    <p class="text-primary fs-5 mb-4 animated slideInDown">@lang('messages.Partner with us to access reliable and comfortable transport options across Bali. From private cars to group shuttles, our trusted fleet is ready to serve your travel needs with professionalism and punctuality. Travel smoothly with Bali Kami Tour.')</p>
                </div>
            </div>
        </div>
    </div>
    <div class="container py-5 my-5">
        <div class="search-container flex-end">
            <div class="search-item">
                <input type="text" id="searchTransport" class="form-control" placeholder="@lang('messages.Search by name')">
            </div>
            <div class="search-item">
                <select id="searchType" class="custom-select">
                    <option value="">@lang('messages.All Type')</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @if($transports->count() > 0)
        <div class="card-box-content">
            @foreach($transports as $transport)
                <div class="card transport-item" data-type="{{ strtolower($transport->type) }}">
                    <img src="{{ $transport->cover?asset('storage/transports/transports-cover/' . $transport->cover):asset('images/default.webp') }}" class="thumbnail-image" loading="lazy">
                    <div class="card-body">
                        <h5 class="card-title">{{ $transport->name }}</h5>
                        <p class="card-text">
                            {{ $transport->address }}
                        </p>
                    </div>
                    <div class="card-footer text-right">
                        <a href="{{ route('transport.show', $transport->id) }}" class="btn btn-primary">@lang('messages.View Details')</a>
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
                let query = $("#searchTransport").val().toLowerCase();
                let type = $("#searchType").val().toLowerCase();
    
                $(".transport-item").each(function () {
                    let transportName = $(this).find(".card-title").text().toLowerCase();
                    let transportType = $(this).data("type");
    
                    if ((transportName.includes(query) || query === "") && 
                        (transportType.includes(type) || type === "")) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
    
            $("#searchTransport, #searchType").on("input change", filterHotels);
        });
    </script>
@endsection