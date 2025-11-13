@extends('frontend.layouts.app')
@section('title', __('messages.Home'))
@section('content')
    @include('frontend.partials.spinner')
    @include('frontend.home.partials.hero')
    @include('frontend.home.partials.services')
    @include('frontend.home.partials.hotel-promotion')
    @include('frontend.home.partials.about')
    @include('frontend.home.partials.why-us')
    @include('frontend.home.partials.faqs')
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>
@endsection
