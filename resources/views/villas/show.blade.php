@section('title','Villa Details')
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="title"><i class="dw dw-building-1"></i>&nbsp; @lang('messages.Villa')</div>
                    @include('partials.breadcrumbs', [
                        'breadcrumbs' => [
                            ['url' => route('dashboard.index'), 'label' => __('messages.Dashboard')],
                            ['url' => route('view.villas.index'), 'label' => __('messages.Private Villa')],
                            ['label' => $villa->name],
                        ]
                    ])
                </div>
                @include('partials.alerts')
                
                <div class="row">
                    @if (session('bookingcode') or $promotions->count() > 0)
                        <div class="col-md-12 promotion-bookingcode">
                            @if (session('bookingcode'))
                                <div class="bookingcode-card">
                                    <div class="icon-card bookingcode">
                                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                    </div>
                                    <div class="content-card">
                                        <div class="code">{{ session('bookingcode.code')}}</div>
                                        <div class="text-card">@lang('messages.Booking Code') @lang('messages.Aplied')</div>
                                        <div class="text-card">@lang('messages.Expired') {{ dateFormat(session('bookingcode.expired_date')) }}</div>
                                    </div>
                                    <div class="content-card-price">
                                        <div class="price"><span>$</span>{{ session('bookingcode.discounts') }}</div>
                                        <form id="removeBookingCode" action="{{ route('bookingcode.remove') }}" method="POST" style="display: inline;">
                                            @csrf
                                        </form>
                                        <button type="submit" form="removeBookingCode" class="btn-remove-code"><i class="fa fa-close" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                            @endif
                            @if ($promotions->count() > 0)
                                @foreach ($promotions as $promotion)
                                    @include('partials.promotion-card', compact('promotion'))
                                @endforeach
                            @endif
                        </div>
                    @endif
                    <div class="col-md-4 mobile">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-box m-b-18 {{ session('booking_dates.duration') < $villa->min_stay?"form-alert":""; }}">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy fa fa-search" aria-hidden="true"></i>@lang('messages.Check Price')</div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <form action="{{ route('view.villa-prices',$villa->code) }}" method="POST" role="search">
                                                    {{ csrf_field() }}
                                                    <div class="form-group">
                                                        <input readonly id="checkincout" name="checkincout" class="form-control @error('checkincout') is-invalid @enderror" type="text" value="{{ dateFormat(session('booking_dates.checkin'))." - ".dateFormat(session('booking_dates.checkout')) }}" placeholder="@lang('messages.Check In') - @lang('messages.Check Out')" required>
                                                        @error('checkincout')
                                                            <span class="invalid-feedback">
                                                                {{ $message }}
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <input type="hidden" name="villa_id" value="{{ $villa->id }}">
                                                    <input type="hidden" name="villacode" value="{{ $villa->code }}">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            @if (session('booking_dates.duration') < $villa->min_stay)
                                                                <p class="error-notification"> @lang('messages.Minimum stay') {{ $villa->min_stay }} @lang('messages.nights')</p>
                                                            @endif
                                                        </div>
                                                        <div class="col-6">
                                                            <button type="submit" class="btn btn-primary" style="float: right;"><i class='icon-copy fa fa-search' aria-hidden='true'></i> @lang('messages.Check Price')</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                   </div>
                    <div class="col-md-8">
                        @if (isset($error) && is_array($error) && count($error) > 0 || isset($success) && is_array($success) && count($success) > 0)
                            @include('partials.msg')
                        @endif
                        <div class="info-action">
                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (\Session::has('warning'))
                                <div class="alert alert-danger">
                                    <ul>
                                        <li>{!! \Session::get('warning') !!}</li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <div class="card-box m-b-18">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="dw dw-building-1"></i> {{ $villa->name }}</div>
                            </div>
                            <div class="page-card">
                                <div class="card-banner m-b-18">
                                    <img src="{{ asset('storage/villas/covers/' . $villa->cover) }}" alt="{{ $villa->name }}" loading="lazy">
                                </div>
                                <div class="row card-content">
                                    <div class="col-6 col-md-6">
                                        <div class="card-subtitle">@lang('messages.Address')</div>
                                        <p><a target="__blank" href="{{ $villa->map }}">{!! $villa->address !!}</a></p>
                                    </div>
                                    <div class="col-6 col-md-6">
                                        <div class="card-subtitle">@lang('messages.Website')</div>
                                        <p><a target="__blank" href="{{ $villa->web }}">{!! $villa->web !!}</a></p>
                                    </div>
                                    @if ($villa->rooms->count() > 0)
                                        <div class="col-6 col-md-6">
                                            <div class="card-subtitle">@lang('messages.Bedroom')</div>
                                            <p><i class="icon-copy fa fa-bed" aria-hidden="true"></i> {{ $villa->rooms->count() }}</p>
                                        </div>
                                        <div class="col-6 col-md-6">
                                            <div class="card-subtitle">@lang('messages.Occupancy')</div>
                                            <p><i class="icon-copy fa fa-user" aria-hidden="true"></i> {{ $guestTotals->total_adult }} + <i class="icon-copy fa fa-child" aria-hidden="true"></i> {{ $guestTotals->total_child }}</p>
                                        </div>
                                    @endif
                                    <div class="col-6 col-md-6">
                                        <div class="card-subtitle">@lang('messages.Minimum Stay')</div>
                                        <p>{{ $villa->min_stay }} {{ $villa->min_stay > 1?__('messages.nights'):__('messages.night') }}</p>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <div class="card-subtitle">@lang('messages.About')</div>
                                        @if (config('app.locale') == "zh")
                                            <p>{!! $villa->description_traditional !!}</p>
                                        @elseif (config('app.locale') == "zh-CN")
                                            <p>{!! $villa->description_simplified !!}</p>
                                        @else
                                            <p>{!! $villa->description !!}</p>
                                        @endif
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <hr class="light-hr">
                                    </div>
                                    @if ($villa->facility)

                                        <div class="col-12 col-md-12">
                                            <div class="card-subtitle">@lang('messages.Facility')</div>
                                            @if (config('app.locale') == "zh")
                                                <p>{!! trim($villa->facility_traditional) !!}</p>
                                            @elseif (config('app.locale') == "zh-CN")
                                                <p>{!! trim($villa->facility_simplified) !!}</p>
                                            @else
                                                <p>{!! trim($villa->facility) !!}</p>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="col-12 col-md-12">
                                        <hr class="light-hr">
                                    </div>
                                    @if ($villa->rooms->count() > 0)
                                        <div class="col-12 col-md-12">
                                            <div class="card-subtitle">@lang('messages.Rooms')</div>
                                            <div class="card-box-content">
                                                @foreach ($villa->rooms as $room)
                                                    @include('villas.partials.room-card', compact('room'))
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                           
                            @foreach ($villa->galleries as $gallery)
                                <div class="card-box-content">
                                    <div class="gallery-item">
                                        <a href="{{ asset('storage/villas/villas-galleries/' . $gallery->image) }}" data-fancybox="gallery" data-caption="{{ $gallery->caption }}">
                                            <img src="{{ asset('storage/villas/villas-galleries/' . $gallery->image) }}" alt="{{ $gallery->caption }}" loading="lazy">
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-4 desktop">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-box m-b-18 {{ session('booking_dates.duration') < $villa->min_stay?"form-alert":""; }}">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy fa fa-search" aria-hidden="true"></i>@lang('messages.Check Price')</div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <form action="{{ route('view.villa-prices',$villa->code) }}" method="POST" role="search">
                                                    {{ csrf_field() }}
                                                    <div class="form-group">
                                                        <input readonly id="checkincout" name="checkincout" class="form-control @error('checkincout') is-invalid @enderror" type="text" value="{{ dateFormat(session('booking_dates.checkin'))." - ".dateFormat(session('booking_dates.checkout')) }}" placeholder="@lang('messages.Check In') - @lang('messages.Check Out')" required>
                                                        @error('checkincout')
                                                            <span class="invalid-feedback">
                                                                {{ $message }}
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <input type="hidden" name="villa_id" value="{{ $villa->id }}">
                                                    <input type="hidden" name="villacode" value="{{ $villa->code }}">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            @if (session('booking_dates.duration') < $villa->min_stay)
                                                                <p class="error-notification"> @lang('messages.Minimum stay') {{ $villa->min_stay }} @lang('messages.nights')</p>
                                                            @endif
                                                        </div>
                                                        <div class="col-6">
                                                            <button type="submit" class="btn btn-primary" style="float: right;"><i class='icon-copy fa fa-search' aria-hidden='true'></i> @lang('messages.Check Price')</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (count($nearvillas)>0)
                        @include('villas.partials.near-villa', compact('nearvillas'))
                    @endif
                </div>
            </div>

            @include('layouts.footer')
        </div>
    </div>
@endsection