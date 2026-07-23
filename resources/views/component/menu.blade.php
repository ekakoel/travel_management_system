<?php
    use App\Models\Orders;
    use App\Models\OrderWedding;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Schema;
    use App\Http\Requests\StoremenuRequest;
    use App\Http\Requests\UpdatemenuRequest;
    // USER =======================================================================================================
    $now = Carbon::now();
    $currentUser = Auth::user();
    $canAccessAdminDashboard = $currentUser && $currentUser->canAccessAdminDashboard();
    $hasOrdersTable = Schema::hasTable('orders');
    $hasWeddingOrdersTable = Schema::hasTable('order_weddings');
    $ordersForUser = function (?string $status = null) use ($hasOrdersTable, $currentUser, $now) {
        if (! $hasOrdersTable || ! $currentUser) {
            return collect();
        }

        $query = Orders::where('user_id', $currentUser->id);

        if ($status) {
            $query->where('status', $status)->where('checkin', '>=', $now);
        }

        return $query->get();
    };
    $weddingOrdersForUser = function (string $status) use ($hasWeddingOrdersTable, $currentUser, $now) {
        if (! $hasWeddingOrdersTable || ! $currentUser) {
            return collect();
        }

        return OrderWedding::where('agent_id', $currentUser->id)
            ->where('status', $status)
            ->where('checkin', '>=', $now)
            ->get();
    };
    $menu_order = $ordersForUser();
    $order_active = $ordersForUser('Active');
    $order_rejected = $ordersForUser('Rejected');
    $order_invalid = $ordersForUser('Invalid');
    $order_waiting = $ordersForUser('Waiting');
    $order_draft = $ordersForUser('Draft');
    $order_confirmed = $ordersForUser('Confirmed');
    $order_approved = $ordersForUser('Approved');
    $order_wedding_draft = $weddingOrdersForUser('Draft');
    $order_wedding_pending = $weddingOrdersForUser('Pending');
    $order_wedding_approved = $weddingOrdersForUser('Approved');
    // Admin =======================================================================================================
    $adm_menu_order = $ordersForUser();
    $adm_order_active = $ordersForUser('Active');
    $adm_order_rejected = $ordersForUser('Rejected');
    $adm_order_invalid = $ordersForUser('Invalid');
    $adm_order_waiting = $ordersForUser('Waiting');
    $adm_order_draft = $ordersForUser('Draft');
    $adm_order_confirmed = $ordersForUser('Confirmed');
    $adm_order_approved = $ordersForUser('Approved');
    $adm_order_wedding_draft = $weddingOrdersForUser('Draft');
    $adm_order_wedding_pending = $weddingOrdersForUser('Pending');
    $adm_order_wedding_approved = $weddingOrdersForUser('Approved');
    $ord_pend = $hasOrdersTable
        ? Orders::where('status','Pending')->where('checkin','>',$now)->get()
        : collect();
    $ord_wedding_pend = $hasWeddingOrdersTable
        ? OrderWedding::where('status','Pending')->where('checkin','>=',$now)->get()
        : collect();
    $cord_pend = count($ord_pend)+count($ord_wedding_pend);
    $cord_tour_pend = count($ord_pend);
    $cord_wedding_pend = count($ord_wedding_pend);
?>
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
            @if (count($order_wedding_draft) > 0 or count($order_draft) > 0 or count($order_active) > 0 or count($order_invalid) > 0 or count($order_rejected) > 0 or count($order_confirmed)>0or count($order_approved)>0)
                <div class="user-notification m-r-18">
                    <div class="dropdown">
                        <a class="dropdown-toggle no-arrow" href="#" role="button" data-toggle="dropdown">
                            <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                            @if (count($order_draft) > 0 || count($order_wedding_draft)>0 || count($order_invalid)>0 || count($order_rejected)>0)
                                <span class="badge notification-active">{{ count($order_draft) + count($order_invalid) + count($order_rejected) + count($order_wedding_draft)}}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="notification-list mx-h-350 customscroll">
                                <ul>
                                    @if (count($order_draft)>0)
                                        <li>
                                            <a href="/orders">
                                                <span>{{ count($order_draft) + count($order_wedding_draft) }}</span>
                                                <i class="draft icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Draft Order')</p>
                                                <p class="description-notif">@lang('messages.You have') {{ count($order_draft) + count($order_wedding_draft) }} @lang('messages.unsubmitted orders')</p>
                                            </a>
                                        </li> 
                                    </form>
                                    @endif
                                    @if (count($order_approved)>0)
                                        <li>
                                            <a href="/orders">
                                                <i class="approved icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Approved Order')</p>
                                                <p class="description-notif">@lang('messages.You have') {{ count($order_approved) }} @lang('messages.approved orders')</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if (count($order_confirmed)>0)
                                        <li>
                                            <a href="/orders">
                                                <i class="confirmed icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Confirmed Order')</p>
                                                <p class="description-notif">@lang('messages.You have') {{ count($order_confirmed) }} @lang('messages.confirmed orders')</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if (count($order_active)>0)
                                        <li>
                                            <a href="/orders">
                                                <i class="active icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Active Order')</p>
                                                <p class="description-notif">@lang('messages.You have') {{ count($order_active) }} @lang('messages.active orders')</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if (count($order_rejected)>0)
                                        <li>
                                            <a href="/orders">
                                                <span>{{ count($order_rejected) }}</span>
                                                <i class="rejected icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Rejected Order')</p>
                                                <p class="description-notif">@lang('messages.You have') {{ count($order_rejected) }} @lang('messages.rejected orders')</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if (count($order_invalid)>0)
                                        <li>
                                            <a href="/orders">
                                                <span>{{ count($order_invalid) }}</span>
                                                <i class="invalid icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Invalid Order')</p>
                                                <p class="description-notif">@lang('messages.You have') {{ count($order_invalid) }} @lang('messages.invalid orders')</p>
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
            @if ($cord_pend > 0)
                <a href="/orders-admin#pending-orders">
                    <div class="notif-order blink_me m-r-18">
                        {{ $cord_pend }}
                    </div>
                </a>
            @endif
        @endcan
        @canany(['posAuthor','posRsv'])
            @if ($cord_tour_pend > 0)
                <a href="/orders-admin#pending-orders">
                    <div class="notif-order blink_me">
                        {{ $cord_tour_pend }}
                    </div>
                </a>
            @endif
        @endcanany
        @canany(['weddingDvl','weddingAuthor','weddingRsv','weddingSls'])
            @if ($cord_wedding_pend > 0)
                <a href="/orders-admin#pending-orders">
                    <div class="notif-order blink_me">
                        {{ $cord_wedding_pend }}
                    </div>
                </a>
            @endif
        @endcanany
        @can('isAdmin')
            @if (count($adm_order_draft) > 0 || count($adm_order_active) > 0 || count($adm_order_invalid) > 0 || count($adm_order_rejected) > 0 || count($adm_order_wedding_draft))
                <div class="user-notification">
                    <div class="dropdown">
                        <a class="dropdown-toggle no-arrow" href="#" role="button" data-toggle="dropdown">
                            <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                            @if (count($adm_order_draft) > 0 || count($adm_order_wedding_draft)>0 || count($adm_order_invalid)>0 || count($adm_order_rejected)>0)
                                <span class="badge notification-active">{{ count($adm_order_draft) + count($adm_order_invalid) + count($adm_order_rejected) + count($adm_order_wedding_draft)}}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="notification-list mx-h-350 customscroll">
                                <ul>
                                    @if (count($adm_order_draft)>0 or count($adm_order_wedding_draft)>0)
                                        <li>
                                            <a href="/orders">
                                                <span>{{ count($adm_order_draft) + count($adm_order_wedding_draft) }}</span>
                                                <i class="draft icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Draft Order')</p>
                                                <p class="description-notif">@lang('messages.You have') {{ count($adm_order_draft) + count($adm_order_wedding_draft) }} @lang('messages.unsubmitted order')</p>
                                            </a>
                                        </li> 
                                    @endif
                                    @if (count($adm_order_active)>0)
                                        <li>
                                            <a href="/orders">
                                                <i class="active icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Active Order')</p>
                                                <p class="description-notif">@lang('messages.You have') {{ count($adm_order_active) }} @lang('messages.active orders')</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if (count($adm_order_rejected)>0)
                                        <li>
                                            <a href="/orders">
                                                <span>{{ count($adm_order_rejected) }}</span>
                                                <i class="rejected icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Rejected Order') </p>
                                                <p class="description-notif">@lang('messages.You have') {{ count($adm_order_rejected) }} @lang('messages.rejected orders')</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if (count($adm_order_invalid)>0)
                                        <li>
                                            <a href="/orders">
                                                <span>{{ count($adm_order_invalid) }}</span>
                                                <i class="invalid icon-copy fa fa-tags" aria-hidden="true"></i>
                                                <p>@lang('messages.Invalid Order') </p>
                                                <p class="description-notif">@lang('messages.You have') {{ count($adm_order_invalid) }} @lang('messages.Invalid orders')</p>
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
        <div class="user-info-dropdown">
            <div class="dropdown">
                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                    <div class="user-icon">
                        @if (Auth::user()->profileimg == '')
                            <img src="{{ asset('storage/user/profile/default_user_img.png') }}" alt=""
                                class="avatar-photo">
                        @else
                        <img src="{{ asset('storage/user/profile') .'/'. Auth::user()->profileimg }}" alt="{{ Auth::user()->name }}" >
                        @endif
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
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
        </div>
        
    </div>
</div>
