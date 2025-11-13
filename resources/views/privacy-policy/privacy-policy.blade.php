<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Privacy Policy</title>
    <!-- Basic Page Info -->
	<meta charset="utf-8">
	<!-- Site favicon -->
	<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/balikami/apple-touch-icon.png') }}">
	<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/balikami/favicon-32x32.png') }}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/balikami/favicon-16x16.png') }}">
	<!-- Mobile Specific Metas -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">

    <link rel="stylesheet" href="vendors/term/fonts/icomoon/style.css">
    <link rel="stylesheet" href="vendors/term/css/owl.carousel.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="vendors/term/css/bootstrap.min.css">
    
    <!-- Style -->
    <link rel="stylesheet" href="vendors/term/css/style.css">

</head>
<body>
    <div class="site-mobile-menu site-navbar-target">
        <div class="site-mobile-menu-header">
            <div class="site-mobile-menu-close mt-3">
            <span class="icon-close2 js-menu-toggle"></span>
            </div>
        </div>
        <div class="site-mobile-menu-body"></div>
    </div>
    <header class="site-navbar site-navbar-target" role="banner">
        <div class="container">
            <div class="row align-items-center position-relative">
            <div class="col-3">
                <div class="site-logo">
                    <a href="{{ config('app.app_url') }}" class="font-weight-bold"><img src="{{ asset(config('app.logo_dark')) }}" alt="Logo Bali Kami Tour" class="dark-logo"></a>
                </div>
            </div>
            <div class="col-9  text-right">
                <span class="d-inline-block d-lg-none"><a href="#" class="text-primary site-menu-toggle js-menu-toggle py-5"><span class="icon-menu h3"></span></a></span>
                <nav class="site-navigation text-right ml-auto d-none d-lg-block" role="navigation">
                <ul class="site-menu main-menu js-clone-nav ml-auto ">
                    <li><a href="/" class="nav-link">@lang('messages.Home')</a></li>
                    <li><a href="{{ config('app.term') }}" class="nav-link">@lang('messages.Terms and Conditions')</a></li>
                    <li class="active"><a href="#" class="nav-link">@lang('messages.Privacy Policy')</a></li>
                    {{-- <li><a href="{{ config('app.user_manual') }}" class="nav-link">@lang('messages.User Manual')</a></li>
                    <li><a href="{{ config('app.help') }}" class="nav-link">@lang('messages.Help')</a></li>
                    <li><a href="{{ config('app.contact') }}" class="nav-link">@lang('messages.Contact')</a></li> --}}
                </ul>
                </nav>
            </div>
            </div>
        </div>
    </header>
    <div id="term" class="container-term">
        <div class="title">@lang('messages.Privacy Policy')</div>
        <div class="under-line"></div>
        <div class="content">
            <div class="opening">
                @lang('messages.Welcome to https://online.balikamitour.com, a platform dedicated to providing information on tourism services online. Bali Kami Tour & Travel understands the importance of your privacy and is committed to protecting your personal information. This Privacy Policy explains how we collect, use, and protect the information you provide to us, based on applicable legal provisions. By using our services, you can be assured that the privacy data you provide to us will not be used for any purposes that may harm any party. By registering, you agree to the terms of this Privacy Policy.')
            </div><br>
            @if (config('app.locale') == "en")
                @foreach ($system_term as $system_term)
                    <div class="subtitle">{{ $system_term->name_en }}</div>
                    <p>{!! $system_term->policy_en !!}</p>
                @endforeach
            @elseif(config('app.locale') == "zh")
                @foreach ($system_term as $system_term)
                    <div class="subtitle">{{ $system_term->name_zh }}</div>
                    <p>{!! $system_term->policy_zh !!}</p>
                @endforeach
            @else
                @foreach ($system_term as $system_term)
                    <div class="subtitle">{{ $system_term->name_id }}</div>
                    <p>{!! $system_term->policy_id !!}</p>
                @endforeach
            @endif
            <div class="date-term">
                <p>
                    Denpasar, 01 January 2023<br>
                    <b>{{ config('app.business') }}</b>
                </p>
            </div>
        </div>
    </div>
    <div class="footer">
        @php
            use Carbon\Carbon;
            $years = Carbon::now();
        @endphp
        <div class="text-center d-print-none p-b-18">
            <div class="inline-link">
                <a href="{{ config('app.term') }}" target="_blank">Terms and Conditions</a> - 
                <a href="{{ config('app.privacy') }}" target="_blank">Privacy Policy</a>
                {{-- <a href="{{ config('app.USER_MANUAL') }}" target="_blank">User Manual</a> - 
                <a href="{{ config('app.CONTACT') }}" target="_blank">Contact</a> --}}
            </div>
            <a href="{{ config('app.app_url') }}" target="_blank">{{ config('app.vendor') }}</a>
            <p>Copyright ©{{ date('Y',strtotime($years))." ".config('app.business') }} | {{ config('app.app_status') }} Version {{ config('app.app_version') }}</p>
        </div>
    </div>
    <script src="vendors/term/js/jquery-3.3.1.min.js"></script>
    <script src="vendors/term/js/popper.min.js"></script>
    <script src="vendors/term/js/bootstrap.min.js"></script>
    <script src="vendors/term/js/jquery.sticky.js"></script>
    <script src="vendors/term/js/main.js"></script>
</body>
</html>