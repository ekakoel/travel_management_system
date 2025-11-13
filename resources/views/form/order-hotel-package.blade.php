@section('title','Order Hotel')
@section('content')
    @extends('layouts.head')
        <div class="mobile-menu-overlay"></div>
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <i class="icon-copy dw dw-hotel-o"></i>
                                    {{ $orderNumber }}
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    @include('partials.breadcrumbs', [
                                        'breadcrumbs' => [
                                            ['url' => route('dashboard.index'), 'label' => __('messages.Dashboard')],
                                            ['url' => route('view.hotels'), 'label' => __('messages.Hotels')],
                                            ['url' => route('view.hotel-detail',$hotel->code), 'label' => $hotel->name],
                                            ['label' => __('messages.Room')." ".$room->rooms],
                                            ['label' => dateFormat($checkin)." - ".dateFormat($checkout) ],
                                        ]
                                    ])
                                </nav>
                            </div>
                        </div>
                    </div>
                    @include('partials.alerts')
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fa fa-tag" aria-hidden="true"></i>@lang('messages.Create Order')</div>
                                </div>
                                <div class="row">
                                    <div class="col-6 col-md-6">
                                        <div class="order-bil text-left">
                                            <img src="{{ asset($logoDark) }}" alt="{{ $altLogo }}" loading="lazy">
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
                                <div class="business-name">{{ $business->name }}</div>
                                <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                                <hr class="form-hr">
                                <form id="create-order" action="{{ route('func.create.order-hotel-package',$package->id) }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table tb-list nowrap">
                                                <tbody>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Order No')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ $orderNumber }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Order Date')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ dateTimeFormat($now) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="htd-1">
                                                            @lang('messages.Service')
                                                        </td>
                                                        <td class="htd-2">
                                                            {{ $service }}
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
                                        @canany(['posDev','posAuthor','posRsv'])
                                            @include('partials.admin-create-order', compact('agents'))
                                        @endcanany
                                    </div>
                                    <div class="page-subtitle">@lang('messages.Order Details')</div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table tb-list nowrap">
                                                    <tbody>
                                                        <tr>
                                                            <td class="htd-1">
                                                                @lang('messages.Hotel Promotions')
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
                                                        <tr>
                                                            <td class="htd-1">
                                                                @lang('messages.Room')
                                                            </td>
                                                            <td class="htd-2">
                                                                {{ $room->rooms }}
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
                                                                {{ $duration." ".__("messages.nights")}}
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
                                            @if ($package->benefits || $package->benefits || $package->additional_info)
                                                <div class="col-md-12">
                                                    <div class="page-note">
                                                        @if ($package->benefits)
                                                            <b>@lang('messages.Benefits') :</b>
                                                            {!! $package->benefits !!}
                                                        @endif
                                                        @if ($package->include)
                                                            <b>@lang('messages.Include') :</b>
                                                            {!! $package->include !!}
                                                        @endif
                                                        @if ($package->additional_info)
                                                            <b>@lang('messages.Additional Info') :</b>
                                                            {!! $package->additional_info !!}
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="page-subtitle">@lang('messages.Suites and Villas')</div>
                                        
                                            <div class="row">
                                                <div class="col-md-12 room-list">
                                                    <ol id="dynamic_field">
                                                        <li class="m-b-8">
                                                            <div class="room-container ">
                                                                <div class="row">
                                                                    <div class="col-sm-12">
                                                                        <div class="subtitle">@lang('messages.Room')</div>
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <div class="form-group">
                                                                            <label for="number_of_guests[]">@lang('messages.Number of Guest')</label>
                                                                            <input id="number_of_guests[]" type="number" min="1" max="{{ $room_capacity }}" name="number_of_guests[]" class="form-control m-0 @error('number_of_guests[]') is-invalid @enderror" placeholder="{{ __('messages.Capacity') ." ".$room->capacity." ". __('messages.guests') }}" value="{{ old('number_of_guests[]') }}" required>
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
                                                                            <label for="extra_bed_id[]">@lang('messages.Extra Bed')<span> * </span><i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.Select an extra bed if the room is occupied by more than room capacity')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label><br>
                                                                            <select name="extra_bed_id[]" id="extra_bed_id[]" type="text" class="custom-select @error('extra_bed_id[]') is-invalid @enderror">
                                                                                <option selected value="" data-ebPrice="0">@lang('messages.None')</option>
                                                                                @foreach ($hotel->extrabeds as $eb)
                                                                                    <option value="{{ $eb->id }}" data-ebprice="{{ $eb->calculatePrice($usdrates, $tax) }}">@lang('messages.'.$eb->name) @lang('messages.'.$eb->type)</option>
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
                                                        <input name="request_quotation" type="checkbox" style="display: block !important;" value="1"> 
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
                                                            <div class="alert alert-danger">
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
                                                        <select name="airport_shuttle_in" id="airportShuttleIn" type="text" class="custom-select @error('airport_shuttle_in') is-invalid @enderror">
                                                            <option selected value="" data-transportin="0">@lang('messages.Select Transport')</option>
                                                            @if ($transports)
                                                                @foreach ($transports as $transport)
                                                                    @php
                                                                        $transport_in = $transport->prices->where('transports_id',$transport->id)->first();
                                                                    @endphp
                                                                    @if ($transport_in)
                                                                        <option value="{{ $transport->id }}" data-transportin=1 data-transportpricein={{ $transport_in->calculatePrice($usdrates, $tax) }} data-transportinpriceid={{ $transport_in->id }}>{{ $transport_in->transport->brand." ".$transport_in->transport->name." - (".$transport_in->transport->capacity.")" }}</option>
                                                                    @else
                                                                        <option value="{{ $transport->id }}" data-transportin=1 data-transportpricein=0 data-transportinpriceid=0>{{ $transport->brand." ".$transport->name." - (".$transport->capacity.")" }}</option>
                                                                    @endif
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
                                                        <select name="airport_shuttle_out" id="airportShuttleOut" type="text" class="custom-select @error('airport_shuttle_out') is-invalid @enderror">
                                                            <option selected value="" data-transportout="0" data-transportpriceout=0>@lang('messages.Select Transport')</option>
                                                            @if ($transports)
                                                                @foreach ($transports as $transport)
                                                                    @php
                                                                        $transport_out = $transport_prices->where('transports_id',$transport->id)->first();
                                                                    @endphp
                                                                    @if ($transport_out)
                                                                        <option value="{{ $transport->id }}" data-transportout=1 data-transportpriceout={{ $transport_out->calculatePrice($usdrates, $tax) }} data-transportoutpriceid={{ $transport_out->id }}>{{ $transport_out->transport->brand." ".$transport_out->transport->name." - (".$transport_out->transport->capacity.")" }}</option>
                                                                    @else
                                                                        <option value="{{ $transport->id }}" data-transportout=1 data-transportpriceout=0 data-transportoutpriceid=0>{{ $transport->brand." ".$transport->name." - (".$transport->capacity.")" }}</option>
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                <option value="Request" data-transportout="1">@lang('messages.Request')</option>
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
                                                        <textarea id="note" name="note" placeholder="@lang('messages.Optional')" class="tiny_mce form-control border-radius-0" value="{{ old('note') }}"></textarea>
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
                                                                <div id="suitesAndVillasText" class="normal-text">@lang('messages.Suites and Villas')</div>
                                                                <div id="airportShuttle" class="normal-text">@lang('messages.Airport Shuttle')</div>
                                                                <div id="extraBedText" class="normal-text">@lang('messages.Extra Bed')</div>
                                                                <hr class="form-hr">
                                                                <div class="total-price">@lang('messages.Total Price')</div>
                                                            </div>
                                                            <div class="col-6 col-md-6 text-right">
                                                                <div id="suitesAndVillasPrice" class="text-price"><span id="suitesAndVillasPriceLable"></span></div>
                                                                <div id="airportShuttlePrice" class="text-price"><span id="airportShuttleText"></span></div>
                                                                <div id="extraBedPrice" class="text-price"><span id='extraBedPriceTotal'>{{ currencyFormatUsd($final_price) }}</span></div>
                                                                <hr class="form-hr">
                                                                <div class="total-price">
                                                                    <span id="finalprice">{{ currencyFormatUsd($final_price) }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="orderno" value="{{ $orderNumber }}">
                                            <input type="hidden" name="airport_shuttle_in_price_id" id="airport_shuttle_in_price_id" value="">
                                            <input type="hidden" name="airport_shuttle_out_price_id" id="airport_shuttle_out_price_id" value="">
                                            <input type="hidden" name="duration" id="duration" value="{{ $duration }}">
                                            <input type="hidden" name="final_price" id="final_price" value="{{ $final_price }}">
                                            <input type="hidden" name="var_package_price" id='var_package_price' value="{{ $final_price }}">
                                            <div class="card-box-footer">
                                                <button type="submit" form="create-order" id="normal-reserve" class="btn btn-primary"><i class="icon-copy fa fa-shopping-basket" aria-hidden="true"></i> @lang('messages.Order')</button>
                                                <button type="button" onclick="goBack()" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                            </div>
                                        
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                @include('layouts.footer')
            </div>
        </div>
        @include('partials.loading-form', ['id' => 'create-order'])
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script type="text/javascript">
            function goBack() {
              window.history.back();
            }
            $(document).ready(function () {
                var currencyFormatter = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD',minimumFractionDigits: 0, maximumFractionDigits: 0  });
                function debounce(func, wait) {
                    let timeout;
                    return function(...args) {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(this, args), wait);
                    };
                }
                var maxField = 8;
                var duration = parseInt($('#duration').val()) || 0;
                var roomPrice = parseInt($('#var_price_pax').val()) || 0;
                var promoPrice = parseInt($('#var_package_price').val()) || 0;
                var orderService = $('#services').val() || "Other";
                var roomCount = 1;
                var roomId = 1;
                var aiportStatusIn = 0;
                var transportPriceIn = 0;
                var transportPriceOut = 0;
                var aiportStatusOut = 0;

                function toggleExtraBedSelection() {
                    $('input[name="number_of_guests[]"]').each(function () {
                        var numberOfGuests = parseInt($(this).val()) || 0;  
                        var extraBedSelect = $(this).closest('div').next('div').find('select[name="extra_bed_id[]"]');

                        if (numberOfGuests > 2) {
                            extraBedSelect.prop('disabled', false);
                        } else {
                            extraBedSelect.prop('disabled', true);
                            extraBedSelect.val("");
                        }
                    });
                    
                }
                function calculatePrices() {
                    var totalPromoPrice = promoPrice * roomCount;
                    var finalPrice = totalPromoPrice;
                    var totalExtraBedPrice = 0;
                    $("select[name='extra_bed_id[]']").each(function () {
                        var selectedOption = $(this).find(":selected");
                        var ebPrice = parseInt(selectedOption.data('ebprice')) || 0;
                        var extraBedPrice = ebPrice * duration;
                        totalExtraBedPrice += extraBedPrice;
                    });

                    $("select[name='airport_shuttle_in']").each(function () {
                        var airportSelectedOption = $(this).find(":selected");
                        var inStatus = parseInt(airportSelectedOption.data('transportin')) || 0;
                        var inPrice = parseInt(airportSelectedOption.data('transportpricein')) || 0;
                        var inPriceId = parseInt(airportSelectedOption.data('transportinpriceid')) || 0;
                        aiportStatusIn = inStatus;
                        transportPriceIn = inPrice;
                        transportPriceInId = inPriceId;
                    });
                    $("select[name='airport_shuttle_out']").each(function () {
                        var airportSelectedOption = $(this).find(":selected");
                        var outStatus = parseInt(airportSelectedOption.data('transportout')) || 0;
                        var outPrice = parseInt(airportSelectedOption.data('transportpriceout')) || 0;
                        var outPriceId = parseInt(airportSelectedOption.data('transportoutpriceid')) || 0;
                        aiportStatusOut = outStatus;
                        transportPriceOut = outPrice;
                        transportPriceOutId = outPriceId;
                    });

                    finalPrice += totalExtraBedPrice;
                    finalPrice += transportPriceIn + transportPriceOut;
                    
                    $('#promo_price').val(totalPromoPrice);
                    $('#final_price').val(finalPrice);
                    $("#total_promo_price").text(totalPromoPrice);

                    
                    if (aiportStatusIn === 1 && aiportStatusOut === 1) {
                        document.getElementById('airportShuttle').hidden = false;
                        document.getElementById('airportShuttlePrice').hidden = false;
                        document.getElementById('airportShuttleText').hidden = false;
                        document.getElementById('airport_shuttle_in_price_id').value = transportPriceInId;
                        document.getElementById('airport_shuttle_out_price_id').value = transportPriceOutId;
                        if (transportPriceIn > 0 && transportPriceOut > 0) {
                            $("#airportShuttleText").text(currencyFormatter.format(transportPriceIn + transportPriceOut));
                            $("#finalprice").text(currencyFormatter.format(finalPrice));
                        }else{
                            $("#airportShuttleText").text("@lang('messages.To be advised')");
                            $("#finalprice").text("@lang('messages.To be advised')");
                        }

                    }else if (aiportStatusIn === 1){
                        document.getElementById('airportShuttle').hidden = false;
                        document.getElementById('airportShuttlePrice').hidden = false;
                        document.getElementById('airportShuttleText').hidden = false;
                        document.getElementById('airport_shuttle_in_price_id').value = transportPriceInId;
                        document.getElementById('airport_shuttle_out_price_id').value = transportPriceOutId;
                        
                        if (transportPriceIn > 0) {
                            $("#airportShuttleText").text(currencyFormatter.format(transportPriceIn + transportPriceOut));
                            $("#finalprice").text(currencyFormatter.format(finalPrice));
                        }else{
                            $("#airportShuttleText").text("@lang('messages.To be advised')");
                            $("#finalprice").text("@lang('messages.To be advised')");
                        }
                    }else if (aiportStatusOut === 1){
                        document.getElementById('airportShuttle').hidden = false;
                        document.getElementById('airportShuttlePrice').hidden = false;
                        document.getElementById('airportShuttleText').hidden = false;
                        document.getElementById('airport_shuttle_in_price_id').value = transportPriceInId;
                        document.getElementById('airport_shuttle_out_price_id').value = transportPriceOutId;
                        if (transportPriceOut > 0) {
                            $("#airportShuttleText").text(currencyFormatter.format(transportPriceIn + transportPriceOut));
                            $("#finalprice").text(currencyFormatter.format(finalPrice));
                        }else{
                            $("#airportShuttleText").text("@lang('messages.To be advised')");
                            $("#finalprice").text("@lang('messages.To be advised')");
                        }
                    }else{
                        document.getElementById('airportShuttle').hidden = true;
                        document.getElementById('airportShuttleText').hidden = true;
                        document.getElementById('airportShuttlePrice').hidden = true;
                        document.getElementById('airport_shuttle_in_price_id').value = transportPriceInId;
                        document.getElementById('airport_shuttle_out_price_id').value = transportPriceOutId;
                        $("#finalprice").text(currencyFormatter.format(finalPrice));
                    }

                    if (totalExtraBedPrice > 0) {
                        document.getElementById('extraBedPrice').hidden = false;
                        document.getElementById('extraBedText').hidden = false;
                        $("#extraBedPriceTotal").text(currencyFormatter.format(totalExtraBedPrice));
                    }else{
                        document.getElementById('extraBedPrice').hidden = true;
                        document.getElementById('extraBedText').hidden = true;
                        $("#extraBedPriceTotal").text(currencyFormatter.format(totalExtraBedPrice));
                    }
                    if (totalPromoPrice > 0) {
                        document.getElementById('suitesAndVillasText').hidden = false;
                        document.getElementById('suitesAndVillasPrice').hidden = false;
                        $("#suitesAndVillasPriceLable").text(currencyFormatter.format(totalPromoPrice));
                    }else{
                        document.getElementById('suitesAndVillasText').hidden = true;
                        document.getElementById('suitesAndVillasPrice').hidden = true;
                        $("#suitesAndVillasPriceLable").text(currencyFormatter.format(totalPromoPrice));
                    }
                }
                $("#airportShuttleIn").change(function() {
                    calculatePrices();
                });

                $("#airportShuttleOut").change(function() {
                    calculatePrices();
                });
                $('input[name="number_of_guests[]"]').on('input', function () {
                    toggleExtraBedSelection();
                });
                $(document).on('change', 'select[name="extra_bed_id[]"]', function () {
                    calculatePrices();
                });
                $('#add').on('click', function () {
                    if (roomCount < maxField) {
                        roomCount++;
                        roomId++;
                        var newRoom = `
                            <li id="li${roomId}" class="m-b-8">
                                <div class="room-container">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="subtitle">@lang('messages.Room')</div>
                                            <button class="btn btn-remove" name="remove" id="${roomId}" type="button">
                                                <i class="icon-copy fa fa-close" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="number_of_guests[]">@lang('messages.Number of Guest')</label>
                                                <input id="number_of_guests[]" type="number" min="1" max="{{ $room_capacity }}" name="number_of_guests[]" class="form-control m-0 @error('number_of_guests[]') is-invalid @enderror" placeholder="{{ __('messages.Capacity') ." ".$room->capacity." ". __('messages.guests') }}" value="{{ old('number_of_guests[]') }}" required>
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
                                                <label for="extra_bed_id[]">@lang('messages.Extra Bed')<span> * </span><i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.Select an extra bed if the room is occupied by more than room capacity')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label><br>
                                                <select name="extra_bed_id[]" id="extra_bed_id[]" type="text" class="custom-select @error('extra_bed_id[]') is-invalid @enderror">
                                                    <option selected value="" data-ebPrice="0">@lang('messages.None')</option>
                                                    @foreach ($hotel->extrabeds as $eb)
                                                        <option value="{{ $eb->id }}" data-ebprice="{{ $eb->calculatePrice($usdrates, $tax) }}">@lang('messages.'.$eb->name) @lang('messages.'.$eb->type)</option>
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
                            </li>`;
                        $('#dynamic_field').append(newRoom);
                        calculatePrices();
                    }
                });
                $(document).on('click', '.btn-remove', function () {
                    var buttonId = $(this).attr("id");
                    $('#li' + buttonId).remove();
                    roomCount--;
                    roomId--;
                    calculatePrices();
                });
                calculatePrices();
            });
            

        </script>
@endsection