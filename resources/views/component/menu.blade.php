@php
    $currentUser = $backendNavigation['user'];
    $canAccessAdminDashboard = $backendNavigation['canAccessAdminDashboard'];
    $orderCounts = $backendNavigation['orderCounts'];
    $weddingOrderCounts = $backendNavigation['weddingOrderCounts'];
    $pendingCounts = $backendNavigation['pendingCounts'];
@endphp
<div class="d-print-none header">
    <div class="header-left">
        <div class="menu-icon dw dw-menu"></div>
    </div>
    <div class="header-right">
        @if (!request()->isMethod('post'))
            <a class="dropdown-togle" href="#" role="button" data-toggle="dropdown">
                <div class="lang-dropdown m-r-18">
                    <div class="dropdown">
                        <div class="lang-icon">
                            @if (app()->getLocale() == 'en')
                                English
                            @elseif (app()->getLocale() == 'zh')
                                繁體中文
                            @else
                                简体中文
                            @endif
                        </div>
                        
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                            <a class="dropdown-item" href="{{ language_switch_url('en') }}"><i class="fa fa-language" aria-hidden="true"></i>English</a>
                            <a class="dropdown-item" href="{{ language_switch_url('zh') }}"><i class="fa fa-language" aria-hidden="true"></i> 繁體中文</a>
                            <a class="dropdown-item" href="{{ language_switch_url('zh-CN') }}"><i class="fa fa-language" aria-hidden="true"></i> 简体中文</a>
                        </div>
                    </div>
                </div>
            </a>
        @endif
        @can('isUser')
            @if ($weddingOrderCounts['Draft'] > 0 || $orderCounts['Draft'] > 0 || $orderCounts['Active'] > 0 || $orderCounts['Invalid'] > 0 || $orderCounts['Rejected'] > 0 || $orderCounts['Confirmed'] > 0 || $orderCounts['Approved'] > 0)
                <div class="user-notification m-r-18">
                    <div class="dropdown">
                        <a class="dropdown-toggle no-arrow" href="#" role="button" data-toggle="dropdown">
                            <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                            @if ($orderCounts['Draft'] > 0 || $weddingOrderCounts['Draft'] > 0 || $orderCounts['Invalid'] > 0 || $orderCounts['Rejected'] > 0)
                                <span class="badge notification-active">{{ $orderCounts['Draft'] + $orderCounts['Invalid'] + $orderCounts['Rejected'] + $weddingOrderCounts['Draft'] }}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="notification-list mx-h-350 customscroll">
                                <ul>
                                    @if ($orderCounts['Draft'] > 0 || $weddingOrderCounts['Draft'] > 0)
                                        <li>
                                            <a href="/orders">
                                                <span>{{ $orderCounts['Draft'] + $weddingOrderCounts['Draft'] }}</span>
                                                <i class="draft icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Draft Order')</p>
                                                <p class="description-notif">@lang('messages.You have') {{ $orderCounts['Draft'] + $weddingOrderCounts['Draft'] }} @lang('messages.unsubmitted orders')</p>
                                            </a>
                                        </li> 
                                    </form>
                                    @endif
                                    @if ($orderCounts['Approved'] > 0)
                                        <li>
                                            <a href="/orders">
                                                <i class="approved icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Approved Order')</p>
                                                <p class="description-notif">@lang('messages.You have') {{ $orderCounts['Approved'] }} @lang('messages.approved orders')</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($orderCounts['Confirmed'] > 0)
                                        <li>
                                            <a href="/orders">
                                                <i class="confirmed icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Confirmed Order')</p>
                                                <p class="description-notif">@lang('messages.You have') {{ $orderCounts['Confirmed'] }} @lang('messages.confirmed orders')</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($orderCounts['Active'] > 0)
                                        <li>
                                            <a href="/orders">
                                                <i class="active icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Active Order')</p>
                                                <p class="description-notif">@lang('messages.You have') {{ $orderCounts['Active'] }} @lang('messages.active orders')</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($orderCounts['Rejected'] > 0)
                                        <li>
                                            <a href="/orders">
                                                <span>{{ $orderCounts['Rejected'] }}</span>
                                                <i class="rejected icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Rejected Order')</p>
                                                <p class="description-notif">@lang('messages.You have') {{ $orderCounts['Rejected'] }} @lang('messages.rejected orders')</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($orderCounts['Invalid'] > 0)
                                        <li>
                                            <a href="/orders">
                                                <span>{{ $orderCounts['Invalid'] }}</span>
                                                <i class="invalid icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Invalid Order')</p>
                                                <p class="description-notif">@lang('messages.You have') {{ $orderCounts['Invalid'] }} @lang('messages.invalid orders')</p>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endcan
        @can('posDev')
            @if ($pendingCounts['all'] > 0)
                <a href="/orders-admin#pending-orders">
                    <div class="notif-order blink_me m-r-18">
                        {{ $pendingCounts['all'] }}
                    </div>
                </a>
            @endif
        @endcan
        @canany(['posAuthor','posRsv'])
            @if ($pendingCounts['tour'] > 0)
                <a href="/orders-admin#pending-orders">
                    <div class="notif-order blink_me">
                        {{ $pendingCounts['tour'] }}
                    </div>
                </a>
            @endif
        @endcanany
        @canany(['weddingDvl','weddingAuthor','weddingRsv','weddingSls'])
            @if ($pendingCounts['wedding'] > 0)
                <a href="/orders-admin#pending-orders">
                    <div class="notif-order blink_me">
                        {{ $pendingCounts['wedding'] }}
                    </div>
                </a>
            @endif
        @endcanany
        @can('isAdmin')
            @if ($orderCounts['Draft'] > 0 || $orderCounts['Active'] > 0 || $orderCounts['Invalid'] > 0 || $orderCounts['Rejected'] > 0 || $weddingOrderCounts['Draft'] > 0)
                <div class="user-notification">
                    <div class="dropdown">
                        <a class="dropdown-toggle no-arrow" href="#" role="button" data-toggle="dropdown">
                            <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                            @if ($orderCounts['Draft'] > 0 || $weddingOrderCounts['Draft'] > 0 || $orderCounts['Invalid'] > 0 || $orderCounts['Rejected'] > 0)
                                <span class="badge notification-active">{{ $orderCounts['Draft'] + $orderCounts['Invalid'] + $orderCounts['Rejected'] + $weddingOrderCounts['Draft'] }}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="notification-list mx-h-350 customscroll">
                                <ul>
                                    @if ($orderCounts['Draft'] > 0 || $weddingOrderCounts['Draft'] > 0)
                                        <li>
                                            <a href="/orders">
                                                <span>{{ $orderCounts['Draft'] + $weddingOrderCounts['Draft'] }}</span>
                                                <i class="draft icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Draft Order')</p>
                                                <p class="description-notif">@lang('messages.You have') {{ $orderCounts['Draft'] + $weddingOrderCounts['Draft'] }} @lang('messages.unsubmitted order')</p>
                                            </a>
                                        </li> 
                                    @endif
                                    @if ($orderCounts['Active'] > 0)
                                        <li>
                                            <a href="/orders">
                                                <i class="active icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Active Order')</p>
                                                <p class="description-notif">@lang('messages.You have') {{ $orderCounts['Active'] }} @lang('messages.active orders')</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($orderCounts['Rejected'] > 0)
                                        <li>
                                            <a href="/orders">
                                                <span>{{ $orderCounts['Rejected'] }}</span>
                                                <i class="rejected icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Rejected Order') </p>
                                                <p class="description-notif">@lang('messages.You have') {{ $orderCounts['Rejected'] }} @lang('messages.rejected orders')</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if ($orderCounts['Invalid'] > 0)
                                        <li>
                                            <a href="/orders">
                                                <span>{{ $orderCounts['Invalid'] }}</span>
                                                <i class="invalid icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Invalid Order') </p>
                                                <p class="description-notif">@lang('messages.You have') {{ $orderCounts['Invalid'] }} @lang('messages.Invalid orders')</p>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endcan
        <section class="user-info-dropdown">
            <div id="backEndNavbar" class="dropdown">
                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                    <div class="user-icon">
                        @if (Auth::user()->profileimg == '')
                            <img src="{{ asset('storage/user/profile/default_user_img.png') }}" alt="" class="avatar-photo">
                        @else
                            <img src="{{ asset('storage/user/profile') .'/'. Auth::user()->profileimg }}" alt="{{ Auth::user()->name }}" >
                        @endif
                    </div>
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('profile') }}"><i class="dw dw-user1"></i>{{ Auth::user()->name }}</a>
                    @if ($canAccessAdminDashboard)
                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="ion-speedometer"></i> @lang('messages.Dashboard')</a>
                    @endif
                    <a class="dropdown-item" href="{{ route('view.orders') }}"><i class="icon-copy fa fa-tags" aria-hidden="true"></i> @lang('messages.Order')</a>
                    <a class="dropdown-item" href="{{ route('view.manual-book') }}"><i class="icon-copy fa fa-book" aria-hidden="true"></i> @lang('messages.Manual Book')</a>
                    <a class="dropdown-item" href="{{ route('terms-and-conditions') }}"><i class="fa fa-info-circle" aria-hidden="true"></i> @lang('messages.Term And Condition')</a>
                    <a class="dropdown-item" href="{{ route('privacy-policy') }}"><i class="fa fa-info-circle" aria-hidden="true"></i> @lang('messages.Privacy Policy')</a>
                    <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="dw dw-logout"></i> @lang('messages.Log Out')</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>
