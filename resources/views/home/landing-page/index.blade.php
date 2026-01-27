@extends('layouts.app')
@section('title', 'Home')
@section('content')
    @include('home.partials.spinner')
    @include('home.partials.home.hero')
    @include('home.partials.home.services')
    @include('home.partials.home.hotel-promotion')
    @include('home.partials.home.about')
    @include('home.partials.home.why-us')
    @include('home.partials.home.faqs')
    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>
@endsection
