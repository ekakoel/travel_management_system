@section('title', __('messages.Weddings'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="info-action">
        @if (\Session::has('error'))
            <div class="alert alert-danger">
                <ul>
                    <li>{!! \Session::get('error') !!}</li>
                </ul>
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
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title"><i class="icon-copy dw dw-hotel-o"></i>&nbsp; @lang('messages.Wedding Venues')</div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard">@lang('messages.Dashboard')</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">@lang('messages.Wedding Venues')</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="icon-copy dw dw-hotel"></i> @lang("messages.Wedding Venues")</div>
                        </div>
                        
                        <form action="/wedding-search" method="POST" role="search";>
                            {{ csrf_field() }}
                            <div class="search-container flex-end">
                                <div class="search-item">
                                    <input type="text" class="form-control" name="hotel_name" placeholder="@lang("messages.Search by hotel")" value="{{ old('hotel_name') }}">
                                </div>
                                <button type="submit" class="btn-search btn-primary"><i class='icon-copy fa fa-search' aria-hidden='true'></i> @lang("messages.Search")</button>
                            </div>
                        </form>
                        <div class="card-box-content">
                            @foreach ($hotels as $hotel)
                                @if (count($hotel->weddings)>0 or count($hotel->wedding_venue)>0 or count($hotel->dinner_packages)>0)
                                    <div class="trans-card">
                                        <div class="trans-img-container">
                                            <div class="top-lable">
                                                
                                                <i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>
                                                <a target="__blank" href="{{ $hotel->map }}">
                                                    <p class="text">{{ $hotel->region }}</p>
                                                </a>
                                            </div>
                                            <a href="wedding-hotel-{{ $hotel->code }}">
                                                <div id="loading-spinner" style="display: none;">
                                                    <i class="fa fa-spinner fa-spin"></i>
                                                </div>
                                                <img src="{{ asset('storage/hotels/hotels-cover/' . $hotel->cover) }}" class="img-fluid rounded thumbnail-image" onload="hideSpinner()">
                                                <div class="front-image-title">
                                                    <p>{{ $hotel->name }}</p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.footer')
        </div>
    </div>
@endsection
