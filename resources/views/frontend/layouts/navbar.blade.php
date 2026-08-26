@php
    $logoColor = config('app.logo_img_color');
    $currentUser = Auth::user();
    $currentPosition = $currentUser?->position;
    $currentUserAvatar = $currentUser && filled($currentUser->profileimg)
        ? asset('storage/user/profile/' . $currentUser->profileimg)
        : asset('storage/user/profile/default_user_img.png');
    $frontendOnlyPositions = ['agent'];
    $coreOpsPositions = ['developer', 'author', 'reservation'];
    $weddingOpsPositions = ['weddingRsv', 'weddingDvl', 'weddingAuthor', 'weddingSls'];
    $canAccessWorkspace = in_array($currentPosition, array_merge($coreOpsPositions, $weddingOpsPositions), true);
    $canAccessAdminDashboard = $currentUser && $currentUser->canAccessAdminDashboard();
    $canAccessReservations = in_array($currentPosition, ['developer', 'reservation', 'weddingRsv'], true);
@endphp

<style>
    .navbar-user-trigger {
        display: inline-flex !important;
        align-items: center;
        gap: 0.7rem;
    }

    .navbar-user-trigger__avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.7);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.14);
        background: #ffffff;
    }

    .navbar-user-trigger__name {
        display: inline-block;
        max-width: 168px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        vertical-align: middle;
    }

    .navbar-user-summary {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .navbar-user-summary__avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        object-fit: cover;
        flex: 0 0 auto;
    }

    @media (max-width: 991.98px) {
        #mainNavbar .dropdown-menu {
            position: static !important;
            inset: auto !important;
            min-width: 0;
            margin: 0.15rem 0 0.35rem;
            padding: 0;
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none !important;
            transform: none !important;
            max-height: min(50vh, 320px);
            overflow-y: auto;
            overscroll-behavior: contain;
        }

        #mainNavbar .dropdown-item,
        #mainNavbar .dropdown-header {
            padding: 0.55rem 0 0.55rem 1.35rem;
            background: transparent;
            border: 0;
            white-space: normal;
        }

        #mainNavbar .dropdown-item {
            color: var(--bs-dark, #1f2937);
            font-weight: 500;
        }

        #mainNavbar .dropdown-item i {
            width: 1.25rem;
            text-align: center;
            color: #585858;
        }
        #mainNavbar .dropdown-item i:hover {
            color: #585858;
        }

        #mainNavbar .dropdown-header {
            color: #6b7280;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        #mainNavbar .dropdown-divider {
            margin: 0.2rem 0 0.35rem 1.35rem;
            opacity: 0.12;
        }

        #mainNavbar .nav-item.dropdown .nav-link.dropdown-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-user-trigger__name {
            max-width: none;
        }
    }
</style>

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
                <div class="nav-link dropdown-toggle {{ request()->is('services*') || request()->is('activity-services') ? 'active' : '' }}" data-bs-toggle="dropdown">
                    @lang('messages.Services')
                </div>
                <div class="dropdown-menu shadow-sm m-0">
                    @foreach ($globalServices as $item)
                        @if ($item['public_route'])
                            <a class="dropdown-item" href="{{ route($item['public_route']) }}"><i class="{{ $item['icon'] }}"></i> {{ $item['label'] }}</a>
                        @endif
                    @endforeach
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
                    <a class="dropdown-item" href="{{ language_switch_url('en') }}"><i class="fa fa-language" aria-hidden="true"></i>English</a>
                    <a class="dropdown-item" href="{{ language_switch_url('zh') }}"><i class="fa fa-language" aria-hidden="true"></i> 繁體中文</a>
                    <a class="dropdown-item" href="{{ language_switch_url('zh-CN') }}"><i class="fa fa-language" aria-hidden="true"></i> 简体中文</a>
                </div>
            </div>
            @if (Auth::check())
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle navbar-user-trigger" href="#" data-bs-toggle="dropdown">
                        <img src="{{ $currentUserAvatar }}" alt="{{ $currentUser->name }}" class="navbar-user-trigger__avatar">
                        <span class="navbar-user-trigger__name">{{ Auth::user()->name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow-sm m-0">
                        <div class="px-3 py-2 border-bottom">
                            <div class="navbar-user-summary">
                                <img src="{{ $currentUserAvatar }}" alt="{{ $currentUser->name }}" class="navbar-user-summary__avatar">
                                <div>
                                    <div class="fw-semibold text-dark">{{ $currentUser->name }}</div>
                                    <div class="small text-muted">{{ ucfirst((string)__('messages.'.$currentPosition)) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="dropdown-header">@lang('messages.Account')</div>
                        <a class="dropdown-item" href="{{ route('profile') }}"><i class="fa fa-id-card me-2" aria-hidden="true"></i>@lang('messages.Profile')</a>
                        <a class="dropdown-item" href="{{ route('view.orders') }}"><i class="icon-copy fa fa-tags me-2" aria-hidden="true"></i>@lang('messages.Order')</a>
                        <a class="dropdown-item" href="{{ route('orders.history') }}"><i class="fa fa-history me-2" aria-hidden="true"></i>@lang('messages.Order History')</a>
                        <a class="dropdown-item" href="{{ url('/manual-book') }}"><i class="icon-copy fa fa-book me-2" aria-hidden="true"></i>@lang('messages.Manual Book')</a>

                        @if ($canAccessAdminDashboard)
                            <hr class="dropdown-divider">
                            <div class="dropdown-header">@lang('messages.Dashboard')</div>
                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>@lang('messages.Dashboard')</a>
                            @can('posDev')
                                <a class="dropdown-item" href="{{ route('admin.panel-main.view') }}"><i class="fas fa-briefcase me-2"></i>@lang('messages.Admin Panel')</a>
                            @endcan
                            @if ($canAccessReservations)
                                <a class="dropdown-item" href="{{ url('/reservation') }}"><i class="fa fa-calendar-check me-2" aria-hidden="true"></i>@lang('messages.Reservations')</a>
                            @endif
                        @endif

                        <hr class="dropdown-divider">
                        <div class="dropdown-header">@lang('messages.General Terms')</div>
                        <a class="dropdown-item" href="{{ url('/terms-and-conditions') }}"><i class="fa fa-info-circle me-2" aria-hidden="true"></i>@lang('messages.Terms and Conditions')</a>
                        <a class="dropdown-item" href="{{ url('/privacy-policy') }}"><i class="fa fa-shield-alt me-2" aria-hidden="true"></i>@lang('messages.Privacy Policy')</a>

                        <hr class="dropdown-divider">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>@lang('messages.Logout')</button>
                        </form>
                    </div>
                </div>
            @else
                <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> @lang('messages.Login')</a>
            @endif
        </div>
    </div>
</nav>
<!-- Navbar End -->
