@php
     if (isset($promotions)){
        $pr = count($promotions);
        $promotion_price = 0;
        for ($i=0; $i < $pr; $i++) { 
            $promotion_price = $promotion_price + $promotions[$i]->discounts;
        }
    }else{
        $promotion_price = 0;
    }
    $orderno = count($corders) + 1;
@endphp
<!DOCTYPE html>
<html>
<head>
	<!-- Basic Page Info -->
	<meta charset="utf-8">
	<title>{{ config('app.name', 'Bali Kami Tour') }}</title>
	<!-- Site favicon -->
	<link rel="apple-touch-icon" sizes="180x180" href="images/balikami/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="images/balikami/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="images/balikami/favicon-16x16.png">
	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
	<!-- CSS -->
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="/panel/styles/core.css">
	<link rel="stylesheet" type="text/css" href="/panel/styles/icon-font.min.css">
	<link rel="stylesheet" type="text/css" href="/panel/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="/panel/css/responsive.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="/panel/styles/style.css">
	<link rel="stylesheet" type="text/css" href="/panel/fullcalendar/fullcalendar.css">
	<link rel="stylesheet" type="text/css" href="/css/style.css">
	<link rel="stylesheet" type="text/css" href="/panel/dropzone/dropzone.css">
	<!-- Slick Slider css -->
	<link rel="stylesheet" type="text/css" href="/panel/slick/slick.css">
	<!-- bootstrap-touchspin css -->
	<link rel="stylesheet" type="text/css" href="/panel/bootstrap-touchspin/jquery.bootstrap-touchspin.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js "></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script type="text/javascript">  
		if(performance.navigation.type == 2){
		   location.reload(true);
		}
	</script>
	</head>
	<body class="sidebar-light">
        @include('component.menu')
        {{-- @include('component.sysload') --}}
        @include('layouts.left-navbar')
        <div class="mobile-menu-overlay"></div>
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <i class="icon-copy dw dw-hotel-o"></i>
                                    @if ($service == "Hotel")
                                        {{ 'ORD.' . date('Ymd', strtotime($now)) . '.HNP' . $orderno }}
                                    @elseif ($service == "Hotel Promo")
                                        {{ 'ORD.' . date('Ymd', strtotime($now)) . '.HPP' . $orderno }}
                                    @elseif ($service == "Hotel Package")
                                        {{ 'ORD.' . date('Ymd', strtotime($now)) . '.HPA' . $orderno }}
                                    @endif
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard">@lang('messages.Dashboard')</a></li>
                                        <li class="breadcrumb-item"><a href="hotels">@lang('messages.Hotels')</a></li>
                                        <li class="breadcrumb-item"><a href="hotel-{{ $hotel->code }}">{{ $hotel->name }}</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">@lang('messages.Room'){{ " ".$room->rooms }}</a></li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fa fa-tag" aria-hidden="true"></i>@lang('messages.Create Order')</div>
                                </div>
                                <div class="row">
                                    <div class="col-6 col-md-6">
                                        <div class="order-bil text-left">
                                            <img src="{{ asset(config('app.logo_dark')) }}" alt="{{ config('app.alt_logo') }}">
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-6 flex-end">
                                        <div class="label-title">@lang('messages.Order')</div>
                                    </div>
                                    <div class="col-md-12 text-right">
                                        <div class="label-date float-right" style="width: 100%">
                                            {{ dateFormat($now) }}
                                        </div>
                                    </div>
                                </div>
                                <form id="create-order" action="/fadd-order" method="POST">
                                    @csrf
                                    <div class="business-name">{{ $business->name }}</div>
                                    <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                                    <hr class="form-hr">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table tb-list nowrap">
                                                <tbody>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Order No')
                                                        </td>
                                                        <td class="htd-2">
                                                            @if ($service == "Hotel")
                                                                <b>{{ 'ORD.' . date('Ymd', strtotime($now)) . '.HNP' . $orderno }}</b>
                                                            @elseif ($service == "Hotel Promo")
                                                                <b>{{ 'ORD.' . date('Ymd', strtotime($now)) . '.HPP' . $orderno }}</b>
                                                            @elseif ($service == "Hotel Package")
                                                                <b>{{ 'ORD.' . date('Ymd', strtotime($now)) . '.HPA' . $orderno }}</b>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Order Date')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ dateFormat($now) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Service')
                                                        </td>
                                                        <td class="htd-2">
                                                            @if ($service == "Hotel")
                                                                @lang('messages.Hotel')
                                                            @elseif ($service == "Hotel Promo")
                                                                @lang('messages.Hotel Promo')
                                                            @elseif ($service == "Hotel Package")
                                                                @lang('messages.Hotel Package')
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Region')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ $hotel->region }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        {{-- Admin create order ================================================================= --}}
                                        @canany(['posDev','posAuthor','posRsv'])
                                            <div class="col-md-6">
                                                <div class="mobile">
                                                    <hr class="form-hr">
                                                </div>
                                                <div class="form-group ">
                                                    <label for="user_id">Select Agent <span>*</span></label>
                                                    <div class="col-sm-12">
                                                        <select name="user_id" class="custom-select @error('user_id') is-invalid @enderror" value="{{ old('user_id') }}" required>
                                                            <option selected value="">Select Agent</option>
                                                            @foreach ($agents as $agent)
                                                                <option value="{{ $agent->id }}">{{ $agent->username." (".$agent->code.") @".$agent->office }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('user_id')
                                                            <div class="alert-form">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        @endcanany
                                        {{-- Admin create order ================================================================= --}}
                                    </div>
                                    <div class="page-subtitle">@lang('messages.Hotel Detail')</div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table tb-list nowrap">
                                                <tbody>
                                                    @if ($service == "Hotel")
                                                        <tr>
                                                            <td class="htd-1">
                                                                @lang('messages.Hotel Name')
                                                            </td>
                                                            <td class="htd-2">
                                                                {{ $hotel->name }}
                                                            </td>
                                                        </tr>
                                                    @elseif ($service == "Hotel Promo")
                                                        <tr>
                                                            <td class="htd-1">
                                                                @lang('messages.Hotel Name')
                                                            </td>
                                                            <td class="htd-2">
                                                                {{ $hotel->name }}
                                                            </td>
                                                        </tr>
                                                    @elseif ($service == "Hotel Package")
                                                        <tr>
                                                            <td class="htd-1">
                                                                @lang('messages.Hotel Package')
                                                            </td>
                                                            <td class="htd-2">
                                                                {{ $package->name }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="htd-1">
                                                                @lang('messages.Hotel Name')
                                                            </td>
                                                            <td class="htd-2">
                                                                {{ $hotel->name }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Room')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ $room->rooms }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Maximum Capacity')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ $room->capacity." " }}@lang('messages.Guest')
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table tb-list">
                                                <tbody>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Duration')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ $duration." " }}@lang('messages.Night')
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Check In')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ dateFormat($checkin) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Check Out')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ dateFormat($checkout) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        @if ($service != "Hotel Promo")
                                            <div class="col-md-12">
                                                <div class="page-note">
                                                    @if ($service == "Hotel")
                                                        @if ($room->include != "")
                                                            <b>@lang('messages.Include') :</b> <br>
                                                            {!! $room->include !!}
                                                            <hr class="form-hr">
                                                        @endif
                                                        @if (isset($room->additional_info))
                                                            <b>@lang('messages.Additional Information') :</b> <br>
                                                            {!! $room->additional_info !!}
                                                        @endif
                                                    @elseif ($service == "Hotel Package")
                                                        @if ($package->include != "")
                                                            <b>@lang('messages.Include') :</b> <br>
                                                            {!! $package->include !!}
                                                            <hr class="form-hr">
                                                        @endif
                                                        @if ($package->benefits != "")
                                                            <b>@lang('messages.Benefit') :</b> <br>
                                                            {!! $package->benefits !!}
                                                            <hr class="form-hr">
                                                        @endif
                                                        @if ($package->additional_info != "")
                                                            <b>@lang('messages.Additional Information') :</b> <br>
                                                            {!! $package->additional_info !!}
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-md-12">
                                                <div class="page-note">
                                                    @php
                                                        $pr_id = json_decode($promo_id);
                                                        $cpr = count($pr_id);
                                                        $promoid = 0;
                                                        $promo_booking_code = [];
                                                        $promo_include = [];
                                                        $promo_benefits = [];
                                                        $promo_additional_info = [];
                                                        $promo_name = [];
                                                        $book_periode_start = [];
                                                        $book_periode_end = [];
                                                        $periode_start = [];
                                                        $periode_end = [];
                                                    @endphp
                                                    @for ($p = 0; $p < $cpr; $p++)
                                                        @if ($pr_id[$p] != 0)
                                                            @php
                                                                $promos = $promo->where('id',$pr_id[$p])->first();
                                                                $pid = $promos->id;
                                                            @endphp
                                                            @if ($pid != $promoid)
                                                                @php
                                                                    $p_benefits = $promos->benefits;
                                                                    array_push($book_periode_start,$promos->book_periode_start);
                                                                    array_push($book_periode_end,$promos->book_periode_end);
                                                                    array_push($periode_start,$promos->periode_start);
                                                                    array_push($periode_end,$promos->periode_end);
                                                                @endphp
                                                                @if (isset($promos->booking_code))
                                                                    @php
                                                                        array_push($promo_booking_code,$promos->booking_code);
                                                                    @endphp
                                                                @endif
                                                                @if (isset($promos->name))
                                                                    @php
                                                                        array_push($promo_name,$promos->name);
                                                                    @endphp
                                                                @endif
                                                                @if (isset($promos->include))
                                                                    @php
                                                                        array_push($promo_include,$promos->include);
                                                                    @endphp
                                                                @endif
                                                                @if (isset($p_benefits))
                                                                    @php
                                                                        array_push($promo_benefits,$p_benefits);
                                                                    @endphp
                                                                @endif
                                                                @if (isset($promos->additional_info))
                                                                    @php
                                                                        array_push($promo_additional_info,$promos->additional_info);
                                                                    @endphp
                                                                @endif
                                                            @endif
                                                            @php
                                                                $promoid = $pid;
                                                            @endphp
                                                        @endif
                                                    @endfor
                                                    @php
                                                        $p_benefit = implode($promo_benefits);
                                                        $p_include = implode($promo_include);
                                                        $p_ai = implode($promo_additional_info);
                                                    @endphp
                                                    @if ($p_benefit != "")
                                                        <b>@lang('messages.Benefits') :</b><br>
                                                        {!! $p_benefit !!}
                                                    @endif
                                                    @if ($p_include != "")
                                                        <b>@lang('messages.Include') :</b> <br>
                                                        {!! $p_include !!}
                                                    @endif
                                                    @if ($p_ai != "")
                                                        <b>@lang('messages.Additional Information') :</b> <br>
                                                        {!! $p_ai !!}
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if ($hotel->cancellation_policy != "")
                                        <div class="page-subtitle">@lang('messages.Cancellation Policy')</div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="cancelation-policy">
                                                    {!! $hotel->cancellation_policy !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="page-subtitle">@lang('messages.Guest and Room Details')</div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <ol id="dynamic_field">
                                                <li class="m-b-8">
                                                    <div class="room-container ">
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="subtitle">@lang('messages.Room') 1</div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div class="form-group">
                                                                    <label for="number_of_guests[]">@lang('messages.Number of Guest')</label>
                                                                    <input onchange="fRequest()" id="number_of_guests[]" type="number" min="1" max="{{ $room->capacity }}" name="number_of_guests[]" class="form-control m-0 @error('number_of_guests[]') is-invalid @enderror" placeholder="@lang('messages.Number of Guest')" value="{{ old('number_of_guests[]') }}" required>
                                                                    @error('number_of_guests[]')
                                                                        <div class="alert alert-danger">
                                                                            {{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        
                                                            <div class="col-sm-9">
                                                                <div class="form-group">
                                                                    <label for="guest_detail[]">@lang('messages.Guest Name')  <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.Children guests must include the age on the back of their name. ex: Children Name(age)')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label>
                                                                    <input type="text" name="guest_detail[]" class="form-control m-0 @error('guest_detail[]') is-invalid @enderror" placeholder="@lang('messages.Separate names with commas')" value="{{ old('guest_detail[]') }}" required>
                                                                    @error('guest_detail[]')
                                                                        <div class="alert alert-danger">
                                                                            {{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        
                                                            <div class="col-sm-4">
                                                                <div class="form-group">
                                                                    <label for="special_day[]">@lang('messages.Special Day') <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.If during your stay there are guests who have special days such as birthdays, aniversaries, and others')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label>
                                                                    <input type="text" name="special_day[]" class="form-control m-0 @error('special_day[]') is-invalid @enderror" placeholder="@lang('messages.ex Birthday')" value="{{ old('special_day[]') }}">
                                                                    @error('special_day[]')
                                                                        <div class="alert alert-danger">
                                                                            {{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <div class="form-group">
                                                                    <label for="special_date[]">@lang('messages.Insert Date for Special Day')</label>
                                                                    <input type="date" name="special_date[]" class="form-control m-0 @error('special_date[]') is-invalid @enderror" placeholder="ex: yyyy-mm-dd" value="{{ old('special_date[]') }}">
                                                                    @error('special_date[]')
                                                                        <div class="alert alert-danger">
                                                                            {{ $message }}
                                                                        </div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4" style="place-self: padding-bottom: 6px;">
                                                                <div class="form-group">
                                                                    <label for="extra_bed_id[]">@lang('messages.Extra Bed')<span> * </span><i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.Select an extra bed if the room is occupied by more than 2 guests')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label><br>
                                                                    <select name="extra_bed_id[]" id="extra_bed_id[]" type="text" class="custom-select @error('extra_bed_id[]') is-invalid @enderror" required>
                                                                        <option selected value="">@lang('messages.Select extra bed')</option>
                                                                        <option value="0">@lang('messages.None')</option>
                                                                        @foreach ($extrabed as $eb)
                                                                            <option value="{{ $eb->id }}">@lang('messages.'.$eb->name) @lang('messages.'.$eb->type)</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('extra_bed[]')
                                                                        <span class="invalid-feedback">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ol>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="checkbox-left">
                                                <input name="request_quotation" type="checkbox" style="display: block !important;" value="Yes"> 
                                                <p>
                                                    @lang('messages.Ask for quote rates for rooms more than 8 units')
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <button id="add" type="button" class="btn btn-primary"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Add More Room')</button>
                                        </div>
                                    </div>
                                    <div class="page-subtitle">@lang('messages.Flight and Transport Detail')</div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="arrival_flight">@lang('messages.Arrival Flight')</label>
                                                <input type="text" name="arrival_flight" class="form-control @error('arrival_flight') is-invalid @enderror" placeholder="@lang('messages.Arrival Flight')" value="{{ old('arrival_flight') }}">
                                                @error('arrival_flight')
                                                    <div
                                                        class="alert alert-danger">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="arrival_time">@lang('messages.Arrival Date and Time')</label>
                                                <input readonly type="text" name="arrival_time" class="form-control datetimepicker @error('arrival_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ old('arrival_time') }}">
                                                @error('arrival_time')
                                                    <div class="alert alert-danger">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="airport_shuttle_in">@lang('messages.Airport Shuttle') <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.Request')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label>
                                                <select name="airport_shuttle_in" id="airport_shuttle_in" type="text" class="custom-select @error('airport_shuttle_in') is-invalid @enderror">
                                                    <option selected value="">@lang('messages.Select Transport')</option>
                                                    @if (isset($transports))
                                                        @foreach ($transports as $transport_in)
                                                            <option value="{{ $transport_in->id }}">{{ $transport_in->name." - ".$transport_in->capacity }} <i> Seats</i></option>
                                                        @endforeach
                                                    @else
                                                        <option value="Request">@lang('messages.Request')</option>
                                                    @endif
                                                </select>
                                                @error('airport_shuttle_in')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="departure_flight">@lang('messages.Departure Flight')</label>
                                                <input type="text" name="departure_flight" class="form-control @error('departure_flight') is-invalid @enderror" placeholder="@lang('messages.Departure Flight')" value="{{ old('departure_flight') }}">
                                                @error('departure_flight')
                                                    <div class="alert alert-danger">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="departure_time"> @lang('messages.Departure Date and Time')</label>
                                                <input readonly type="text" name="departure_time" class="form-control datetimepicker @error('departure_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ old('departure_time') }}">
                                                @error('departure_time')
                                                    <div class="alert alert-danger">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="airport_shuttle_out">@lang('messages.Airport Shuttle') <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.Request')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label>
                                                <select name="airport_shuttle_out" id="airport_shuttle_out" type="text" class="custom-select @error('airport_shuttle_out') is-invalid @enderror">
                                                    <option selected value="">@lang('messages.Select Transport')</option>
                                                    @if (isset($transports))
                                                    @foreach ($transports as $transport_out)
                                                        <option value="{{ $transport_out->id }}">{{ $transport_out->name." - ".$transport_out->capacity }} <i> Seats</i></option>
                                                    @endforeach
                                                @else
                                                    <option value="Request">@lang('messages.Request')</option>
                                                @endif
                                                </select>
                                                @error('airport_shuttle_out')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="page-subtitle">@lang('messages.Remark')</div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <textarea id="note" name="note" placeholder="@lang('messages.Optional')" class="textarea_editor form-control border-radius-0" value="{{ old('note') }}"></textarea>
                                                @error('note')
                                                    <div class="alert alert-danger">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                        <div id="optional_service" class="page-subtitle">@lang('messages.Price')</div>
                                        </div>
                                        <div class="col-md-12 m-b-8">
                                            <div class="box-price-kicked">
                                                <div class="row">
                                                    <div class="col-6 col-md-6">
                                                        @if ($kick_back > 0 or isset($bookingcode) or $promotion_price > 0)
                                                            <div class="promo-text">@lang('messages.Normal Price')</div>
                                                            <hr class="form-hr">
                                                            @if ($kick_back > 0)
                                                                <div class="promo-text">@lang('messages.Kick Back')</div>
                                                            @endif
                                                            @if ($promotion_price > 0)
                                                                <div class="promo-text">@lang('messages.Promotion')</div>
                                                            @endif
                                                            @if ($bookingcode)
                                                                <div class="promo-text">@lang('messages.Booking Code')</div>
                                                            @endif
                                                            <hr class="form-hr">
                                                            <div class="total-price">@lang('messages.Total Price')</div>
                                                        @else
                                                            <div class="total-price">@lang('messages.Total Price')</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-6 col-md-6 text-right">
                                                        @if ($kick_back > 0 or $bookingcode or $promotion_price > 0)
                                                            <div class="text-price line-trought"><span id='total_normal_price'>{{  number_format(($normal_price))  }}</span></div>
                                                            <hr class="form-hr">
                                                            @if ($kick_back > 0)
                                                                <div class="kick-back"><span id="total_kick_back">{{ number_format($kick_back) }}</span></div>
                                                            @endif
                                                            @if ($promotion_price > 0)
                                                                <div class="kick-back"><span id="total_kick_back">{{ number_format($promotion_price) }}</span></div>
                                                            @endif
                                                            @if ($bookingcode)
                                                                @php
                                                                    $book_code = $bookingcode->discounts;
                                                                @endphp
                                                                <div class="kick-back"><span>{{ number_format($bookingcode->discounts) }}</span></div>
                                                            @else
                                                                @php
                                                                    $book_code = 0;
                                                                @endphp
                                                            @endif
                                                            @php
                                                                $f_price = $final_price - $kick_back - $promotion_price - $book_code;
                                                            @endphp
                                                            <hr class="form-hr">
                                                            <div class="total-price">
                                                                <span id="finalprice">{{ number_format($f_price) }}</span>
                                                            </div>
                                                        @else
                                                            <div class="total-price"><span id="finalprice">{{ number_format($final_price ) }}</span></div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notif-modal text-left">
                                        @lang('messages.Please make sure all the data is correct before you make an order')
                                    </div>
                                    @if ($service == "Hotel")
                                        <input type="hidden" name="service" value="Hotel">
                                        <input type="hidden" name="include" value="{{ $room->include }}">
                                        <input type="hidden" name="additional_info" value="{{ $room->additional_info }}">
                                        <input type="hidden" name="orderno" value="{{ 'ORD.' . date('Ymd', strtotime($now)) . '.HNP' . $orderno }}">

                                    @elseif ($service == "Hotel Promo")
                                        @php
                                            $p_booking_code = json_encode($promo_booking_code);
                                            $p_name = json_encode($promo_name);
                                            $p_book_periode_start = json_encode($book_periode_start);
                                            $p_book_periode_end = json_encode($book_periode_end);
                                            $p_periode_start = json_encode($periode_start);
                                            $p_periode_end = json_encode($periode_end);
                                        @endphp
                                        <input type="hidden" name="promo_id" value="{{ $promo_id }}">
                                        <input type="hidden" name="service" value="Hotel Promo">
                                        <input type="hidden" name="booking_code" value="{{ $p_booking_code }}">
                                        <input type="hidden" name="promo_name" value="{{ $p_name }}">

                                        <input type="hidden" name="book_period_start" value="{{ $p_book_periode_start }}">
                                        <input type="hidden" name="book_period_end" value="{{ $p_book_periode_end }}">
                                        <input type="hidden" name="period_start" value="{{ $p_periode_start }}">
                                        <input type="hidden" name="period_end" value="{{ $p_periode_end }}">
                                        
                                        <input type="hidden" name="benefits" value="{{ $p_benefits }}">
                                        <input type="hidden" name="include" value="{{ $p_include }}">
                                        <input type="hidden" name="additional_info" value="{{ $p_ai }}">
                                        <input type="hidden" name="orderno" value="{{ 'ORD.' . date('Ymd', strtotime($now)) . '.HPP' . $orderno }}">

                                    @elseif ($service == "Hotel Package")
                                        <input type="hidden" name="period_start" value="{{ date('Y-m-d',strtotime($package->stay_period_start)) }}">
                                        <input type="hidden" name="period_end" value="{{ date('Y-m-d',strtotime($package->stay_period_end)) }}">
                                        <input type="hidden" name="booking_code" value="{{ $package->booking_code }}">
                                        <input type="hidden" name="service" value="Hotel Package">
                                        <input type="hidden" name="package_name" value="{{ $package->name }}">
                                        <input type="hidden" name="benefits" value="{{ $package->benefits }}">
                                        <input type="hidden" name="include" value="{{  $package->include  }}">
                                        <input type="hidden" name="additional_info" value="{{ $package->additional_info }}">
                                        <input type="hidden" name="orderno" value="{{ 'ORD.' . date('Ymd', strtotime($now)) . '.HPA' . $orderno }}">
                                    @endif
                                    <input type="hidden" name="page" value="hotel-detail">
                                    <input type="hidden" name="action" value="Create Order">
                                    <input type="hidden" name="sales_agent" value="{{ Auth::user()->id }}">
                                    <input type="hidden" name="servicename" value="{{ $hotel->name }}">
                                    <input type="hidden" name="subservice" value="{{ $room->rooms }}">
                                    <input type="hidden" name="service_id" value="{{ $hotel->id }}">
                                    <input type="hidden" name="subservice_id" value="{{ $room->id }}">
                                    <input type="hidden" name="duration" id="duration" value="{{ $duration }}">
                                    <input type="hidden" name="capacity" value="{{ $room->capacity }}">
                                    <input type="hidden" name="checkin" value="{{ $checkin }}">
                                    <input type="hidden" name="checkout" value="{{ $checkout }}">
                                    <input type="hidden" name="cancellation_policy" value="{{ $hotel->cancellation_policy }}">
                                    <input type="hidden" name="location" value="{{ $hotel->region }}">
                                    @if (isset($bookingcode->discounts))
                                        <input type="hidden" name="bookingcode_discounts" id='var_bcode_discounts' value="{{ $bookingcode->discounts }}">
                                        <input type="hidden" name="bookingcode_id" value="{{ $bookingcode->id }}">
                                    @else
                                        <input type="hidden" name="bookingcode_discounts" id='var_bcode_discounts' value="0">
                                    @endif
                                    @if (isset($promotions))
                                        <input type="hidden" name="promotion_price" id='var_promotion_price' value="{{ $promotion_price }}">
                                    @endif
                                    @if (Auth::user()->type != "Admin")
                                        <input type="hidden" name="sales_agent" value="{{ Auth::user()->id }}">
                                    @endif
                                    <input type="hidden" name="normal_price" value="{{ $normal_price }}">
                                    <input type="hidden" name="total_normal_price" id="total_normal_price" value="{{ $normal_price }}">
                                    <input type="hidden" name="kick_back" id="kick_back" value="{{ $kick_back }}">
                                    <input type="hidden" name="kick_back_per_pax" value="{{ $kick_back_per_pax }}">
                                    <input type="hidden" name="var_kick_back" id='var_kick_back' value="{{ $kick_back }}">
                                    <input type="hidden" name="var_normal_price" id='var_normal_price' value="{{ $normal_price }}">
                                    <input type="hidden" name="var_price_pax" id='var_price_pax' value="{{ $price_pax }}">
                                    <div class="card-box-footer">
                                        <button type="submit" form="create-order" id="normal-reserve" class="btn btn-primary"><i class="icon-copy fa fa-shopping-basket" aria-hidden="true"></i> @lang('messages.Order')</button>
                                        <button type="button" onclick="goBack()" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                    </div>
                                </Form>
                            </div>
                        </div>
                    </div>
                @include('layouts.footer')
            </div>
        </div>
        <!-- js -->
        <script src="panel/jquery/jquery.min.js"></script>
        <script src="panel/jquery/jquery.validate.min.js"></script>
        <script src="panel/script/core.js"></script>
        <script src="panel/script/script.min.js"></script>
        <script src="panel/script/process.js"></script>
        <script src="panel/script/layout-settings.js"></script>
        <script src="panel/apexcharts/apexcharts.min.js"></script>
        <script src="panel/script/dashboard.js"></script>
        <script src="panel/dropzone/dropzone.js"></script>
        <script src="panel/ckeditor/ckeditor.js"></script>
        <script src="panel/bootstrap-touchspin/jquery.bootstrap-touchspin.js"></script>
        <script src="panel/fullcalendar/fullcalendar.min.js"></script>
        <script src="vendors/scripts/calendar-setting.js"></script>
        <script src="assets/dist/pdfreader/pspdfkit.js"></script>
        <script src="panel/slick/slick.min.js"></script>
        <script src="js/sweetalert/sweetalert2.all.min.js"></script>
        <script src="js/script.js"></script>
        <script src="panel/datatables/js/responsive.bootstrap4.min.js"></script>
        <script>
            $('form').submit(function (event) {
                if ($(this).hasClass('submitted')) {
                    event.preventDefault();
                }
                else {
                    $(this).find(':submit').html('<i class="fa fa-spinner fa-spin spn"></i> @lang('messages.Loading')');
                    $(this).addClass('submitted');
                }
            });
        </script>
        <script>
            function goBack() {
              window.history.back();
            }
        </script>
        <script type="text/javascript">
            $(document).ready(function(){ 
                var maxField = 8;
                var duration = parseInt($('#duration').val());
                var final_price =  parseInt($('#final_price').val());
                var roomprice =  parseInt($('#var_price_pax').val());
                var kickback =  parseInt($('#var_kick_back').val());
                var normalprice =  parseInt($('#var_normal_price').val());
                var bcode_discounts =  parseInt($('#var_bcode_discounts').val());
                var promotion_price =  parseInt($('#var_promotion_price').val());
                var r = 1;  
                var i=1;
                $('#add').click(function(){
                    if(r < maxField){ 
                        r++;
                        i++;
                        $('#dynamic_field').append('<li id="li'+i+'" class="m-b-8"><div class="room-container "><div class="row"><div class="col-sm-12"><div class="subtitle">@lang('messages.Room') '+i+'</div><button class="btn btn-remove" name="remove" id="'+i+'" type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i> </button></div><div class="col-sm-3"><div class="form-group"><label for="number_of_guests[]">@lang('messages.Number of Guest')</label><input type="number" min="1" max="{{ $room->capacity }}" name="number_of_guests[]" class="form-control m-0 @error('number_of_guests[]') is-invalid @enderror" placeholder="@lang('messages.Number of Guest')" value="{{ old('number_of_guests[]') }}" required>@error('number_of_guests[]')<div class="alert alert-danger">{{ $message }}</div>@enderror</div></div><div class="col-sm-9"><div class="form-group"><label for="guest_detail[]">@lang('messages.Guest Name')  <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.Children guests must include the age on the back of their name. ex: Children Name(age)')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label><input type="text" name="guest_detail[]" class="form-control m-0 @error('guest_detail[]') is-invalid @enderror" placeholder="@lang('messages.Separate names with commas')" value="{{ old('guest_detail[]') }}" required>@error('guest_detail[]')<div class="alert alert-danger">{{ $message }}</div>@enderror</div></div><div class="col-sm-4"><div class="form-group"><label for="special_day[]">@lang('messages.Special Day') <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.If during your stay there are guests who have special days such as birthdays, aniversaries, and others')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label><input type="text" name="special_day[]" class="form-control m-0 @error('special_day[]') is-invalid @enderror" placeholder="@lang('messages.ex Birthday')" value="{{ old('special_day[]') }}">@error('special_day[]')<div class="alert alert-danger">{{ $message }}</div>@enderror</div></div><div class="col-sm-4"><div class="form-group"><label for="special_date[]">@lang('messages.Insert Date for Special Day')</label><input type="date" name="special_date[]" class="form-control m-0 @error('special_date[]') is-invalid @enderror" placeholder="ex: yyyy-mm-dd" value="{{ old('special_date[]') }}">@error('special_date[]')<div class="alert alert-danger">{{ $message }}</div>@enderror</div></div><div class="col-sm-4" style="place-self: padding-bottom: 6px;"><div class="form-group"><label for="extra_bed_id[]">@lang('messages.Extra Bed') <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.Select an extra bed if the room is occupied by more than 2 guests')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label><br><select name="extra_bed_id[]" type="text" class="custom-select @error('extra_bed_id[]') is-invalid @enderror" required><option selected value="">@lang('messages.Select extra bed')</option><option value=0>None</option>@foreach ($extrabed as $eb)@php $eb_cr = ceil($eb->contract_rate/$usdrates->rate) + $eb->markup; $eb_price = (ceil($eb_cr * $tax->tax / 100) + $eb_cr)* $duration; @endphp <option value="{{ $eb->id }}">{{ $eb->name." (".$eb->type.")"}}</option>@endforeach</select>@error('extra_bed_id[]')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror</div></div></div></div></li>');  
                        
                        if (kickback > 0 || bcode_discounts > 0 || promotion_price > 0) {
                            var totalnormalprice = (normalprice * r);
                            var fp = totalnormalprice;
                            if (kickback > 0) {
                                var total_kick_back = (kickback * r);
                                $('#kick_back').val(total_kick_back);
                                document.getElementById("total_kick_back").innerHTML = total_kick_back;
                            }else{
                                var total_kick_back = 0;
                            }
                            if (bcode_discounts > 0) {
                                var booking_code = bcode_discounts;
                                var f_d_price = fp - booking_code ;
                            }else{
                                var booking_code = 0;
                            }
                            
                            var f_d_price = fp - total_kick_back - booking_code - promotion_price;
                            $('#total_normal_price').val(totalnormalprice);
                            $('#finalprice').val(fp);
                            document.getElementById("total_normal_price").innerHTML = totalnormalprice;
                            document.getElementById("finalprice").innerHTML = f_d_price;

                        } else{
                            var totalnormalprice = (normalprice * r);
                            $('#normal_price').val(totalnormalprice);
                            $('#finalprice').val(totalnormalprice);
                            document.getElementById("total_normal_price").innerHTML = totalnormalprice;
                            document.getElementById("finalprice").innerHTML = totalnormalprice;
                        }
                    }
                });
                $(document).on('click', '.btn-remove', function(){  
                    var button_id = $(this).attr("id");   
                    $('#li'+button_id+'').remove();
                    i--;
                    r--;
                    if (kickback > 0 || bcode_discounts > 0 || promotion_price > 0) {
                        var totalnormalprice = (normalprice * r);
                        var fp = totalnormalprice;
                        if (kickback > 0) {
                            var total_kick_back = (kickback * r);
                            var fp = totalnormalprice - total_kick_back;
                            $('#kick_back').val(total_kick_back);
                            document.getElementById("total_kick_back").innerHTML = total_kick_back;
                        }
                        if (bcode_discounts > 0) {
                            var bk = bcode_discounts;
                            f_d_price = fp - bk ;
                        }
                        if (promotion_price > 0) {
                            f_d_price = fp - promotion_price;
                        }
                        $('#total_normal_price').val(totalnormalprice);
                        $('#finalprice').val(fp);
                        document.getElementById("total_normal_price").innerHTML = totalnormalprice;
                        document.getElementById("finalprice").innerHTML = f_d_price;
                        // document.getElementById("tnp").innerHTML = f_d_price;
                    } else{
                        var totalnormalprice = (normalprice * r);
                        $('#total_normal_price').val(totalnormalprice);
                        $('#finalprice').val(totalnormalprice);
                        document.getElementById("total_normal_price").innerHTML = totalnormalprice;
                        document.getElementById("finalprice").innerHTML = totalnormalprice;
                    }
                }); 
            });
           
        </script>
    </body>
</html>