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
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <i class="icon-copy fa fa-car"></i>
                                {{ 'ORD.' . date('Ymd', strtotime($now)) . '.TRN' . $orderno }}
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard">@lang('messages.Dashboard')</a></li>
                                    <li class="breadcrumb-item"><a href="transports">@lang('messages.Transports')</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:history.back()">{{ $transport->name }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ $price->type }}</a></li>
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
                            <form id="createOrderTranspor" action="{{ route('func.create.order-transport',$price->id) }}" method="POST">
                                @csrf
                                <div class="modal-body pd-5">
                                    <div class="business-name">{{ $business->name }}</div>
                                    <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                                    <hr class="form-hr">
                                    <div class="row">
                                        <div class="col-md-6 m-b-8">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="page-list"> @lang('messages.Order No') </div>
                                                    <div class="page-list"> @lang('messages.Order Date') </div>
                                                    <div class="page-list"> @lang('messages.Service') </div>
                                                </div>
                                                <div class="col-8">
                                                    <div class="page-list-value">
                                                        {{ 'ORD.' . date('Ymd', strtotime($now)) . '.TRN' . $orderno }}
                                                    </div>
                                                    <div class="page-list-value">
                                                        {{ date('D, d-M-Y', strtotime($now)) }}
                                                    </div>
                                                    <div class="page-list-value">@lang('messages.Transport')</div>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        {{-- Admin create order ================================================================= --}}
                                        @canany(['posDev','posAuthor','posRsv'])
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label for="user_id" class="col-sm-12 col-md-12 col-form-label">Select Agent <span>*</span></label>
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
                                    </div>
                                    <div class="page-subtitle">@lang('messages.Order')</div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="page-list">@lang('messages.Transport')</div>
                                                    <div class="page-list">@lang('messages.Type')</div>
                                                    <div class="page-list">@lang('messages.Capacity')</div>
                                                </div>
                                                <div class="col-8">
                                                    <div class="page-list-value">{{ $transport->name }}</div>
                                                    <div class="page-list-value">@lang('messages.'.$transport->type)</div>
                                                    <div class="page-list-value">{{ $transport->capacity . ' ' }}@lang('messages.Seat')</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="page-list">@lang('messages.Service')</div>
                                                    @if ($price->type == "Daily Rent")
                                                        <div class="page-list">@lang('messages.Location')</div>
                                                    @else
                                                        <div class="page-list">@lang('messages.Src') - @lang('messages.Dst')</div>
                                                    @endif
                                                </div>
                                                <div class="col-8">
                                                    <div class="page-list-value">{{ $price->type }}</div>
                                                    @if ($price->type == "Daily Rent")
                                                        <div class="page-list-value">{{ $price->src }}</div>
                                                    @else
                                                        <div class="page-list-value">{{ $price->src." - ".$price->dst }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="page-note">
                                                @if (isset($transport->include))
                                                    <b>@lang('messages.Include') :</b><br>
                                                    {!! $transport->include !!}
                                                @endif
                                                @if (isset($transport->additional_info) or isset($price->additional_info))
                                                    <hr class="form-hr">
                                                    <b>@lang('messages.Additional Information') :</b> <br>
                                                    {!! $transport->additional_info !!}<br>
                                                    {!! $price->additional_info !!}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="page-subtitle">@lang('messages.Details')</div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="number_of_guests">@lang('messages.Number of Guest') </label>
                                                            <input name="number_of_guests" min="1" max="{{ $transport->capacity }}" wire:model="number_of_guests" class="form-control @error('number_of_guests') is-invalid @enderror" placeholder="@lang('messages.Maximum') {{ $transport->capacity }} @lang('messages.Guests')" type="number" required>
                                                        @error('number_of_guests')
                                                            <span class="invalid-feedback">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                @if ($price->type == "Daily Rent")
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="duration" >@lang('messages.Duration') </label>
                                                                <input name="duration" min="1" class="form-control @error('duration') is-invalid @enderror" placeholder="@lang('messages.Insert duration by day')" type="number" required>
                                                            @error('duration')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="order_type" value="{{ $price->type }}">
                                                @else
                                                    <input type="hidden" name="duration" value="{{ $price->duration }}">
                                                    <input type="hidden" name="order_type" value="{{ $price->type }}">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="airport_shuttle_type">@lang('messages.Type')<span> *</label>
                                                            <select name="airport_shuttle_type" id="airport_shuttle_type" class="custom-select @error('airport_shuttle_type') is-invalid @enderror">
                                                                <option selected value="Arrival">@lang('messages.Arrival')</option>
                                                                <option value="Departure">@lang('messages.Departure')</option>
                                                            </select>
                                                            @error('airport_shuttle_type')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($price->type == "Daily Rent")
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pickup_date">@lang('messages.Pickup Date') </label>
                                                    <input type="text" id="pickup_date" readonly name="pickup_date" class="form-control datetimepicker @error('pickup_date') is-invalid @enderror" placeholder="@lang('messages.Select date and time')"  required>
                                                    @error('pickup_date')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="pickup_location">@lang('messages.Pickup Location') </label>
                                                        <input type="text" name="pickup_location" class="form-control @error('pickup_location') is-invalid @enderror" placeholder="@lang('messages.Location')" required>
                                                    @error('pickup_location')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="dropoff_location">@lang('messages.Dropoff Location') </label>
                                                        <input type="text" name="dropoff_location" class="form-control @error('dropoff_location') is-invalid @enderror" placeholder="@lang('messages.Location')" required>
                                                    @error('dropoff_location')
                                                        <span class="invalid-feedback">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        @elseif($price->type == "Airport Shuttle")
                                            <div class="col-md-12">
                                                <div id="arrival_fields" class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="arrival_flight">@lang('messages.Arrival Flight')</label>
                                                            <input type="text" name="arrival_flight" class="form-control @error('arrival_flight') is-invalid @enderror" placeholder="@lang('messages.Arrival Flight')" value="{{ old('arrival_flight') }}">
                                                            @error('arrival_flight')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="arrival_time">@lang('messages.Arrival Date and Time')</label>
                                                            <input readonly type="text" name="arrival_time" class="form-control datetimepicker @error('arrival_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ old('arrival_time') }}">
                                                            @error('arrival_time')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="departure_fields" class="row" style="display: none;">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="departure_flight">@lang('messages.Departure Flight')</label>
                                                            <input type="text" name="departure_flight" class="form-control @error('departure_flight') is-invalid @enderror" placeholder="@lang('messages.Departure Flight')" value="{{ old('departure_flight') }}">
                                                            @error('departure_flight')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="departure_time">@lang('messages.Departure Date and Time')</label>
                                                            <input readonly type="text" name="departure_time" class="form-control datetimepicker @error('departure_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ old('departure_time') }}">
                                                            @error('departure_time')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="guest_detail" >@lang('messages.Guest Detail') </label>
                                                <textarea name="guest_detail" placeholder="@lang('messages.Insert guest name')" class="textarea_editor form-control border-radius-0" value="{{ old('guest_detail') }}" required></textarea>
                                                @error('guest_detail')
                                                    <div class="alert alert-danger">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="note">@lang('messages.Note')</label>
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
                                        <div class="col-md-12 m-b-8">
                                            <div class="box-price-kicked">
                                                <div class="row">
                                                    <div class="col-6 col-md-6">
                                                        @if (isset($bookingcode->code) or $promotion_price > 0)
                                                            <div class="modal-text-price">@lang('messages.Price per pax') :</div>
                                                            @if (isset($bookingcode->code))
                                                                <div class="modal-text-price" id="booking_code_label">@lang('messages.Booking Code') :</div>
                                                            @endif
                                                            @if ($promotion_price > 0)
                                                                <div class="modal-text-price" id="promotion_label">@lang('messages.Promotion') :</div>
                                                            @endif
                                                            <hr class="form-hr">
                                                        @endif
                                                        <div class="price-name">@lang('messages.Total Price')</div>
                                                    </div>
                                                    <div class="col-6 col-md-6 text-right">
                                                        @if (isset($bookingcode->code) or $promotion_price > 0)
                                                            <div class="modal-num-price"><span id="normal_price">{{ currencyFormatUsd($normal_price) }}</span></div>
                                                            @if (isset($bookingcode->code))
                                                                <div id="booking_code_discount" class="kick-back">{{ currencyFormatUsd($bookingcode->discounts) }}</div>
                                                            @endif
                                                            @if ($promotion_price > 0)
                                                                <div id="promotion_price" class="kick-back">{{ currencyFormatUsd($promotion_price) }}</div>
                                                            @endif
                                                            <hr class="form-hr">
                                                        @endif
                                                        <div class="price-tag"><span id="final_price">{{ currencyFormatUsd($final_price) }}</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="notif-modal text-left">
                                    @lang('messages.Please make sure all the data is correct before you place an order')
                                </div>
                                <input type="hidden" name="orderno" value="{{ 'ORD.' . date('Ymd', strtotime($now)) . '.TRN' . $orderno }}">
                                <input type="hidden" name="transport_id" value="{{ $transport->id }}">
                                <input type="hidden" name="bookingcode_id" value="{{ $bookingcode ? $bookingcode->id : NULL }}">
                                <div class="card-box-footer">
                                    <button type="submit" form="createOrderTranspor" class="btn btn-primary"><i class="fa fa-shopping-basket"></i> @lang('messages.Order')</button>
                                    <button type="button" onclick="goBack()" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const selectShuttleType = document.getElementById("airport_shuttle_type");
                const arrivalFields = document.getElementById("arrival_fields");
                const departureFields = document.getElementById("departure_fields");
                function toggleFields() {
                    if (selectShuttleType.value === "Arrival") {
                        arrivalFields.style.display = "flex";
                        departureFields.style.display = "none";
                    } else {
                        arrivalFields.style.display = "none";
                        departureFields.style.display = "flex";
                    }
                }
                toggleFields();
                selectShuttleType.addEventListener("change", toggleFields);
            });
        </script>
       <script>
        document.addEventListener("DOMContentLoaded", function() {
            const durationInput = document.querySelector('input[name="duration"]');
            const orderTypeInput = document.querySelector('input[name="order_type"]');
            const finalPriceSpan = document.getElementById('final_price');
            const bookingCodeDiscount = document.getElementById('booking_code_discount');
            const promotionPrice = document.getElementById('promotion_price');

            if (!durationInput || !finalPriceSpan) {
                console.error("Elemen input duration atau final_price tidak ditemukan.");
                return;
            }

            const transportPrice = @json($transport_price);
            const bookingDiscount = @json(session('bookingcode') ? session('bookingcode')->discounts : 0);
            const promotion = @json($promotion_price);
            function calculateFinalPrice() {
                let duration = parseInt(durationInput.value) || 1;
                let orderType = orderTypeInput ? orderTypeInput.value : "";

                let totalPrice;
                if (orderType === "Daily Rent") {
                    totalPrice = (transportPrice * duration) - bookingDiscount - promotion;
                } else {
                    totalPrice = transportPrice - bookingDiscount - promotion;
                }

                totalPrice = totalPrice < 0 ? 0 : totalPrice;

                finalPriceSpan.textContent = totalPrice.toLocaleString("en-US", {
                    style: "currency",
                    currency: "USD",
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
            calculateFinalPrice();
            durationInput.addEventListener('input', calculateFinalPrice);
            if (bookingDiscount === 0 && bookingCodeDiscount) {
                bookingCodeDiscount.style.display = 'none';
                const bookingCodeLabel = document.getElementById('booking_code_label');
                if (bookingCodeLabel) bookingCodeLabel.style.display = 'none';
            }
            if (promotion === 0 && promotionPrice) {
                promotionPrice.style.display = 'none';
                const promotionLabel = document.getElementById('promotion_label');
                if (promotionLabel) promotionLabel.style.display = 'none';
            }
        });
    </script>
    
    
    </body>
</html>