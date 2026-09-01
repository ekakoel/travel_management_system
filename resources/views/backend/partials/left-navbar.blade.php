
@php
    $user = $backendNavigation['user'];
    $servicesMenu = $backendNavigation['services'];
    $promotions = $backendNavigation['promotions'];
    $isApprovedUser = $backendNavigation['isApprovedUser'];
    $operationsPendingCount = $backendNavigation['pendingCounts']['operations'];
    $ordersNavigationActive = $backendNavigation['active']['orders'];
    $reservationsNavigationActive = $backendNavigation['active']['reservations'];
    $invoicesNavigationActive = $backendNavigation['active']['invoices'];
    $operationsNavigationActive = $backendNavigation['active']['operations'];
    $logoColor = $backendNavigation['logos']['color'];
    $logoWhite = $backendNavigation['logos']['white'];
@endphp
<div class="left-side-bar backend-sidebar d-print-none">
    <div class="brand-logo backend-sidebar__brand">
        <a href="{{ $user->canAccessAdminDashboard() ? route('admin.dashboard') : route('home') }}">
            <img src="{{ asset('storage/logo/'.$logoColor) }}" alt="Logo Bali Kami Tour" class="dark-logo">
            <img src="{{ asset('storage/logo/'.$logoWhite) }}" alt="Logo Bali Kami Tour" class="light-logo">
        </a>
        <div class="close-sidebar" data-toggle="left-sidebar-close">
            <i class="ion-close-round"></i>
        </div>
    </div>
    <div class="menu-block customscroll">
        <div class="sidebar-menu m-b-38">
            <div class="user-profile backend-sidebar__profile">
                <div class="backend-sidebar__avatar" aria-hidden="true">
                    {{ Str::of($user->name)->substr(0, 1)->upper() }}
                </div>
                <div class="backend-sidebar__profile-copy">
                    <b>{{ $user->name }}</b>
                    <span><i class="icon-copy fi-key"></i> {{ $user->position }}</span>
                </div>
                <span class="backend-sidebar__status">{{ $user->status }}</span>
            </div>
            @if (count($promotions) > 0)
                <div class="promotion-box backend-sidebar__promo">
                    <p>@lang('messages.Active Promotion')</p>
                    @foreach ($promotions as $promotion)
                        <div class="promotion-item">
                            <div class="promotion-description" data-toggle="tooltip" data-placement="top" title="@lang('messages.Ongoing promotion'){{" ". $promotion->name." "}}@lang('messages.and get discounts'){{ " $".$promotion->discounts." " }}@lang('messages.until'){{ " ". date('d M y',strtotime($promotion->periode_end)) }}" >
                                <b>{{ $promotion->name }}</b>
                                <p>{{ date('d M y',strtotime($promotion->periode_start))." - ".date('d M y',strtotime($promotion->periode_end)) }}</p>
                            </div>
                            <div class="promotion-discounts">
                                {{ "$".$promotion->discounts }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            @if (Auth::user()->status == "Active")
                @if ($isApprovedUser)
                    <ul id="accordion-menu" class="backend-sidebar__nav">
                        <li>
                            <a href="{{ route('home') }}" class="nav-toggle no-arrow">
                                <i class="fas fa-home"></i>@lang('messages.Home')
                            </a>
                        </li>
                        @foreach ($servicesMenu as $serviceItem)
                            @if ($serviceItem['public_route'])
                                <li>
                                    <a href="{{ route($serviceItem['public_route']) }}">
                                        <i class="{{ $serviceItem['icon'] }}"></i> {{ $serviceItem['label'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                        <li class="backend-sidebar__section-item">
                            <div class="backend-sidebar__section-label">@lang('messages.Backend')</div>
                        </li>
                        @if ($user->canAccessAdminDashboard())
                            <ul id="accordion-dashboard-menu" class="backend-sidebar__nav">
                                <li>
                                    <a href="{{ route('admin.dashboard') }}" class="nav-toggle no-arrow {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                        <i class="fas fa-tachometer-alt"></i>@lang('messages.Dashboard')
                                    </a>
                                </li>
                            </ul>
                            @canany(['posDev','posAdm','posAuthor','posRsv'])
                                <li class="dropdown">
                                    <a href="javascript:;" class="dropdown-toggle">
                                        <i class="fas fa-user-shield"></i>@lang("messages.Admin")</span>
                                    </a>
                                    <ul class="submenu">
                                        @can('posDev')
                                            <li>
                                                <a href="{{ route('admin.panel-main.view') }}" class="{{ request()->routeIs('admin.panel-main.view') ? 'active' : '' }}">
                                                    <i class="fas fa-th"></i> @lang("messages.Admin Panel")
                                                </a>
                                            </li>
                                        @endcan
                                        <li>
                                            <a href="{{ route('currency') }}" class="{{ request()->routeIs('currency') ? 'active' : '' }}">
                                                <i class="fas fa-chart-line"></i> @lang("messages.Currency")
                                            </a>
                                        </li>
                                        @canany(['posDev','posAdm'])
                                            <li>
                                                <a href="{{ route('user-manager') }}" class="{{ request()->routeIs('user-manager') ? 'active' : '' }}">
                                                    <i class="fas fa-users"></i> @lang("messages.User Manager")
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('view.term-and-condition') }}" class="{{ request()->routeIs('view.term-and-condition') ? 'active' : '' }}">
                                                    <i class="fas fa-shield-alt"></i> @lang("messages.Term And Condition")
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.company-profile.edit') }}" class="{{ request()->routeIs('admin.company-profile.*') ? 'active' : '' }}">
                                                    <i class="fas fa-cog"></i> @lang("messages.Company Profile")
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.footer-manager.index') }}" class="{{ request()->routeIs('admin.footer-manager.*') ? 'active' : '' }}">
                                                    <span class="icon-copy dw dw-browser2" aria-hidden="true"></span> @lang("messages.Footer Manager")
                                                </a>
                                            </li>
                                        @endcanany
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a href="javascript:;" class="dropdown-toggle">
                                        <i class="fas fa-handshake"></i><span class="mtext">@lang("messages.Provider")</span>
                                    </a>
                                    <ul class="submenu">
                                        {{-- <li>
                                            <a href="/partners">
                                                <i class="fa fa-handshake-o" aria-hidden="true"></i> @lang("messages.Partners")
                                            </a>
                                        </li> --}}
                                        {{-- @canany(['posDev'])
                                            <li>
                                                <a href="{{ route('partners-admin.index') }}" {{ request()->routeIs('admin-panelpartners-admin.index') ? 'active' : '' }}>
                                                    <i class="icon-copy fi-torso-business"></i> Wedding Partners
                                                </a>
                                            </li>
                                        @endcanany --}}
                                        <li>
                                            <a href="{{ route('admin.partners.index') }}" {{ request()->routeIs('admin.partners.index') ? 'active' : '' }}>
                                                <i class="fa fa-handshake-o" aria-hidden="true"></i> @lang("messages.Partners")
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.guides.index') }}" {{ request()->routeIs('admin.guides.index') ? 'active' : '' }}>
                                                <i class="fas fa-user-check"></i> @lang("messages.Guide")
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.drivers.index') }}" {{ request()->routeIs('admin.drivers.index') ? 'active' : '' }}>
                                                <i class="fas fa-user-tie"></i> @lang("messages.Driver")
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                {{-- OPERATIONS ------------------------------------- --}}
                                <li class="dropdown {{ $operationsNavigationActive ? 'show' : '' }}">
                                    <a href="javascript:;" class="dropdown-toggle">
                                        <i class="fas fa-briefcase"></i><span class="mtext">@lang('messages.Operations')</span>
                                    </a>
                                    <ul id="operations-submenu" class="submenu">
                                        @canany(['posDev', 'posRsv','posAdm'])
                                            <li class="order-count">
                                                <a href="{{ route('admin.order.index') }}" class="{{ $ordersNavigationActive ? 'active' : '' }}">
                                                    <i class="fas fa-shopping-cart"></i> @lang('messages.Orders')
                                                    @if ($operationsPendingCount > 0)
                                                        <span class="order-pending-text backend-sidebar__badge" data-toggle="tooltip" data-placement="top" title="{{ __('messages.Pending Orders') }}">
                                                            <i class="icon-copy ti-alarm-clock"></i><span>{{ $operationsPendingCount }}</span>
                                                        </span>
                                                    @endif
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('view.reservation') }}" class="{{ $reservationsNavigationActive ? 'active' : '' }}">
                                                    <i class="fas fa-calendar-check"></i> @lang('messages.Reservations')
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.invoices.index') }}" class="{{ $invoicesNavigationActive ? 'active' : '' }}">
                                                    <i class="fas fa-file-invoice-dollar"></i> @lang('messages.Invoices')
                                                </a>
                                            </li>
                                        @endcan
                                        @canany(['posDev', 'posAuthor', 'posAdm'])
                                            <li>
                                                <a href="{{ route('admin.transport-types.index') }}" class="{{ request()->routeIs('admin.transport-types.*') ? 'active' : '' }}">
                                                    <i class="fas fa-list"></i> Transport Types
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.transport-brands.index') }}" class="{{ request()->routeIs('admin.transport-brands.*') ? 'active' : '' }}">
                                                    <i class="fas fa-tags"></i> Transport Brands
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.activity-types.index') }}" class="{{ request()->routeIs('admin.activity-types.*') ? 'active' : '' }}">
                                                    <i class="fas fa-list"></i> Activity Type
                                                </a>
                                            </li>
                                        @endcanany
                                    </ul>
                                </li>
                                {{-- SERVICES --------------------------------------- --}}
                                <li class="dropdown">
                                    <a href="javascript:;" class="dropdown-toggle">
                                        <i class="fas fa-bars"></i>@lang("messages.Services")</span>
                                    </a>
                                    <ul class="submenu">
                                        @foreach ($servicesMenu as $serviceItem)
                                            @if ($serviceItem['admin_route'])
                                                <li>
                                                    <a href="{{ route($serviceItem['admin_route']) }}" class="{{ request()->routeIs(...$serviceItem['admin_active']) ? 'active' : '' }}">
                                                        <i class="{{ $serviceItem['icon'] }}"></i> {{ $serviceItem['label'] }}
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </li>
                                {{-- REVIEWS --}}
                                @canany(['posDev','posAdm','posRsv'])
                                    <li class="dropdown">
                                        <a href="javascript:;" class="dropdown-toggle">
                                            <i class="fas fa-star"></i><span class="mtext">@lang("messages.Reviews")</span>
                                        </a>
                                        <ul class="submenu">
                                            <li>
                                                <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.index') ? 'active' : '' }}">
                                                    <i class="fas fa-star"></i> @lang('messages.Tour Reviews')
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('view.generate-review-link') }}" class="{{ request()->routeIs('view.generate-review-link') ? 'active' : '' }}">
                                                    <i class="fas fa-star"></i> @lang('messages.Tour Review Links')
                                                </a>
                                            </li>
                                            {{-- <li>
                                                <a href="{{ route('admin.wedding-reviews.index') }}" class="{{ request()->routeIs('admin.wedding-reviews.index') ? 'active' : '' }}">
                                                    <i class="fas fa-star"></i> @lang('messages.Wedding Reviews')
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('view.generate-wedding-review-link') }}" class="{{ request()->routeIs('view.generate-wedding-review-link') ? 'active' : '' }}">
                                                    <i class="fas fa-star"></i> @lang('messages.Wedding Review Links')
                                                </a>
                                            </li> --}}
                                        </ul>
                                    </li>
                                @endcan
                                
                                {{-- <li class="dropdown">
                                    <a href="javascript:;" class="dropdown-toggle">
                                        <span class="micon icon-copy fa fa-percent"></span><span class="mtext">@lang("messages.Promo")</span>
                                    </a>
                                    <ul class="submenu">
                                        <li>
                                            <a href="{{ route('promotion') }}" class="{{ request()->routeIs('promotion') ? 'active' : '' }}">
                                                <i class="fa fa-bullhorn" aria-hidden="true"></i> @lang("messages.Promotion")
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('booking-code') }}" class="{{ request()->routeIs('booking-code') ? 'active' : '' }}">
                                                <i class="fa fa-calendar-check-o" aria-hidden="true"></i> @lang("messages.Booking Code")
                                            </a>
                                        </li>
                                    </ul>
                                </li> --}}
                                @canany(['posDev', 'posRsv' ,'posAdm'])
                                    <li>
                                        <a href="{{ route('admin.transport-management.index') }}" class="dropdown-toggle no-arrow {{ request()->routeIs('admin.transport-management.index') ? 'active' : '' }}">
                                            <i class="fas fa-car"></i> @lang("messages.Transport Management")
                                        </a>
                                    </li>
                                @endcan
                                
                            @endcanany
                        @endif
                    </ul>
                @else
                    <div class="notifikasi-menu">
                        @lang('messages.Your account is in the approval process, please wait for 2 x 24 hours for approval! Thank you.')
                    </div>
                @endif
            @else
                <div class="notifikasi-menu">
                    @lang('messages.Your account has been disabled because it does not comply with the established terms.')
                </div>
            @endif
        </div>
    </div>
</div>
