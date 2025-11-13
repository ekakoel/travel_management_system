<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<title>{{ config('app.name', 'Bali Kami Tour') }} | @yield('title')</title>
	<meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/balikami/apple-touch-icon.png') }}">
	<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/balikami/favicon-32x32.png') }}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/balikami/favicon-16x16.png') }}">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="/panel/styles/core.css">
	<link rel="stylesheet" type="text/css" href="/panel/styles/icon-font.min.css">
	<link rel="stylesheet" type="text/css" href="/panel/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="/panel/css/responsive.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="{{ asset("/panel/styles/style.css") }}">
	<link rel="stylesheet" type="text/css" href="{{ asset("/css/style.css") }}">
	<link rel="stylesheet" type="text/css" href="/panel/fullcalendar/fullcalendar.css">
	{{-- <link rel="stylesheet" type="text/css" href="/panel/dropzone/dropzone.css"> --}}
	<link rel="stylesheet" type="text/css" href="/panel/slick/slick.css">
	<link rel="stylesheet" type="text/css" href="/panel/datatables/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="/panel/datatables/css/responsive.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="/panel/bootstrap-touchspin/jquery.bootstrap-touchspin.css">
	<link rel="stylesheet" href="/assets/owlcarousel/owl.carousel.min.css">
	<link rel="stylesheet" href="/assets/owlcarousel/owl.theme.default.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
	<!-- Bootstrap WYSIHTML5 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-wysihtml5-bower/0.3.3/bootstrap3-wysihtml5.min.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js "></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<script src="https://sandbox.doku.com/jokul-checkout-js/v1/jokul-checkout-1.0.0.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<!--<script src="/vendors/scripts/datatable-setting.js"></script>-->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" rel="stylesheet" />
	
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
	</head>
	<body class="sidebar-light anim-feed-up">
		@include('component.menu')
		@include('layouts.left-navbar')
		@yield('content')
		@include('layouts.footjs')
	</body>
	@livewireScripts
</html>