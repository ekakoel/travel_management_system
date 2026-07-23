<?php
    use App\Models\Services;
    use App\Models\Promotion;
    use App\Models\Orders;
    use App\Models\OrderWedding;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Input;
    use App\Http\Requests\StoremenuRequest;
    use App\Http\Requests\UpdatemenuRequest;
    use Illuminate\Support\Facades\Schema;
    // Services =======================================================================================================
    $services_menu = Schema::hasTable('services')
        ? Services::where('status','Active')->orderBy('name', 'asc')->get()
        : collect();
    $services_admin = Schema::hasTable('services')
        ? Services::orderBy('name', 'asc')->get()
        : collect();
    $now = Carbon::now();
    $left_orders_pending = Schema::hasTable('orders')
        ? Orders::where('status','Pending')->where('checkin','>=',$now)->get()
        : collect();
    $left_orders_wedding_pending = Schema::hasTable('order_weddings')
        ? OrderWedding::where('status','Pending')->where('wedding_date','>=',$now)->get()
        : collect();
    $c_left_orders_pending = count($left_orders_pending);
    $c_left_orders_wedding_pending = count($left_orders_wedding_pending);
    $c_o_pending = $c_left_orders_pending+$c_left_orders_wedding_pending;
    $o_wedding_pending = $c_left_orders_wedding_pending;
    $o_tour_pending = $c_left_orders_pending;

    //USER
    $user = Auth::user();
    // PROMOTION
    $promotions = Schema::hasTable('promotions')
        ? Promotion::where('periode_start','<', $now)
            ->where('periode_end','>',$now)
            ->where('status','Active')->get()
        : collect();
    $isApprovedUser = ! Schema::hasColumn('users', 'is_approved') || (bool) $user->is_approved;
    $logoColor = config('app.logo_img_color');
    $logoWhite = config('app.logo_img_white');
    $logoBlack = config('app.logo_img_black');
?>
<div class="left-side-bar backend-sidebar d-print-none">
    <div class="brand-logo backend-sidebar__brand">
        <a href="{{ $user->canAccessAdminDashboard() ? route('admin.dashboard') : route('dashboard.index') }}">
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
                                <span class="micon dw dw-home" aria-hidden="true"></span>@lang('messages.Home')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('view.accommodation-service') }}" class="nav-toggle no-arrow {{ request()->routeIs('view.accommodation-service') || request()->routeIs('view.accommodation-detail') || request()->routeIs('view.hotel-detail') || request()->routeIs('view.hotel-detail-flyer') || request()->routeIs('view.accommodation-check-price') || request()->routeIs('view.hotel-check-price') ? 'active' : '' }}">
                                <span class="micon dw dw-building1" aria-hidden="true"></span> @lang("messages.Accommodations")
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('view.tour-package-services') }}" class="nav-toggle no-arrow {{ request()->routeIs('view.tour-package-services') || request()->routeIs('view.tour-detail') ? 'active' : '' }}">
                                <span class="micon dw dw-map-6" aria-hidden="true"></span> @lang("messages.Tours")
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('view.activity-services') }}" class="nav-toggle no-arrow {{ request()->routeIs('view.activity-services') || request()->routeIs('view.activity-public-detail') ? 'active' : '' }}">
                                <span class="micon dw dw-pin-1" aria-hidden="true"></span> @lang("messages.Activities")
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('view.transport-service') }}" class="nav-toggle no-arrow {{ request()->routeIs('view.transport-service') ? 'active' : '' }}">
                                <span class="micon dw dw-bus" aria-hidden="true"></span> @lang("messages.Transports")
                            </a>
                        </li>
                        @canany(['posDev','posAuthor','posRsv','weddingRsv','weddingSls','weddingAuthor','weddingDvl'])
                            <li class="backend-sidebar__section-item">
                                <div class="backend-sidebar__section-label">@lang('messages.Backend')</div>
                            </li>
                            @if ($user->canAccessAdminDashboard())
                                <ul id="accordion-dashboard-menu" class="backend-sidebar__nav">
                                    <li>
                                        <a href="{{ route('admin.dashboard') }}" class="nav-toggle no-arrow {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                            <span class="micon ion-speedometer" aria-hidden="true"></span>@lang('messages.Dashboard')
                                        </a>
                                    </li>
                                </ul>
                            @endif
                            <li class="dropdown">
                                <a href="javascript:;" class="dropdown-toggle">
                                    <span class="micon dw dw-panel"></span><span class="mtext">@lang("messages.Admin")</span>
                                </a>
                                <ul class="submenu">
                                    @can('posDev')
                                        <li>
                                            <a href="{{ route('view.admin-panel-main') }}" class="{{ request()->routeIs('view.admin-panel-main') ? 'active' : '' }}">
                                                <span class="micon dw dw-analytics-4" aria-hidden="true"></span> @lang("messages.Admin Panel")
                                            </a>
                                        </li>
                                    @endcan
                                    @canany(['posDev','posAuthor','posRsv','weddingRsv','weddingAuthor','weddingSls','weddingDvl'])
                                        <li>
                                            <a href="{{ route('currency') }}" class="{{ request()->routeIs('currency') ? 'active' : '' }}">
                                                <span class="icon-copy dw dw-money-1" aria-hidden="true"></span> @lang("messages.Currency")
                                            </a>
                                        </li>
                                    @endcanany
                                    @can('posDev')
                                        <li>
                                            <a href="{{ route('user-manager') }}" class="{{ request()->routeIs('user-manager') ? 'active' : '' }}">
                                                <span class="icon-copy dw dw-group" aria-hidden="true"></span> @lang("messages.User Manager")
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('view.term-and-condition') }}" class="{{ request()->routeIs('view.term-and-condition') ? 'active' : '' }}">
                                                <span class="icon-copy dw dw-file-125" aria-hidden="true"></span> @lang("messages.Term And Condition")
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.company-profile.edit') }}" class="{{ request()->routeIs('admin.company-profile.*') ? 'active' : '' }}">
                                                <span class="icon-copy dw dw-building1" aria-hidden="true"></span> @lang("messages.Company Profile")
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.footer-manager.index') }}" class="{{ request()->routeIs('admin.footer-manager.*') ? 'active' : '' }}">
                                                <span class="icon-copy dw dw-browser2" aria-hidden="true"></span> @lang("messages.Footer Manager")
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                            {{-- REVIEWS --}}
                            @canany(['posDev','posRsv','weddingDvl','weddingRsv'])
                                <li class="dropdown">
                                    <a href="javascript:;" class="dropdown-toggle">
                                        <span class="micon icon-copy dw dw-star" aria-hidden="true"></span><span class="mtext">@lang("messages.Reviews")</span>
                                    </a>
                                    <ul class="submenu">
                                        <li>
                                            <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.index') ? 'active' : '' }}">
                                                <i class="dw dw-star" aria-hidden="true"></i> @lang('messages.Tour Reviews')
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('view.generate-review-link') }}" class="{{ request()->routeIs('view.generate-review-link') ? 'active' : '' }}">
                                                <i class="dw dw-star" aria-hidden="true"></i> @lang('messages.Tour Review Links')
                                            </a>
                                        </li>
                                        {{-- <li>
                                            <a href="{{ route('admin.wedding-reviews.index') }}" class="{{ request()->routeIs('admin.wedding-reviews.index') ? 'active' : '' }}">
                                                <i class="dw dw-star" aria-hidden="true"></i> @lang('messages.Wedding Reviews')
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('view.generate-wedding-review-link') }}" class="{{ request()->routeIs('view.generate-wedding-review-link') ? 'active' : '' }}">
                                                <i class="dw dw-star" aria-hidden="true"></i> @lang('messages.Wedding Review Links')
                                            </a>
                                        </li> --}}
                                    </ul>
                                </li>
                            @endcan
                            @canany(['posDev','posAuthor','posRsv','weddingDvl','weddingSls','weddingAuthor','weddingRsv'])
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
                            @endcanany
                            @canany(['posDev','posRsv','weddingDvl','weddingSls','weddingAuthor','weddingRsv'])
                                {{-- SPK --}}
                                <li>
                                    <a href="{{ route('view.transport-management.index') }}" class="dropdown-toggle no-arrow {{ request()->routeIs('view.transport-management.index') ? 'active' : '' }}">
                                        <span class="micon dw dw-map-5"></span> @lang("messages.Transport Management")
                                    </a>
                                </li>
                                {{-- <li>
                                    <a href="{{ route('view.reservation') }}" class="dropdown-toggle no-arrow {{ request()->routeIs('view.reservation') || request()->routeIs('reservations.show') || request()->routeIs('spks.show') ? 'active' : '' }}">
                                        <span class="micon dw dw-list"></span> @lang("messages.Reservations")
                                    </a>
                                </li> --}}
                                @can('posDev')
                                    <li class="order-count">
                                        <a href="{{ route('orders-admin') }}" class="dropdown-toggle no-arrow {{ request()->routeIs('orders-admin') ? 'active' : '' }}">
                                            <i class="micon icon-copy dw dw-shopping-cart1" aria-hidden="true"></i> @lang("messages.Orders")
                                            <div class="order-pending-text backend-sidebar__badge" data-toggle="tooltip" data-placement="top" title="Pending Orders" >
                                                <i class="icon-copy ti-alarm-clock"></i> <span>{{ $c_o_pending }}</span>
                                            </div>
                                        </a>
                                    </li>
                                @endcan
                                @canany(['posAuthor','posRsv'])
                                    <li class="order-count">
                                        <a href="{{ route('orders-admin') }}" class="dropdown-toggle no-arrow {{ request()->routeIs('orders-admin') ? 'active' : '' }}">
                                            <i class="micon icon-copy dw dw-shopping-cart1" aria-hidden="true"></i> @lang("messages.Orders")
                                            <div class="order-pending-text backend-sidebar__badge" data-toggle="tooltip" data-placement="top" title="Pending Orders" >
                                                @if ($o_tour_pending > 0)
                                                    <p>
                                                        <i class="icon-copy ti-alarm-clock"></i> <span>{{ $o_tour_pending }}</span>
                                                    </p>
                                                @endif
                                            </div>
                                        </a>
                                    </li>
                                @endcanany
                                @canany(['weddingDvl','weddingSls','weddingAuthor','weddingRsv'])
                                    <li class="order-count">
                                        <a href="{{ route('orders-admin') }}" class="dropdown-toggle no-arrow">
                                            <i class="micon icon-copy dw dw-shopping-cart1" aria-hidden="true"></i> @lang("messages.Orders")
                                            <div class="order-pending-text backend-sidebar__badge" data-toggle="tooltip" data-placement="top" title="Pending Orders" >
                                                @if ($o_wedding_pending > 0)
                                                    <p>
                                                        <i class="icon-copy ti-alarm-clock"></i> <span>{{ $o_wedding_pending }}</span>
                                                    </p>
                                                @endif
                                            </div>
                                        </a>
                                    </li>
                                @endcanany
                            @endcanany
                            <li class="dropdown">
                                <a href="javascript:;" class="dropdown-toggle">
                                    <span class="micon icon-copy dw dw-deal"></span><span class="mtext">@lang("messages.Provider")</span>
                                </a>
                                <ul class="submenu">
                                    {{-- <li>
                                        <a href="/partners">
                                            <i class="fa fa-handshake-o" aria-hidden="true"></i> @lang("messages.Partners")
                                        </a>
                                    </li> --}}
                                    {{-- @canany(['posDev','weddingDvl','weddingSls','weddingAuthor','weddingRsv'])
                                        <li>
                                            <a href="{{ route('vendors-admin.index') }}" {{ request()->routeIs('admin-panelvendors-admin.index') ? 'active' : '' }}>
                                                <i class="icon-copy fi-torso-business"></i> Wedding Vendors
                                            </a>
                                        </li>
                                    @endcanany --}}
                                    @canany(['posDev','posAuthor','posRsv'])
                                        <li>
                                            <a href="{{ route('guides-admin.index') }}" {{ request()->routeIs('guides-admin.index') ? 'active' : '' }}>
                                                <i class="icon-copy fa fa-user" aria-hidden="true"></i> @lang("messages.Guide")
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('drivers-admin.index') }}" {{ request()->routeIs('drivers-admin.index') ? 'active' : '' }}>
                                                <i class="icon-copy fa fa-user-circle-o" aria-hidden="true"></i> @lang("messages.Driver")
                                            </a>
                                        </li>
                                    @endcanany
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="javascript:;" class="dropdown-toggle">
                                    <span class="micon dw dw-list3"></span><span class="mtext">@lang("messages.Services")</span>
                                </a>
                                <ul class="submenu">
                                    @canany(['posDev','posAuthor','posRsv','weddingDvl','weddingSls','weddingAuthor','weddingRsv'])
                                        @foreach ($services_menu as $menuadmin)
                                            @canany(['posDev','posAuthor','posRsv'])
                                                <li>
                                                    <a href="{{ url("$menuadmin->nicname"."-admin") }}" class="{{ request()->routeIs($menuadmin->nicname.'-admin.index') ? "active" : "" }}">
                                                        <span class="micon {!! $menuadmin->icon !!}"></span> {{ __("messages.".$menuadmin->name) }}
                                                    </a>
                                                </li>
                                            @endcanany
                                        @endforeach
                                    @endcanany
                                </ul>
                            </li>
                        @endcanany
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
