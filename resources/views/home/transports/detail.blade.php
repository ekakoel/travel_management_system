@extends('layouts.app')

@section('title', $transport->name)

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('messages.Home')</a></li>
        <li class="breadcrumb-item"><a href="{{ route('transport-service') }}">@lang('messages.Transports')</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $transport->brand." - ".$transport->name }}</li>
    </ol>
</nav>
<div class="container-fluid my-5">
    <div class="container">
        <div class="row">
            <!-- Transport Cover Image -->
            <div class="col-md-6 my-5">
                <div class="cover-img">
                    <img src="{{ $transport->cover?asset('storage/transports/transports-cover/' . $transport->cover):asset('images/default.webp') }}" class="thumbnail-image" loading="lazy">
                </div>
            </div>
            <!-- Transport Information -->
            <div class="col-md-6 my-5">
                <h1 class="display-4">{{ $transport->brand." - ".$transport->name }}</h1>
                @if ($transport->description)
                    {!! $transport->description !!}
                    <div style="height: 30px;"></div>
                @endif
                <p><strong>@lang('messages.Capacity'):<br></strong> {{ $transport->capacity }} @lang('messages.persons')</p>
                <p><strong>@lang('messages.Services'):<br></strong> @lang('messages.Airport Shuttle'), @lang('messages.Hotel Transfer'), @lang('messages.Daily Rent')</p>
                <p><strong>@lang('messages.Inclusion'):</strong><br> {!! $transport->include !!}</p>
            </div>
        </div>
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
                <a href="{{ route('view.transport-detail',$transport->code) }}" class="btn-primary btn-book">@lang('messages.Book as Partner')</a>
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

