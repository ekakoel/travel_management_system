@php
    $logoColor = config('app.logo_img_color');
@endphp

<nav id="mainNavbar" class="navbar navbar-expand-lg p-0 px-4 px-lg-5">
    <a class="navbar-brand fw-bold" href="{{ url('/') }}">
        <img src="{{ asset('storage/logo/'.$logoColor) }}" alt="Logo Bali Kami Tour" class="logo">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <i class="fa fa-bars"></i>
    </button>

    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto py-4 py-lg-0">
            <a href="{{ url('/') }}" class="nav-item nav-link {{ request()->is('/') ? 'active' : '' }}">
                @lang('messages.Home')
            </a>

            <div class="nav-item dropdown">
                <div class="nav-link dropdown-toggle {{ request()->is('services*') ? 'active' : '' }}" data-bs-toggle="dropdown">
                    @lang('messages.Services')
                </div>
                <div class="dropdown-menu shadow-sm m-0">
                    <a class="dropdown-item" href="{{ route('view.accommodation-service') }}"><i class="fas fa-hotel"></i> @lang('messages.Accommodations')</a>
                    <a class="dropdown-item" href="{{ route('view.transport-service') }}"><i class="fas fa-car"></i> @lang('messages.Transports')</a>
                    <a class="dropdown-item" href="{{ route('tour-package-service') }}"><i class="fas fa-suitcase-rolling"></i> @lang('messages.Tour Packages')</a>
                </div>
            </div>

            <a href="{{ url('about-us') }}" class="nav-item nav-link {{ request()->is('about-us') ? 'active' : '' }}">
                @lang('messages.About Us')
            </a>

            <a href="{{ url('contact-us') }}" class="nav-item nav-link {{ request()->is('contact-us') ? 'active' : '' }}">
                @lang('messages.Contact Us')
            </a>

            
        </div>
        <div class="navbar-nav ms-auto py-4 py-lg-0">
            <div class="nav-item dropdown">
                <div class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-language"></i>
                    @if (app()->getLocale() == 'en')
                        English
                    @elseif (app()->getLocale() == 'zh')
                        繁體中文
                    @else
                        简体中文
                    @endif
                </div>
                <div class="dropdown-menu shadow-sm m-0">
                    <a class="dropdown-item" href="{{ url('lang/en?redirect=' . urlencode(request()->fullUrl())) }}"><i class="fa fa-language" aria-hidden="true"></i>English</a>
                    <a class="dropdown-item" href="{{ url('lang/zh?redirect=' . urlencode(request()->fullUrl())) }}"><i class="fa fa-language" aria-hidden="true"></i> 繁體中文</a>
                    <a class="dropdown-item" href="{{ url('lang/zh-CN?redirect=' . urlencode(request()->fullUrl())) }}"><i class="fa fa-language" aria-hidden="true"></i> 简体中文</a>
                </div>
            </div>
            @if (Auth::check())
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="fa fa-user-circle"></i> {{ Auth::user()->name }}
                    </a>
                    <div class="dropdown-menu shadow-sm m-0">
                        @canany(['posDev','posAuthor','posRsv','weddingRsv','weddingSls','weddingAuthor','weddingDvl'])
                            <a class="dropdown-item" href="{{ route('view.admin-panel-main') }}"><i class="fas fa-user-tie"></i>@lang('messages.Admin Panel')</a>
                        @endcanany
                        <a class="dropdown-item" href="{{ route('dashboard.index') }}"><i class="fas fa-tachometer-alt"></i>@lang('messages.Dashboard')</a>
                        <hr class="dropdown-divider">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i>@lang('messages.Logout')</button>
                        </form>
                    </div>
                </div>
            @else
                <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> @lang('messages.Login')</a>
            @endif
            {{-- <a class="nav-item nav-link btn btn-square rounded-circle bg-light text-primary me-2" href="https://www.facebook.com/BALIKAMITOUR/"><i class="fab fa-facebook-f"></i></a>
            <a class="nav-item nav-link btn btn-square rounded-circle bg-light text-primary me-2" href="https://www.instagram.com/balikamitour"><i class="fab fa-instagram"></i></a>
            <a class="nav-item nav-link btn btn-square rounded-circle bg-light text-primary me-2" href="https://www.youtube.com/@balikamichannel"><i class="fab fa-youtube"></i></a>
            <a class="nav-item nav-link btn btn-square rounded-circle bg-light text-primary me-2" href="https://id.linkedin.com/company/bali-kami-group"><i class="fab fa-linkedin-in"></i></a> --}}
        </div>
    </div>
</nav>
<!-- Navbar End -->
