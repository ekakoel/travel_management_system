<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<title>{{ config('app.name', 'Bali Kami Tour') }} | @yield('title')</title>
	<meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="apple-touch-icon" sizes="180x180"
		href="{{ asset('images/balikami/apple-touch-icon.png') }}">
	<link rel="icon" type="image/png" sizes="32x32"
		href="{{ asset('images/balikami/favicon-32x32.png') }}">
	<link rel="icon" type="image/png" sizes="16x16"
		href="{{ asset('images/balikami/favicon-16x16.png') }}">

	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
		rel="stylesheet">

	{{-- Icon libraries --}}
	<link rel="stylesheet" href="{{ asset('panel/styles/icon-font.min.css') }}">
	<link rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css">
	<link rel="stylesheet"
		href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">

	{{-- Core --}}
	<link rel="stylesheet" href="{{ asset('panel/styles/core.css') }}">

	{{-- Plugins --}}
	<link rel="stylesheet" href="{{ asset('panel/datatables/css/dataTables.bootstrap4.min.css') }}">
	<link rel="stylesheet" href="{{ asset('panel/datatables/css/responsive.bootstrap4.min.css') }}">
	<link rel="stylesheet" href="{{ asset('panel/fullcalendar/fullcalendar.css') }}">
	<link rel="stylesheet" href="{{ asset('panel/slick/slick.css') }}">
	<link rel="stylesheet" href="{{ asset('panel/bootstrap-touchspin/jquery.bootstrap-touchspin.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/owlcarousel/owl.carousel.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/owlcarousel/owl.theme.default.min.css') }}">
	<link rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
	<link rel="stylesheet"
		href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
	<link rel="stylesheet"
		href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.css">

	{{-- Theme and application --}}
	<link rel="stylesheet" href="{{ asset('panel/styles/style.css') }}">
	<link rel="stylesheet" href="{{ mix('build/backend/css/app.css') }}">
	<link rel="stylesheet" href="{{ asset('css/style.css') }}">
	<link rel="stylesheet"
		href="{{ asset('css/daterangepicker.css') }}?v={{ filemtime(public_path('css/daterangepicker.css')) }}">

	@stack('styles')
	
	<script>  
		if(performance.navigation.type === 2){
		   location.reload(true);
		}
		document.addEventListener('contextmenu', function(e) {
			e.preventDefault();
		});
	</script>
	@livewireStyles
    <link href="{{ asset('multiform.css') }}" rel="stylesheet" id="bootstrap">
	@stack('styles')
	</head>
	<body class="sidebar-light anim-feed-up"
		data-backend-money-hint="{{ __('messages.Backend monetary input hint') }}"
		data-backend-money-label="{{ __('messages.Monetary input unit') }}">
		<div id="page-loader">
			<div class="loader-wrapper">
				<div class="spinner"></div>
			</div>
		</div>
		@include('component.menu')
		@include('backend.partials.left-navbar')
		@yield('content')
		@include('layouts.footjs')
		@stack('scripts')
	</body>
	@livewireScripts
</html>
