@extends('frontend.layouts.app')
@section('title', __('messages.Home'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/frontend-home-entry.css') }}">
@endpush

@section('content')
    @include('frontend.partials.spinner')
    @include('frontend.home.partials.slider')
    @include('frontend.home.partials.hero-b2b')
    @include('frontend.home.partials.services')
    @include('frontend.home.partials.partner-flow')
    @include('frontend.home.partials.hotel-promotion')
    @include('frontend.home.partials.platform-overview')
    @include('frontend.home.partials.benefits')
    @include('frontend.home.partials.faqs-home')
    @include('frontend.home.partials.cta')
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>
@endsection
