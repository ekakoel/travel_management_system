<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>@yield('title')</title>
	<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/images/balikami/apple-touch-icon.png') }}">
	<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/images/balikami/favicon-32x32.png') }}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('/images/balikami/favicon-16x16.png') }}">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Libraries Stylesheet -->
    <link href="{{ asset('/landing-page/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/landing-page/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('/landing-page/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('/landing-page/css/style.css') }}" rel="stylesheet">
    {{-- <link rel="stylesheet" type="text/css" href="/panel/styles/core.css"> --}}
    <link rel="stylesheet" type="text/css" href="{{ asset('/landing-page/css/custom_style.css') }}">
    <link rel="stylesheet" type="text/css" href="/panel/styles/icon-font.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    @include('frontend.layouts.navbar')

    <div class="main-container">
        @yield('content')
    </div>

    @include('frontend.layouts.footer')
    <a href="#" id="backToTopBtn" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top" style="display: none; place-content: center;"><i class="bi bi-arrow-up"></i></a>
    <!-- JavaScript Libraries -->

    {{-- jQuery harus dimuat sebelum bootstrap bundle --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('/landing-page/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('/landing-page/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('/landing-page/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('/landing-page/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('/landing-page/lib/counterup/counterup.min.js') }}"></script>
    {{-- DATEPICKER --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <!-- Template Javascript -->
    <script src="{{ asset('/landing-page/js/main.js') }}"></script>
    <script src="{{ asset('/landing-page/js/custom.js') }}"></script>
    @stack('scripts')
</body>
</html>
