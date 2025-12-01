@php
    $logoColor = config('app.logo_img_color');
@endphp
<nav id="mainNavbar" class="navbar navbar-expand-lg p-0 px-4 px-lg-5">
    <a class="navbar-brand fw-bold" href="#">
        <img src="{{ asset('storage/logo/'.$logoColor) }}" alt="Logo Bali Kami Tour" class="logo">
    </a>
    <button id="navToggler" type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <i id="navIcon" class="icon-copy dw dw-menu-1"></i>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto py-4 py-lg-0">
            <a href="{{ request()->is('/') ? '#' : url('/') }}" class="nav-item nav-link {{ request()->is('/') ? 'active' : '' }}"><i class="icon-copy fa fa-home" aria-hidden="true"></i> @lang('messages.Home')</a>
            <a href="{{ request()->routeIs('dashboard.index') ? '#' : route('dashboard.index') }}" 
                class="nav-item nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                <i class="icon-copy fa fa-dashboard" aria-hidden="true"></i> 
                @lang('messages.Dashboard')
            </a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">@lang('messages.Accommodation')</a>
                <div class="dropdown-menu shadow-sm m-0">
                    <a href="{{ route('view.hotels') }}" class="nav-item nav-link {{ request()->routeIs('view.hotels') ? 'active' : '' }}"><i class="icon-copy dw dw-hotel"></i> @lang('messages.Hotels')</a>
                    <a href="{{ route('view.hotel-promotions') }}" class="nav-item nav-link {{ request()->routeIs('view.hotel-promotions') ? 'active' : '' }}"><i class="fa fa-percent" aria-hidden="true"></i> @lang('messages.Hotel Promotions')</a>
                    <a href="{{ route('view.villas.index') }}" class="nav-item nav-link {{ request()->routeIs('view.villas.index') ? 'active' : '' }}"><i class="icon-copy dw dw-building-1"></i> @lang('messages.Private Villa')</a>
                </div>
            </div>
            <a href="{{ route('view.transports') }}" class="nav-item nav-link {{ request()->routeIs('view.transports') ? 'active' : '' }}"><i class="icon-copy dw dw-bus"></i> @lang('messages.Transportation')</a>
            <a href="{{ route('view.tours') }}" class="nav-item nav-link {{ request()->routeIs('view.tours') ? 'active' : '' }}"><i class="icon-copy dw dw-map-2"></i> @lang('messages.Tour Package')</a>
        </div>
        <div class="navbar-nav ms-auto  py-4 py-lg-0">
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    @if (app()->getLocale() == 'en')
                        <i class="fa fa-language" aria-hidden="true"></i> English
                    @elseif (app()->getLocale() == 'zh')
                        <i class="fa fa-language" aria-hidden="true"></i> 繁體中文
                    @else
                        <i class="fa fa-language" aria-hidden="true"></i> 简体中文
                    @endif
                </a>
                <div class="dropdown-menu shadow-sm m-0">
                    <a class="dropdown-item" href="{{ url('lang/en?redirect=' . urlencode(request()->fullUrl())) }}"><i class="fa fa-language" aria-hidden="true"></i>English</a>
                    <a class="dropdown-item" href="{{ url('lang/zh?redirect=' . urlencode(request()->fullUrl())) }}"><i class="fa fa-language" aria-hidden="true"></i> 繁體中文</a>
                    <a class="dropdown-item" href="{{ url('lang/zh-CN?redirect=' . urlencode(request()->fullUrl())) }}"><i class="fa fa-language" aria-hidden="true"></i> 简体中文</a>
                </div>
            </div>
            @if (Auth::check())
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="/profile" data-bs-toggle="dropdown">
                        <div class="user-icon">
                            @if (Auth::user()->profileimg == '')
                                <img src="{{ asset('storage/user/profile/default_user_img.png') }}" alt=""
                                    class="avatar-photo">
                            @else
                                <img src="{{ asset('storage/user/profile') .'/'. Auth::user()->profileimg }}" alt="{{ Auth::user()->name }}" >
                            @endif
                        </div>
                    </a>
                    <div class="dropdown-menu shadow-sm m-0">
                        <a class="dropdown-item" href="/profile"><i class="dw dw-user1"></i>@lang('messages.Profile')</a>
                        <a class="dropdown-item" href="{{ route('view.orders') }}"><i class="icon-copy dw dw-shopping-cart1"></i>@lang('messages.Orders')</a>
                        @canany(['posDev','posAuthor','posRsv','weddingRsv','weddingSls','weddingAuthor','weddingDvl'])
                            <a class="dropdown-item" href="{{ route('view.admin-panel-main') }}"><i class="icon-copy dw dw-server"></i>@lang('messages.Admin Panel')</a>
                        @endcanany
                        <a class="dropdown-item" href="/manual-book"><i class="icon-copy fa fa-book" aria-hidden="true"></i>@lang('messages.Manual Book')</a>
                        <a class="dropdown-item" href="/terms-and-conditions"><i class="fa fa-info-circle" aria-hidden="true"></i>@lang('messages.Terms And Conditions')</a>
                        <a class="dropdown-item" href="/privacy-policy"><i class="fa fa-info-circle" aria-hidden="true"></i>@lang('messages.Privacy Policy')</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i>@lang('messages.Logout')</button>
                        </form>
                    </div>
                </div>
            @else
                <a class="nav-link" href="{{ route('login') }}">@lang('messages.Login')</a>
            @endif
        </div>
    </div>
</nav>