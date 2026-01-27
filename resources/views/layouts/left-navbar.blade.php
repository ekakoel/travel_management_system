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
    // Services =======================================================================================================
    $services_menu = Services::where('status','Active')->orderBy('name', 'asc')->get();
    $services_admin = Services::orderBy('name', 'asc')->get();
    $now = Carbon::now();
    $left_orders_pending = Orders::where('status','Pending')->where('checkin','>=',$now)->get();
    $left_orders_wedding_pending = OrderWedding::where('status','Pending')->where('wedding_date','>=',$now)->get();
    $c_left_orders_pending = count($left_orders_pending);
    $c_left_orders_wedding_pending = count($left_orders_wedding_pending);
    $c_o_pending = $c_left_orders_pending+$c_left_orders_wedding_pending;
    $o_wedding_pending = $c_left_orders_wedding_pending;
    $o_tour_pending = $c_left_orders_pending;

    //USER
    $user = Auth::user();
    // PROMOTION
    $promotions = Promotion::where('periode_start','<', $now)
        ->where('periode_end','>',$now)
        ->where('status','Active')->get();
    $logoColor = config('app.logo_img_color');
    $logoWhite = config('app.logo_img_white');
    $logoBlack = config('app.logo_img_black');
?>
<div class="left-side-bar d-print-none">
    <div class="brand-logo">
        <a href="{{ route('dashboard.index') }}">
            <img src="{{ asset('storage/logo/'.$logoColor) }}" alt="Logo Bali Kami Tour" class="dark-logo">
            <img src="{{ asset('storage/logo/'.$logoWhite) }}" alt="Logo Bali Kami Tour" class="light-logo">
        </a>
        <div class="close-sidebar" data-toggle="left-sidebar-close">
            <i class="ion-close-round"></i>
        </div>
    </div>
    <div class="menu-block customscroll">
        <div class="sidebar-menu m-b-38">
            <div class="user-profile">
                <b><i class="icon-copy fa fa-user" aria-hidden="true"></i> {{ $user->name }}</b><br>
                <i><i class="icon-copy fi-key"></i> {{ $user->position }}</i>
            </div>
            @if (count($promotions) > 0)
                <div class="promotion-box">
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
                @if (auth()->user()->is_approved == 1)
                    <ul id="accordion-menu">
                        <li>
                            <a href="{{ route('home') }}" class="dropdown-toggle no-arrow">
                                <span class="micon dw dw-home" aria-hidden="true"></span>@lang('messages.Home')
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dashboard.index') }}" class="dropdown-toggle no-arrow {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                                <span class="micon ion-speedometer" aria-hidden="true"></span>@lang('messages.Dashboard')
                            </a>
                        </li>
                        <li>
                            <a href="javascript:;" class="dropdown-toggle">
                                <span class="micon dw dw-building1"> </span> @lang("messages.Accommodations")
                            </a>
                            <ul class="submenu">
                                <li>
                                    <a href="{{ route('view.hotels') }}" class="dropdown-toggle no-arrow {{ request()->routeIs('view.hotels') ? 'active' : '' }}">
                                        <i class="icon-copy dw dw-hotel"></i> @lang("messages.Hotels")
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('index.flyers') }}" class="dropdown-toggle no-arrow {{ request()->routeIs('index.flyers') ? 'active' : '' }}">
                                        <i class="icon-copy dw dw-hotel"></i> @lang('messages.Hotel Promotions')
                                    </a>
                                </li>
                                <!--<li>-->
                                <!--    <a href="{{ route('view.villas.index') }}" class="dropdown-toggle no-arrow {{ request()->routeIs('view.villas.index') ? 'active' : '' }}">-->
                                <!--        <i class="icon-copy dw dw-building-1"></i> @lang("messages.Private Villa")-->
                                <!--    </a>-->
                                <!--</li>-->
                            </ul>
                        </li>
                        @foreach ($services_menu as $menuitem)
                            @if ($menuitem->name !== "Hotels" && $menuitem->name !== "Villas")
                                <li>
                                    <a href="{{ route('view.'.$menuitem->nicname) }}" class="dropdown-toggle no-arrow {{ request()->routeIs('view.'.$menuitem->nicname) ? 'active' : '' }}">
                                        <span class="micon fa {!! $menuitem->icon !!}" aria-hidden="true"></span> @lang("messages.".$menuitem->name)
                                    </a>
                                </li>
                            @endif
                        @endforeach
                        

                        {{-- <li class="dropdown">
                            <a href="javascript:;" class="dropdown-toggle">
                                <span class="micon fi-wrench"></span><span class="mtext">@lang("messages.Tools")</span>
                            </a>
                            <ul class="submenu">
                                <li>
                                    <a href="trip-planner" class="dropdown-toggle no-arrow">
                                        <span class="icon-copy fi-map" aria-hidden="true"></span> @lang('messages.Trip Planner')
                                    </a>
                                </li>
                                <li>
                                    <a href="wedding-planner" class="dropdown-toggle no-arrow">
                                        <span class="icon-copy fi-clipboard-notes" aria-hidden="true"></span> @lang('messages.Wedding Planner')
                                    </a>
                                </li>
                            </ul>
                        </li> --}}
                        @canany(['posDev','posAuthor','posRsv','weddingRsv','weddingSls','weddingAuthor','weddingDvl'])
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
                                                <span class="icon-copy dw dw-money-2" aria-hidden="true"></span> @lang("messages.Currency")
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
                                                <span class="icon-copy dw dw-certificate" aria-hidden="true"></span> @lang("messages.Term And Condition")
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('attentions') }}" class="{{ request()->routeIs('attentions') ? 'active' : '' }}">
                                                <span class="icon-copy dw dw-warning" aria-hidden="true"></span> @lang("messages.Attentions")
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
                                <li class="dropdown">
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
                                </li>
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
                                            <div class="order-pending-text" data-toggle="tooltip" data-placement="top" title="Pending Orders" >
                                                <p>
                                                    <i class="icon-copy ti-alarm-clock"></i> <span>{{ $c_o_pending }}</span>
                                                </p>
                                            </div>
                                        </a>
                                    </li>
                                @endcan
                                @canany(['posAuthor','posRsv'])
                                    <li class="order-count">
                                        <a href="{{ route('orders-admin') }}" class="dropdown-toggle no-arrow {{ request()->routeIs('orders-admin') ? 'active' : '' }}">
                                            <i class="micon icon-copy dw dw-shopping-cart1" aria-hidden="true"></i> @lang("messages.Orders")
                                            <div class="order-pending-text" data-toggle="tooltip" data-placement="top" title="Pending Orders" >
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
                                            <div class="order-pending-text" data-toggle="tooltip" data-placement="top" title="Pending Orders" >
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
                                        @foreach ($services_admin as $menuadmin)
                                            @canany(['posDev','posAuthor','posRsv'])
                                                <li>
                                                    <a href="{{ url("$menuadmin->nicname"."-admin") }}" class="{{ request()->routeIs($menuadmin->nicname.'-admin.index') ? "active" : "" }}">
                                                        <span class="micon {!! $menuadmin->icon !!}"></span> {{ $menuadmin->name == "Villas"?__("messages.Private Villa"):__("messages.".$menuadmin->name); }}
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