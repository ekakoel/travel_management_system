<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Basic Page Info -->
        <meta charset="utf-8">
        <title>{{ config('app.name', 'Bali Kami Tour') }}</title>
        <!-- Site favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/balikami/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/balikami/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/balikami/favicon-16x16.png') }}">
        <!-- Mobile Specific Metas -->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <!-- Google Font -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <!-- CSS -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <link rel="stylesheet" type="text/css" href="/panel/styles/core.css">
        <link rel="stylesheet" type="text/css" href="/panel/styles/icon-font.min.css">
        <link rel="stylesheet" type="text/css" href="/panel/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" type="text/css" href="/panel/css/responsive.bootstrap4.min.css">
        <link rel="stylesheet" type="text/css" href="/panel/styles/style.css">
        <link rel="stylesheet" type="text/css" href="/panel/fullcalendar/fullcalendar.css">
        <link rel="stylesheet" type="text/css" href="/css/style.css">
        <link rel="stylesheet" type="text/css" href="/panel/dropzone/dropzone.css">
        <link rel="stylesheet" type="text/css" href="/panel/slick/slick.css">
        <link rel="stylesheet" type="text/css" href="panel/datatables/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" type="text/css" href="panel/datatables/css/responsive.bootstrap4.min.css">
        <link rel="stylesheet" type="text/css" href="/panel/bootstrap-touchspin/jquery.bootstrap-touchspin.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js "></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <link rel="stylesheet" href="/assets/owlcarousel/owl.carousel.min.css">
        <link rel="stylesheet" href="/assets/owlcarousel/owl.theme.default.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
        <script type="text/javascript">  
            if(performance.navigation.type == 2){
            location.reload(true);
            }
        </script>
    </head>
    <body>
        <div class="position-ref full-height bg-01">
            <div class="overlay"></div>
            <div class="welcome-view shadow-grey" style="z-index: 99;">
                <div class="cover-title">
                    @lang('messages.Welcome to')
                </div>
                <div class="logo-cover">
                    <img src="{{ asset('/storage/logo/logo-color-bali-kami.png') }}" alt="logo" width="300">
                </div>
                @if (Route::has('login'))
                    @auth
                        @if (Auth::user()->status == "Block")
                            <div class="notif-cover m-b-30">
                                <div style="color: brown">@lang('messages.Your account has been disabled because it does not comply with the established terms.')</div>
                            </div>
                        @else
                            <div class="notif-cover m-b-30">
                                @lang('messages.Welcome back'), {{ Auth::user()->name }}
                            </div>
                        @endif
                    @else
                        <div class="notif-cover">
                            @lang('messages.Please login to be able to use this service')
                        </div>
                    @endauth
                @endif
                <div class="welcome">
                    @if (Route::has('login'))
                        <div class="links">
                            @auth
                                <div class="btn-main">
                                    <a href="{{ url('/profile') }}">
                                        <button type="button" class="btn btn-primary"><i class="icon-copy fi-torso-business"></i> &nbsp; @lang('messages.Profile')</button>
                                    </a>
                                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <button type="button" class="btn btn-danger"><i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;{{ __('Logout') }}</button>
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div><br> <br>
                                <div class="inline-link">
                                    <a href="{{ config('app.term') }}" target="_blank">Terms and Conditions</a> - 
                                    <a href="{{ config('app.privacy') }}" target="_blank">Privacy Policy</a>
                                </div>
                            @else
                                <a href="{{ route('login') }}">
                                    <button type="button" class="btn btn-primary">@lang('messages.Login')</button>
                                </a>
                                @uiEnabled('btn-register')
                                    <span>@lang('messages.or')</span>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}">
                                            <button type="button" class="btn btn-secondary">@lang('messages.Register')</button>
                                        </a>
                                    @endif
                                @endUiEnabled
                                <br> <br>
                                <div class="inline-link" style="font-size: 0.8rem;">
                                    <a href="{{ config('app.term') }}" target="_blank">Terms and Conditions</a> - 
                                    <a href="{{ config('app.privacy') }}" target="_blank">Privacy Policy</a>
                                </div>
                                {{-- <p>To register as a new user, please contact our administrator to initiate the registration process. The administrator will provide guidance and grant access to your account.</p> --}}
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </body>
</html>