
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
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Schema;
    // Services =======================================================================================================
    $services_menu = Schema::hasTable('services')
        ? Services::where('status','Active')->orderBy('name', 'asc')->get()
        : collect();
    $services_admin = Schema::hasTable('services')
        ? Services::orderBy('name', 'asc')->get()
        : collect();
    $serviceNavigation = [
        'hotels' => ['public' => 'view.hotels-service', 'admin' => 'admin.hotels.index', 'active' => 'admin.hotels.*'],
        'tours' => ['public' => 'view.tour-packages-service', 'admin' => 'admin.tour-packages.index', 'active' => ['admin.tour-packages.*', 'admin.tours.*']],
        'activities' => ['public' => 'view.activities-service', 'admin' => 'admin.activities.index', 'active' => 'admin.activities.*'],
        'transports' => ['public' => 'view.transports-service', 'admin' => 'admin.transports.index', 'active' => 'admin.transports.*'],
        'weddings' => ['public' => 'view.weddings', 'admin' => 'weddings-admin.index', 'active' => 'weddings-admin.*'],
    ];
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
                        @foreach ($services_menu as $femenu)
                            @php($serviceNav = $serviceNavigation[$femenu->nicname] ?? null)
                            @if ($serviceNav && Route::has($serviceNav['public']))
                                <li>
                                    <a href="{{ route($serviceNav['public']) }}">
                                        <i class="{!! $femenu->icon !!}"></i> {{ __("messages.".$femenu->name) }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                        @canany(['posDev','posAuthor','posRsv','weddingRsv','weddingSls','weddingAuthor','weddingDvl'])
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
                            @endif
                            <li class="dropdown">
                                <a href="javascript:;" class="dropdown-toggle">
                                    <i class="fas fa-user-shield"></i>@lang("messages.Admin")</span>
                                </a>
                                <ul class="submenu">
                                    @can('posDev')
                                        <li>
                                            <a href="{{ route('view.admin-panel-main') }}" class="{{ request()->routeIs('view.admin-panel-main') ? 'active' : '' }}">
                                                <i class="fas fa-th"></i> @lang("messages.Admin Panel")
                                            </a>
                                        </li>
                                    @endcan
                                    @canany(['posDev','posAuthor','posRsv','weddingRsv','weddingAuthor','weddingSls','weddingDvl'])
                                        <li>
                                            <a href="{{ route('currency') }}" class="{{ request()->routeIs('currency') ? 'active' : '' }}">
                                                <i class="fas fa-chart-line"></i> @lang("messages.Currency")
                                            </a>
                                        </li>
                                    @endcanany
                                    @can('posDev')
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
                                    @endcan
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
                                    {{-- @canany(['posDev','weddingDvl','weddingSls','weddingAuthor','weddingRsv'])
                                        <li>
                                            <a href="{{ route('vendors-admin.index') }}" {{ request()->routeIs('admin-panelvendors-admin.index') ? 'active' : '' }}>
                                                <i class="icon-copy fi-torso-business"></i> Wedding Vendors
                                            </a>
                                        </li>
                                    @endcanany --}}
                                    @canany(['posDev','posAuthor','posRsv'])
                                        <li>
                                            <a href="{{ route('admin.vendors.index') }}" {{ request()->routeIs('admin.vendors.index') ? 'active' : '' }}>
                                                <i class="fa fa-handshake-o" aria-hidden="true"></i> @lang("messages.Vendors")
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
                                    @endcanany
                                </ul>
                            </li>
                            {{-- SERVICES --------------------------------------- --}}
                            @canany(['posDev','posAuthor','posRsv'])
                            <li class="dropdown">
                                <a href="javascript:;" class="dropdown-toggle">
                                    <i class="fas fa-bars"></i>@lang("messages.Services")</span>
                                </a>
                                <ul class="submenu">
                                        @foreach ($services_menu as $menuadmin)
                                            @php($serviceNav = $serviceNavigation[$menuadmin->nicname] ?? null)
                                            @if ($serviceNav && Route::has($serviceNav['admin']))
                                                <li>
                                                    <a href="{{ route($serviceNav['admin']) }}" class="{{ request()->routeIs(...(array) $serviceNav['active']) ? 'active' : '' }}">
                                                        <i class="{!! $menuadmin->icon !!}"></i> {{ __("messages.".$menuadmin->name) }}
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </li>
                            @endcanany
                            {{-- REVIEWS --}}
                            @canany(['posDev','posRsv','weddingDvl','weddingRsv'])
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
                            @canany(['posDev','posRsv'])
                                {{-- SPK --}}
                                <li>
                                    <a href="{{ route('view.transport-management.index') }}" class="dropdown-toggle no-arrow {{ request()->routeIs('view.transport-management.index') ? 'active' : '' }}">
                                        <i class="fas fa-car"></i> @lang("messages.Transport Management")
                                    </a>
                                </li>
                                @can('posDev')
                                    <li class="order-count">
                                        <a href="{{ route('admin.order.index') }}" class="dropdown-toggle no-arrow {{ request()->routeIs('admin.order.index') ? 'active' : '' }}">
                                            <i class="fas fa-shopping-cart"></i> @lang("messages.Orders")
                                            <div class="order-pending-text backend-sidebar__badge" data-toggle="tooltip" data-placement="top" title="Pending Orders" >
                                                <i class="icon-copy ti-alarm-clock"></i> <span>{{ $c_o_pending }}</span>
                                            </div>
                                        </a>
                                    </li>
                                @endcan
                                @canany(['posAuthor','posRsv'])
                                    <li class="order-count">
                                        <a href="{{ route('admin.order.index') }}" class="dropdown-toggle no-arrow {{ request()->routeIs('admin.order.index') ? 'active' : '' }}">
                                            <i class="fas fa-shopping-cart"></i> @lang("messages.Orders")
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
                                @canany(['posDev','posRsv'])
                                    <li>
                                        <a href="{{ route('view.reservation') }}" class="dropdown-toggle no-arrow {{ request()->routeIs('view.reservation', 'view.reservation.detail', 'spks.show') ? 'active' : '' }}">
                                            <i class="fas fa-calendar-check"></i> @lang("messages.Reservations")
                                        </a>
                                    </li>
                                @endcan
                            @endcanany
                            

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
