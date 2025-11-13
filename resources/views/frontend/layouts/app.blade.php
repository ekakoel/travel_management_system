<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{ config('app.name', 'Bali Kami Tour') }} | @yield('title')</title>
	<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/images/balikami/apple-touch-icon.png') }}">
	<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/images/balikami/favicon-32x32.png') }}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/images/balikami/favicon-16x16.png') }}">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('/frontend/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/frontend/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('/frontend/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- Template Stylesheet -->
    <link href="{{ asset('/frontend/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('/frontend/css/custom_style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    {{-- @include('layouts.home.navbar') --}}
    @include('frontend.layouts.navbar')
    <div class="main-container">
        <div id="app">
            @yield('content')
        </div>
    </div>
    <a href="#" id="backToTopBtn" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top" style="display: none; place-content: center;"><i class="bi bi-arrow-up"></i></a>
    {{-- @include('layouts.home.script') --}}
    @include('home.partials.footer')
    {{-- @include('frontend.layouts.footer') --}}

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('/frontend/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('/frontend/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('/frontend/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('/frontend/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('/frontend/lib/counterup/counterup.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('/frontend/js/main.js') }}"></script>
</body>
</html>
