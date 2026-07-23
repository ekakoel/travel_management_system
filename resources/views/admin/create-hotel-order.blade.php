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
	<link rel="stylesheet" type="text/css" href="{{ mix('build/backend/css/app.css') }}">
	<link rel="stylesheet" type="text/css" href="/panel/fullcalendar/fullcalendar.css">
	<link rel="stylesheet" type="text/css" href="/css/style.css">
	<link rel="stylesheet" type="text/css" href="/panel/dropzone/dropzone.css">
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<!-- Slick Slider css -->
	<link rel="stylesheet" type="text/css" href="/panel/slick/slick.css">
	<link rel="stylesheet" type="text/css" href="panel/datatables/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" type="text/css" href="panel/datatables/css/responsive.bootstrap4.min.css">
	<!-- bootstrap-touchspin css -->
	<link rel="stylesheet" type="text/css" href="/panel/bootstrap-touchspin/jquery.bootstrap-touchspin.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js "></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
	</head>
	<body class="sidebar-light" data-create-hotel-order-page data-max-rooms="8">
        @include('component.menu')
        @include('layouts.left-navbar')
        <div class="mobile-menu-overlay"></div>
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <x-backend.page-hero>
                        <x-slot name="heading">
                            <i class="icon-copy dw dw-hotel-o"></i>  @lang('messages.Order') - {{ $orderno }}
                        </x-slot>
                    </x-backend.page-hero>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="backend-panel mb-18 p-b-18">
                                <div class="row">
                                    <div class="col-6 col-md-6">
                                        <div class="order-bil text-left">
                                            <img src="{{ asset(config('app.logo_dark')) }}" alt="{{ config('app.alt_logo') }}">
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-6">
                                        <div class="label-title">@lang('messages.Order')</div>
                                    </div>
                                    <div class="col-md-12 text-right">
                                        <div class="label-date float-right backend-width-full">
                                            {{ dateFormat($now) }}
                                        </div>
                                    </div>
                                </div>
                                <form action="/fadd-order" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="business-name">{{ $business->name }}</div>
                                        <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                                        <hr class="form-hr">
                                        <div class="row">
                                            <div class="col-5 col-sm-5 col-md-4 col-lg-4 col-xl-3">
                                                <div class="page-list">@lang('messages.Order No') </div>
                                                <div class="page-list">@lang('messages.Order Date') </div>
                                                <div class="page-list">@lang('messages.Service') </div>
                                                <div class="page-list">@lang('messages.Region') </div>
                                            </div>
                                            <div class="col-7 col-sm-7 col-md-8 col-lg-8 col-xl-9">
                                                <div class="page-list-value">
                                                    <b>{{ $orderno }}</b>
                                                </div>
                                                <div class="page-list-value">
                                                    {{ dateFormat($now) }}
                                                </div>
                                                @if ($service == "Hotel")
                                                    <div class="page-list-value">: @lang('messages.Hotel')</div>
                                                @elseif ($service == "Hotel Promo")
                                                    <div class="page-list-value">: @lang('messages.Hotel Promo')</div>
                                                @elseif ($service == "Hotel Package")
                                                    <div class="page-list-value">: @lang('messages.Hotel Package')</div>
                                                @endif

                                                <div class="page-list-value">: {{ $hotel->region }}</div>
                                            </div>
                                        </div>
                                        <hr class="form-hr">
                                        <div class="page-subtitle">@lang('messages.Hotel Detail')</div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-5 col-sm-5">
                                                        @if ($service == "Hotel")
                                                            <div class="page-list">
                                                                @lang('messages.Hotel Name')
                                                            </div>
                                                        @elseif ($service == "Hotel Promo")
                                                            <div class="page-list">
                                                                @lang('messages.Hotel Promo')
                                                            </div>
                                                            <div class="page-list">
                                                                @lang('messages.Hotel Name')
                                                            </div>
                                                        @elseif ($service == "Hotel Package")
                                                            <div class="page-list">
                                                                @lang('messages.Package')
                                                            </div>
                                                            <div class="page-list">
                                                                @lang('messages.Hotel Name')
                                                            </div>
                                                        @endif
                                                        <div class="page-list">
                                                            @lang('messages.Room')
                                                        </div>
                                                        <div class="page-list">
                                                            @lang('messages.Capacity')
                                                        </div>
                                                    </div>
                                                    <div class="col-7 col-sm-7">
                                                        @if ($service == "Hotel")
                                                            <div class="page-list-value">
                                                                {{ $hotel->name }}
                                                            </div>
                                                        @elseif ($service == "Hotel Promo")
                                                            <div class="page-list-value">
                                                                {{ $promo->name }}
                                                            </div>
                                                            <div class="page-list-value">
                                                                {{ $hotel->name }}
                                                            </div>
                                                        @elseif ($service == "Hotel Package")
                                                            <div class="page-list-value">
                                                                {{ $package->name }}
                                                            </div>
                                                            <div class="page-list-value">
                                                                {{ $hotel->name }}
                                                            </div>
                                                        @endif
                                                        <div class="page-list-value">
                                                            {{ $room->rooms }}
                                                        </div>
                                                        <div class="page-list-value">
                                                            {{ $room->capacity." " }}@lang('messages.Guest')
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-5 col-sm-5">
                                                        <div class="page-list">
                                                            @lang('messages.Duration')
                                                        </div>
                                                        <div class="page-list">
                                                            @lang('messages.Check In')
                                                        </div>
                                                        <div class="page-list">
                                                            @lang('messages.Check Out')
                                                        </div>
                                                    </div>
                                                    <div class="col-7 col-sm-7">
                                                        <div class="page-list-value">
                                                            {{ $duration." " }}@lang('messages.Night')
                                                        </div>
                                                        <div class="page-list-value">
                                                            {{ dateFormat($checkin) }}
                                                        </div>
                                                        <div class="page-list-value">
                                                            {{ dateFormat($checkout) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="page-note">
                                                    @if ($service == "Hotel")
                                                        @if ($room->include != "")
                                                            <b>@lang('messages.Include') :</b> <br>
                                                            {!! $room->include !!}
                                                            <hr class="form-hr">
                                                        @endif
                                                        @if ($room->additional_info != "")
                                                            <b>@lang('messages.Additional Information') :</b> <br>
                                                            {!! $room->additional_info !!}
                                                        @endif
                                                    @elseif ($service == "Hotel Promo")
                                                        @if ($promo->include != "")
                                                            <b>@lang('messages.Include') :</b> <br>
                                                            {!! $promo->include !!}
                                                            <hr class="form-hr">
                                                        @endif
                                                        @if ($promo->benefits != "")
                                                            <b>@lang('messages.Benefit') :</b> <br>
                                                            {!! $promo->benefits !!}
                                                            <hr class="form-hr">
                                                        @endif
                                                        @if ($promo->additional_info != "")
                                                            <b>@lang('messages.Additional Information') :</b> <br>
                                                            {!! $promo->additional_info !!}
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
                                                                    <div class="backend-form-field">
                                                                        <label for="number_of_guests[]">@lang('messages.Number of Guest')</label>
                                                                        <input data-hotel-order-guest-count id="number_of_guests[]" type="number" min="1" max="4" name="number_of_guests[]" class="backend-form-control m-0 @error('number_of_guests[]') is-invalid @enderror" placeholder="@lang('messages.Number of Guest')" value="{{ old('number_of_guests[]') }}" required>
                                                                        @error('number_of_guests[]')
                                                                            <div class="alert alert-danger">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>

                                                                <div class="col-sm-9">
                                                                    <div class="backend-form-field">
                                                                        <label for="guest_detail[]">@lang('messages.Guest Name')  <i data-toggle="tooltip" data-placement="top" title="@lang('messages.Children guests must include the age on the back of their name. ex: Children Name(age)')" class="icon-copy fa fa-info-circle backend-help-icon" aria-hidden="true"></i></label>
                                                                        <input type="text" name="guest_detail[]" class="backend-form-control m-0 @error('guest_detail[]') is-invalid @enderror" placeholder="@lang('messages.Separate names with commas')" value="{{ old('guest_detail[]') }}" required>
                                                                        @error('guest_detail[]')
                                                                            <div class="alert alert-danger">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>

                                                                <div class="col-sm-4">
                                                                    <div class="backend-form-field">
                                                                        <label for="special_day[]">@lang('messages.Special Day') <i data-toggle="tooltip" data-placement="top" title="@lang('messages.If during your stay there are guests who have special days such as birthdays, aniversaries, and others')" class="icon-copy fa fa-info-circle backend-help-icon" aria-hidden="true"></i></label>
                                                                        <input type="text" name="special_day[]" class="backend-form-control m-0 @error('special_day[]') is-invalid @enderror" placeholder="@lang('messages.ex Birthday')" value="{{ old('special_day[]') }}">
                                                                        @error('special_day[]')
                                                                            <div class="alert alert-danger">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <div class="backend-form-field">
                                                                        <label for="special_date[]">@lang('messages.Insert Date for Special Day')</label>
                                                                        <input type="text" readonly name="special_date[]" class="backend-form-control m-0 @error('special_date[]') is-invalid @enderror" placeholder="ex: yyyy-mm-dd" value="{{ old('special_date[]') }}">
                                                                        @error('special_date[]')
                                                                            <div class="alert alert-danger">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-4 backend-align-self-end">
                                                                    @php
                                                                        $extra_bed_room = $extrabed->where('rooms_id',$room->id);
                                                                    @endphp
                                                                    <div class="backend-form-field">
                                                                        <label for="extra_bed_id[]">@lang('messages.Extra Bed')<span> * </span><i data-toggle="tooltip" data-placement="top" title="@lang('messages.Choose an extra bed if the room is occupied by more than 2 guests')" class="icon-copy fa fa-info-circle backend-help-icon" aria-hidden="true"></i></label><br>
                                                                        <select name="extra_bed_id[]" type="text" class="backend-form-control @error('extra_bed_id[]') is-invalid @enderror" required>
                                                                            <option selected value="">@lang('messages.Select extra bed')</option>
                                                                            <option value="0">None</option>
                                                                            @foreach ($extra_bed_room as $eb)
                                                                                @php
                                                                                    $eb_cr = ceil($eb->contract_rate/$usdrates->rate) + $eb->markup;
                                                                                    $eb_price = (ceil($eb_cr * $tax->tax / 100) + $eb_cr) * $duration;
                                                                                @endphp
                                                                                <option value="{{ $eb->id }}">{{ $eb->name." (".$eb->type.") $ ".$eb_price }}</option>
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
                                                <template id="hotelOrderRoomTemplate">
                                                    <li class="m-b-8" id="li__ROOM_ID__">
                                                        <div class="room-container">
                                                            <div class="row">
                                                                <div class="col-sm-12">
                                                                    <div class="subtitle">@lang('messages.Room') __ROOM_NUMBER__</div>
                                                                    <button class="backend-button backend-button-danger" data-hotel-order-remove name="remove" id="__ROOM_ID__" type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i></button>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="backend-form-field">
                                                                        <label for="number_of_guests[]">@lang('messages.Number of Guest')</label>
                                                                        <input data-hotel-order-guest-count type="number" min="1" max="4" name="number_of_guests[]" class="backend-form-control m-0 @error('number_of_guests[]') is-invalid @enderror" placeholder="@lang('messages.Number of Guest')" value="{{ old('number_of_guests[]') }}" required>
                                                                        @error('number_of_guests[]')<div class="alert alert-danger">{{ $message }}</div>@enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-9">
                                                                    <div class="backend-form-field">
                                                                        <label for="guest_detail[]">@lang('messages.Guest Name') <i data-toggle="tooltip" data-placement="top" title="@lang('messages.Children guests must include the age on the back of their name. ex: Children Name(age)')" class="icon-copy fa fa-info-circle backend-help-icon" aria-hidden="true"></i></label>
                                                                        <input type="text" name="guest_detail[]" class="backend-form-control m-0 @error('guest_detail[]') is-invalid @enderror" placeholder="@lang('messages.Separate names with commas')" value="{{ old('guest_detail[]') }}" required>
                                                                        @error('guest_detail[]')<div class="alert alert-danger">{{ $message }}</div>@enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <div class="backend-form-field">
                                                                        <label for="special_day[]">@lang('messages.Special Day') <i data-toggle="tooltip" data-placement="top" title="@lang('messages.If during your stay there are guests who have special days such as birthdays, aniversaries, and others')" class="icon-copy fa fa-info-circle backend-help-icon" aria-hidden="true"></i></label>
                                                                        <input type="text" name="special_day[]" class="backend-form-control m-0 @error('special_day[]') is-invalid @enderror" placeholder="@lang('messages.ex Birthday')" value="{{ old('special_day[]') }}">
                                                                        @error('special_day[]')<div class="alert alert-danger">{{ $message }}</div>@enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <div class="backend-form-field">
                                                                        <label for="special_date[]">@lang('messages.Insert Date')</label>
                                                                        <input type="text" name="special_date[]" class="backend-form-control m-0 @error('special_date[]') is-invalid @enderror" placeholder="ex: yyyy-mm-dd" value="{{ old('special_date[]') }}">
                                                                        @error('special_date[]')<div class="alert alert-danger">{{ $message }}</div>@enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-4 backend-align-self-end">
                                                                    <div class="backend-form-field">
                                                                        <label for="extra_bed_id[]">@lang('messages.Extra Bed') <i data-toggle="tooltip" data-placement="top" title="@lang('messages.Choose an extra bed if the room is occupied by more than 2 guests')" class="icon-copy fa fa-info-circle backend-help-icon" aria-hidden="true"></i></label><br>
                                                                        <select name="extra_bed_id[]" type="text" class="backend-form-control @error('extra_bed_id[]') is-invalid @enderror" required>
                                                                            <option selected value="">@lang('messages.Select extra bed')</option>
                                                                            <option value="0">None</option>
                                                                            @foreach ($extra_bed_room as $eb)
                                                                                @php
                                                                                    $eb_cr = ceil($eb->contract_rate/$usdrates->rate) + $eb->markup;
                                                                                    $eb_price = (ceil($eb_cr * $tax->tax / 100) + $eb_cr) * $duration;
                                                                                @endphp
                                                                                <option value="{{ $eb->id }}">{{ $eb->name." (".$eb->type.") $ ".$eb_price }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('extra_bed_id[]')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </template>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="checkbox">
                                                    <p class="m-t-8">
                                                        <input name="request_quotation" type="checkbox" value="Yes"> @lang('messages.Ask for quote rates for rooms more than 8 units')
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-right">

                                                <button id="add" type="button" class="backend-button backend-button-primary"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Add More Room')</button>
                                            </div>
                                        </div>
                                        <div class="page-subtitle">Flight Detail</div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="backend-form-field row">
                                                    <label for="arrival_flight" class="col-sm-12 col-md-12 col-form-label">@lang('messages.Arrival Flight')</label>
                                                    <div class="col-sm-12 col-md-12">
                                                        <input type="text" name="arrival_flight" class="backend-form-control @error('arrival_flight') is-invalid @enderror" placeholder="@lang('messages.Arrival Flight')" value="{{ old('arrival_flight') }}">
                                                        @error('arrival_flight')
                                                            <div
                                                                class="alert alert-danger">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="backend-form-field row">
                                                    <label for="arrival_time" class="col-sm-12 col-md-12 col-form-label">@lang('messages.Arrival Date and Time')</label>
                                                    <div class="col-sm-12 col-md-12">
                                                        <input readonly type="text" name="arrival_time" class="backend-form-control datetimepicker @error('arrival_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ old('arrival_time') }}">
                                                        @error('arrival_time')
                                                            <div class="alert alert-danger">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="backend-form-field row">
                                                    <label for="departure_flight" class="col-sm-12 col-md-12 col-form-label">@lang('messages.Departure Flight')</label>
                                                    <div class="col-sm-12 col-md-12">
                                                        <input type="text" name="departure_flight" class="backend-form-control @error('departure_flight') is-invalid @enderror" placeholder="@lang('messages.Departure Flight')" value="{{ old('departure_flight') }}">
                                                        @error('departure_flight')
                                                            <div class="alert alert-danger">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="backend-form-field row">
                                                    <label for="departure_time" class="col-sm-12 col-md-12 col-form-label"> @lang('messages.Departure Date and Time')</label>
                                                    <div class="col-sm-12 col-md-12">
                                                        <input readonly type="text" name="departure_time" class="backend-form-control datetimepicker @error('departure_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ old('departure_time') }}">
                                                        @error('departure_time')
                                                            <div class="alert alert-danger">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="backend-form-field row">
                                                    <label for="note" class="col-sm-12 col-md-12 col-form-label">@lang('messages.Note')</label>
                                                    <div class="col-sm-12 col-md-12">
                                                        <textarea data-backend-richtext="true" id="note" name="note" placeholder="@lang('messages.Optional')" class="textarea_editor backend-form-control border-radius-0" value="{{ old('note') }}"></textarea>
                                                        @error('note')
                                                            <div class="alert alert-danger">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 ">
                                                <div class="box-price-kicked">
                                                    @if ($kick_back > 0)
                                                        <div class="row">
                                                            <div class="col-8 col-md-6">
                                                                <div class="usd-rate-kicked">@lang('messages.Normal Price')</div>
                                                                <hr class="form-hr">
                                                                <div class="kick-back">@lang('messages.Kick Back')</div>
                                                                <hr class="form-hr">
                                                                <div class="price-name">@lang('messages.Total Price')</div>
                                                            </div>
                                                            <div class="col-4 col-md-6 text-right">
                                                                <div class="usd-rate-kicked float-right">$ <p id="tda" class="backend-inline-amount"> {{ number_format(($normal_price)) }}</p></div>
                                                                <hr class="form-hr">
                                                                <div class="kick-back float-right">$ <p id="total_kick_back" class="backend-inline-amount"> {{ number_format(($kick_back)) }}</p></div>
                                                                <hr class="form-hr">
                                                                <div class="usd-rate float-right">$ <p id="npkb" class="backend-inline-amount">{{ number_format(($normal_price - $kick_back)) }}</p></div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="row">


                                                                <div class="col-6 col-md-6">
                                                                    <div class="price-name">@lang('messages.Total')</div>
                                                                </div>
                                                                <div class="col-6 col-md-6 text-right">
                                                                    <div class="usd-rate float-right">$ <p id="tda" class="backend-inline-amount"> {{ number_format(($price_pax)) }}</p></div>
                                                                </div>

                                                        </div>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notif-modal text-left">
                                        @lang('messages.Please make sure all the data is correct before you make an order')
                                        {{-- {{ $checkin." - ".$checkout }} --}}
                                    </div>
                                    <div class="modal-footer">
                                        <div class="backend-form-field">
                                            @if ($service == "Hotel")
                                                <input type="hidden" name="service" value="Hotel">
                                                <input type="hidden" name="include" value="{!! $room->include !!}">
                                                <input type="hidden" name="additional_info" value="{{ $room->additional_info }}">
                                            @elseif ($service == "Hotel Promo")
                                                <input type="hidden" name="booking_code" value="{{ $promo->booking_code }}">
                                                <input type="hidden" name="service" value="Hotel Promo">
                                                <input type="hidden" name="promo_name" value="{{ $promo->name }}">
                                                <input type="hidden" name="benefits" value="{{ $promo->benefits }}">
                                                <input type="hidden" name="book_period_start" value="{{ date('Y-m-d',strtotime($promo->book_periode_start)) }}">
                                                <input type="hidden" name="book_period_end" value="{{ date('Y-m-d',strtotime($promo->book_periode_end)) }}">
                                                <input type="hidden" name="period_start" value="{{ date('Y-m-d',strtotime($promo->periode_start)) }}">
                                                <input type="hidden" name="period_end" value="{{ date('Y-m-d',strtotime($promo->periode_end)) }}">
                                                <input type="hidden" name="include" value="{!! $promo->include !!}">
                                                <input type="hidden" name="additional_info" value="{{ $promo->additional_info }}">
                                            @elseif ($service == "Hotel Package")
                                                <input type="hidden" name="period_start" value="{{ date('Y-m-d',strtotime($package->stay_period_start)) }}">
                                                <input type="hidden" name="period_end" value="{{ date('Y-m-d',strtotime($package->stay_period_end)) }}">
                                                <input type="hidden" name="booking_code" value="{{ $package->booking_code }}">
                                                <input type="hidden" name="service" value="Hotel Package">
                                                <input type="hidden" name="package_name" value="{{ $package->name }}">
                                                <input type="hidden" name="benefits" value="{{ $package->benefits }}">
                                                <input type="hidden" name="include" value="{!! $package->include !!}">
                                                <input type="hidden" name="additional_info" value="{{ $package->additional_info }}">
                                            @endif
                                            <input type="hidden" name="page" value="hotel-detail">
                                            <input type="hidden" name="action" value="Create Order">
                                            <input type="hidden" name="orderno" value="{{ $orderno }}">
                                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                            <input type="hidden" name="name" value="{{ Auth::user()->name }}">
                                            <input type="hidden" name="email" value="{{ Auth::user()->email }}">
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

                                            @if ($service == "Hotel Package")
                                                <input type="hidden" name="price_total" id='tda' value="{{ $normal_price * $duration }}">
                                            @else
                                                <input type="hidden" name="price_total" id='price_total' value="{{ ($price_pax * $duration) - $kick_back }}">
                                            @endif
                                            <input type="hidden" name="normal_price" id="normal_price">

                                            <input type="hidden" name="price_pax" id='price_pax' value="{{ $price_pax }}">
                                            <input type="hidden" name="kick_back" id="kick_back" value="{{ $kick_back }}">
                                            <input type="hidden" name="kick_back_per_pax" value="{{ $kick_back_per_pax }}">

                                            <input type="hidden" name="var_kick_back" id='var_kick_back' value="{{ $kick_back }}">
                                            <input type="hidden" name="var_normal_price" id='var_normal_price' value="{{ $normal_price }}">
                                            <input type="hidden" name="var_price_pax" id='var_price_pax' value="{{ $price_pax }}">
                                        </div>
                                        <div class="backend-form-field">
                                            <button type="submit" id="normal-reserve" class="backend-button backend-button-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Order')</button>
                                            <a href="javascript:history.back()">
                                                <button type="button" class="backend-button backend-button-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                            </a>
                                        </div>
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

        <script src="panel/datatables/js/jquery.dataTables.min.js"></script>
        <script src="panel/datatables/js/dataTables.bootstrap4.min.js"></script>
        <script src="panel/datatables/js/dataTables.responsive.min.js"></script>
        <script src="panel/datatables/js/responsive.bootstrap4.min.js"></script>
        <script src="vendors/scripts/datatable-setting.js"></script>
        <script src="{{ mix('build/backend/js/operations/orders-admin/create-hotel-order.js') }}"></script>
    </body>
</html>

