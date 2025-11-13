@extends('layouts.app')

@section('title', $hotel->name)

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $hotel->name }}</li>
    </ol>
</nav>
<div class="container-fluid my-5">
    <div class="container py-5">
        <div class="row">
            <!-- Hotel Cover Image -->
            <div class="col-md-6 my-5">
                <div class="cover-img">
                    <img src="{{ $hotel->cover?asset('storage/hotels/hotels-cover/' . $hotel->cover):asset('images/default.webp') }}" class="thumbnail-image" loading="lazy">
                </div>
            </div>
            <!-- Hotel Information -->
            <div class="col-md-6 my-5">
                <h1 class="display-4">{{ $hotel->name }}</h1>
                <p><strong>@lang('messages.Region'):</strong> {{ $hotel->region }}</p>
                <p><strong>@lang('messages.Address'):</strong> {{ $hotel->address }}</p>
                <p><strong>@lang('messages.Airport Duration'):</strong> {{ $hotel->airport_duration }} minutes</p>
                <p><strong>@lang('messages.Airport Distance'):</strong> {{ $hotel->airport_distance }} km</p>
            </div>
            <div class="col-md-12">
                @if ($hotel->description)
                    <p><strong>@lang('messages.Description'):</strong> {!! $hotel->description !!}</p>
                @endif
                @if ($hotel->facility)
                    <p><strong>@lang('messages.Facilities'):</strong> {!! $hotel->facility !!}</p>
                @endif
                <!-- Benefits -->
                @if ($hotel->benefits)
                    <h4 class="text-success">@lang('messages.Benefits')</h4>
                    <ul class="list-group">
                        @foreach(explode(',', $hotel->benefits) as $benefit)
                            <li class="list-group-item">{!! $benefit !!}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
    <div class="container">
        <!-- Hotel Rooms -->
        @if($hotel->rooms->count() > 0)
            <h3 class="mb-4">@lang('messages.Suites and Villas')</h3>
            <div class="row">
                @foreach($hotel->rooms as $room)
                    <div class="col-md-3 mb-4">
                        <div class="card shadow-sm border-soft rounded hover-effect" data-bs-toggle="modal" 
                        data-bs-target="#roomModal" 
                        data-image="{{ asset('storage/hotels/hotels-room/' . $room->cover) }}"
                        data-room-name="{{ $room->rooms }}">
                            <img src="{{ asset('storage/hotels/hotels-room/' . $room->cover) }}" class="card-img-top" alt="{{ $room->rooms }}" loading="lazy">
                            <div class="card-body">
                                <h5 class="card-title">{{ $room->rooms }}</h5>
                                <p class="card-text">
                                    <strong>@lang('messages.Capacity'):</strong> {{ $room->capacity }} @lang('messages.persons')<br>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @include('home.partials.room-modal')
        @endif
    </div>
</div>
<div class="container-fluid bg-gray py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.1s">
                <h2 class="section-title">@lang('messages.Empowering Partners to Deliver Exceptional Travel Experiences.')</h2>
                <p class="section-subtitle">@lang('messages.Access the booking portal below to reserve services for your clients quickly and efficiently.')</p>
            </div>
            <div class="col-lg-4 wow fadeInUp text-right" data-wow-delay="0.1s">
                <a href="{{ route('view.hotel-detail',$hotel->code) }}" class="btn-primary btn-book">@lang('messages.Book as Partner')</a>
            </div>
        </div>
    </div>
</div>
<a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>
<script>
    const roomModal = document.getElementById('roomModal');
    const roomModalImage = document.getElementById('roomModalImage');
    const roomModalTitle = document.getElementById('roomModalTitle');

    roomModal.addEventListener('show.bs.modal', function (event) {
        const card = event.relatedTarget;
        const imageSrc = card.getAttribute('data-image');
        const roomName = card.getAttribute('data-room-name');
        roomModalImage.src = imageSrc;
        roomModalTitle.textContent = roomName;
    });
</script>
@endsection

