<?php
    use App\Models\Orders;
    use App\Models\OrderWedding;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\File;
    use Illuminate\Support\Facades\Input;
    use App\Http\Requests\StoremenuRequest;
    use App\Http\Requests\UpdatemenuRequest;
    // USER =======================================================================================================
    $now = Carbon::now();
    $menu_order = Orders::where('user_id', Auth::user()->id)->get();
    $order_active = Orders::where('user_id', Auth::user()->id)
        ->where('status','Active')
        ->where('checkin','>=',$now)->get();
    $order_rejected = Orders::where('user_id', Auth::user()->id)
        ->where('status','Rejected')
        ->where('checkin','>=',$now)->get();
    $order_invalid = Orders::where('user_id', Auth::user()->id)
        ->where('status','Invalid')
        ->where('checkin','>=',$now)->get();
    $order_waiting = Orders::where('user_id', Auth::user()->id)
        ->where('status','Waiting')
        ->where('checkin','>=',$now)->get();
    $order_draft = Orders::where('user_id', Auth::user()->id)
        ->where('status','Draft')
        ->where('checkin','>=',$now)->get();
    $order_confirmed = Orders::where('user_id', Auth::user()->id)
        ->where('status','Confirmed')
        ->where('checkin','>=',$now)->get();
    $order_approved = Orders::where('user_id', Auth::user()->id)
        ->where('status','Approved')
        ->where('checkin','>=',$now)->get();
    $order_wedding_draft = OrderWedding::where('agent_id', Auth::user()->id)
        ->where('status','Draft')
        ->where('checkin','>=',$now)->get();
    $order_wedding_pending = OrderWedding::where('agent_id', Auth::user()->id)
        ->where('status','Pending')
        ->where('checkin','>=',$now)->get();
    $order_wedding_approved = OrderWedding::where('agent_id', Auth::user()->id)
        ->where('status','Approved')
        ->where('checkin','>=',$now)->get();
    // Admin =======================================================================================================
    $adm_menu_order = Orders::where('user_id', Auth::user()->id)->get();
    $adm_order_active = Orders::where('user_id', Auth::user()->id)
        ->where('status','Active')
        ->where('checkin','>=',$now)->get();
    $adm_order_rejected = Orders::where('user_id', Auth::user()->id)
        ->where('status','Rejected')
        ->where('checkin','>=',$now)->get();
    $adm_order_invalid = Orders::where('user_id', Auth::user()->id)
        ->where('status','Invalid')
        ->where('checkin','>=',$now)->get();
    $adm_order_waiting = Orders::where('user_id', Auth::user()->id)
        ->where('status','Waiting')
        ->where('checkin','>=',$now)->get();
    $adm_order_draft = Orders::where('user_id', Auth::user()->id)
        ->where('status','Draft')
        ->where('checkin','>=',$now)->get();
    $adm_order_confirmed = Orders::where('user_id', Auth::user()->id)
        ->where('status','Confirmed')
        ->where('checkin','>=',$now)->get();
    $adm_order_approved = Orders::where('user_id', Auth::user()->id)
        ->where('status','Approved')
        ->where('checkin','>=',$now)->get();
    $adm_order_wedding_draft = OrderWedding::where('agent_id', Auth::user()->id)
        ->where('status','Draft')
        ->where('checkin','>=',$now)->get();
    $adm_order_wedding_pending = OrderWedding::where('agent_id', Auth::user()->id)
        ->where('status','Pending')
        ->where('checkin','>=',$now)->get();
    $adm_order_wedding_approved = OrderWedding::where('agent_id', Auth::user()->id)
        ->where('status','Approved')
        ->where('checkin','>=',$now)->get();
    $ord_pend = Orders::where('status','Pending')->where('checkin','>',$now)->get();
    $ord_wedding_pend = OrderWedding::where('status','Pending')->where('checkin','>=',$now)->get();
    $cord_pend = count($ord_pend)+count($ord_wedding_pend);
    $cord_tour_pend = count($ord_pend);
    $cord_wedding_pend = count($ord_wedding_pend);
?>
<div class="d-print-none header">
    <div class="header-left">
        <div class="menu-icon dw dw-menu"></div>
    </div>
    <div class="header-right">
        {{-- <div><p>{{ count($adm_order_wedding_draft) }}</p></div> --}}
        {{-- @if (app()->getLocale() == 'en')
            <div class="language"><a href="{{ url('lang/zh') }}"><i class="fa fa-language" aria-hidden="true"></i> 中文</a></div>
        @else
            <div class="language"><a href="{{ url('lang/en') }}"><i class="fa fa-language" aria-hidden="true"></i> English</a></div>
        @endif --}}
        @if (!request()->isMethod('post'))
            <a class="dropdown-togle" href="#" role="button" data-toggle="dropdown">
                <div class="lang-dropdown m-r-18">
                    <div class="dropdown">
                        <div class="lang-icon">
                            @if (app()->getLocale() == 'en')
                                <i class="fa fa-language" aria-hidden="true"></i> English
                            @elseif (app()->getLocale() == 'zh')
                                <i class="fa fa-language" aria-hidden="true"></i> 繁體中文
                            @else
                                <i class="fa fa-language" aria-hidden="true"></i> 简体中文
                            @endif
                        </div>
                        
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                            <a class="dropdown-item" href="{{ url('lang/en?redirect=' . urlencode(request()->fullUrl())) }}">
                                <i class="fa fa-language" aria-hidden="true"></i> English
                            </a>
                            <a class="dropdown-item" href="{{ url('lang/zh?redirect=' . urlencode(request()->fullUrl())) }}">
                                <i class="fa fa-language" aria-hidden="true"></i> 繁體中文
                            </a>
                            <a class="dropdown-item" href="{{ url('lang/zh-CN?redirect=' . urlencode(request()->fullUrl())) }}">
                                <i class="fa fa-language" aria-hidden="true"></i> 简体中文
                            </a>
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
                    <a class="dropdown-item" href="/profile"><i class="dw dw-user1"></i>{{ Auth::user()->name }}</a>
                    <a class="dropdown-item" href="/orders"><i class="icon-copy fa fa-tags" aria-hidden="true"></i> @lang('messages.Order')</a>
                    <a class="dropdown-item" href="/manual-book"><i class="icon-copy fa fa-book" aria-hidden="true"></i> @lang('messages.Manual Book')</a>
                    <a class="dropdown-item" href="/terms-and-conditions"><i class="fa fa-info-circle" aria-hidden="true"></i> @lang('messages.Term And Condition')</a>
                    <a class="dropdown-item" href="/privacy-policy"><i class="fa fa-info-circle" aria-hidden="true"></i> @lang('messages.Privacy Policy')</a>
                    <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="dw dw-logout"></i> @lang('messages.Log Out')</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    
                </div>
            </div>
        </div>
        
    </div>
</div>
