@extends('layouts.app')

@section('title', $tour->name)

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('messages.Home')</a></li>
        <li class="breadcrumb-item"><a href="{{ route('tour-package-service') }}">@lang('messages.Tour Packages')</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $tour->name }}</li>
    </ol>
</nav>
<div class="container-fluid my-5">
    <div class="container py-5">
        <div class="row">
            <!-- Hotel Cover Image -->
            <div class="col-md-6 p-b-3">
                <div class="cover-img">
                    <img src="{{ $tour->cover?asset('storage/tours/tours-cover/' . $tour->cover):asset('images/default.webp') }}" class="thumbnail-image" loading="lazy">
                </div>
            </div>
            <!-- Hotel Information -->
            <div class="col-md-6 p-b-3">
                <h1 class="display-4">{{ $tour->name }}</h1>
                <p><strong>@lang('messages.Code'):</strong> {{ $tour->code }}</p>
                <p><strong>@lang('messages.Region'):</strong> {{ $tour->region }}</p>
                <p><strong>@lang('messages.Type'):</strong> {{ $tour->type }}</p>
                <p><strong>@lang('messages.Duration'):</strong> {{ $tour->duration }}</p>
                <p><strong>@lang('messages.Destination'):</strong> {!! $tour->destination !!}</p>
            </div>
            <div class="col-md-12 p-b-3">
                @if ($tour->description)
                    {!! $tour->description !!}
                @endif
            </div>
            <div class="col-md-12 p-b-3">
                <div class="galery-container">
                    @foreach ($tour->images as $image)
                    <div class="img-galery shadow-sm" data-bs-toggle="modal" 
                        data-bs-target="#galeryModal" 
                        data-tour-name="{{ $tour->name }}"
                        data-image="{{ asset('storage/tours/tours-galery/' . $image->image) }}">
                            <img src="{{ asset('storage/tours/tours-galery/' . $image->image) }}"
                                 class="thumbnail-image"
                                 loading="lazy"
                                 data-image="{{ asset('storage/tours/tours-galery/' . $image->image) }}">
                        </div>
                    @endforeach
                </div>
                @include('home.partials.galery-modal')
            </div>
            <div class="col-md-12">
                @if ($tour->itinerary)
                    <h5>@lang('messages.Itinerary')</h5>
                    {!! $tour->itinerary !!}
                @endif
                @if ($tour->include)
                    <div style="height: 30px;"></div>
                    <h5>@lang('messages.Inclusion')</h5>
                    {!! $tour->include !!}
                @endif
                @if ($tour->exclude)
                    <div style="height: 30px;"></div>
                    <h5>@lang('messages.Exclusion')</h5>
                    {!! $tour->exclude !!}
                @endif
                @if ($tour->additional_info)
                    <div style="height: 30px;"></div>
                    <h5>@lang('messages.Additional Information')</h5>
                    {!! $tour->additional_info !!}
                @endif
                @if ($tour->terms_and_conditions)
                    <div style="height: 30px;"></div>
                    <h5>@lang('messages.Terms and Conditions')</h5>
                    {!! $tour->terms_and_conditions !!}
                @endif
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
                <a href="{{ route('view.tour-detail',$tour->code) }}" class="btn-primary btn-book">@lang('messages.Book as Partner')</a>
            </div>
        </div>
    </div>
</div>
<a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>
<script>
    const galeryModal = document.getElementById('galeryModal');
    const galeryModalImage = document.getElementById('galeryModalImage');
    const galeryModalTitle = document.getElementById('galeryModalTitle');

    galeryModal.addEventListener('show.bs.modal', function (event) {
        const card = event.relatedTarget;
        const imageSrc = card.getAttribute('data-image');
        const tourName = card.getAttribute('data-tour-name');
        galeryModalImage.src = imageSrc;
        galeryModalTitle.textContent = tourName;
    });
</script>
@endsection

