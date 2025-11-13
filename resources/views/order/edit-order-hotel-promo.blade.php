@section('title',__('Edit Order'))
@extends('layouts.head')
@section('content')
<div class="mobile-menu-overlay"></div>
<div class="main-container">
    <div class="pd-ltr-20">
        <div class="page-header">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="title">
                        <i class="icon-copy fa fa-tags"></i>&nbsp; 
                        @lang($order->status == "Draft" ? 'messages.Order' : 'messages.Detail Order')
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        @include('partials.breadcrumbs', [
                            'breadcrumbs' => [
                                ['url' => route('dashboard.index'), 'label' => __('messages.Dashboard')],
                                ['url' => route('orders.index'), 'label' => __('messages.Order')],
                                ['label' => $order->orderno],
                            ]
                        ])
                    </nav>
                </div>
            </div>
        </div>
        @include('partials.alerts')
        <div class="row">
            @if (count($attentions)>0)
                <div class="col-md-4 mobile">
                    <div class="row">
                        @include('layouts.attentions')
                    </div>
                </div>
            @endif
            <div class="col-md-8">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle"><i class="fa fa-pencil"></i> @lang('messages.Edit Order')</div>
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
                                {{ dateFormat($order->created_at) }}
                            </div>
                        </div>
                    </div>
                    <form id="submitOrderHotelPromo" action="{{ route('func.order-hotel-promo.submit',$order->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="business-name">{{ $business->name }}</div>
                        <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                        <hr class="form-hr">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table tb-list">
                                    <tr>
                                        <td class="htd-1">
                                            @lang('messages.Order No')
                                        </td>
                                        <td class="htd-2">
                                            <b>{{ $order->orderno }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">
                                            @lang('messages.Order Date')
                                        </td>
                                        <td class="htd-2">
                                            {{ dateTimeFormat($order->created_at) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">
                                            @lang('messages.Service')
                                        </td>
                                        <td class="htd-2">
                                            @lang('messages.Hotel Promotion')
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">
                                            @lang('messages.Location')
                                        </td>
                                        <td class="htd-2">
                                            {{ $order->hotel->region }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                @if ($order->status == "Active")
                                    <div class="page-status" style="color: rgb(0, 156, 21)"> @lang('messages.Confirmed') <span>@lang('messages.Status'):</span></div>
                                @elseif ($order->status == "Pending")
                                    <div class="page-status" style="color: #dd9e00">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                                @elseif ($order->status == "Rejected")
                                    <div class="page-status" style="color: rgb(160, 0, 0)">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                                @else
                                    <div class="page-status" style="color: rgb(48, 48, 48)">@lang('messages.'.$order->status) <span>@lang('messages.Status'):</span></div>
                                @endif
                            </div>
                        </div>
                        {{-- ORDER --}}
                        <div class="page-subtitle">@lang('messages.Order')</div>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table tb-list">
                                        <tr>
                                            <td class="htd-1">@lang('messages.Promo')</td>
                                            <td class="htd-2">
                                                {{ $order_promotion }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="htd-1">@lang('messages.Hotel')</td>
                                            <td class="htd-2">{{ $order->hotel->name }}</td>
                                        </tr>
                                    <tr>
                                        <td class="htd-1">@lang('messages.Room')</td>
                                        <td class="htd-2">{{ $rooms }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table tb-list">
                                    <tr>
                                        <td class="htd-1">@lang('messages.Duration')</td>
                                        <td class="htd-2">{{ $order->duration." " }}@lang('messages.Nights')</td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">@lang('messages.Check In')</td>
                                        <td class="htd-2">{{ dateFormat($order->check_in_date) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">@lang('messages.Check Out')</td>
                                        <td class="htd-2">{{ dateFormat($order->check_out_date) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        {{-- SUITES AND VILLAS ============================================================================================== --}}
                        <div id="suitesAndVillas" class="page-subtitle" style="{{ !$numberOfRooms ? 'background-color: #ffe3e3; border: 2px dotted red;' : '' }}">
                            @lang('messages.Suites and Villas')
                        </div>
                        <div class="row">
                            @if ($numberOfRooms>0)
                                <div class="col-md-12">
                                    <table class="data-table table nowrap">
                                        <thead>
                                            <tr>
                                                <th>@lang('messages.Room')</th>
                                                <th>@lang('messages.Number of Guests')</th>
                                                <th>@lang('messages.Guest Name')</th>
                                                <th>@lang('messages.Price')</th>
                                                <th>@lang('messages.Extra Bed')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($roomDetails as $detail)
                                                @if ($detail['special_event_date'])
                                                    <tr data-toggle="tooltip" data-placement="top" title="{{  dateFormat($detail['special_event_date']).' '.$detail['special_event'] }}" style="background-color: #ffe695;">
                                                @else
                                                    <tr>
                                                @endif
                                                    <td>{{ $detail['room'] }}</td>
                                                    <td>{{ $detail['number_of_guests'] }}</td>
                                                    <td>{{ $detail['guest_details'] }}</td>
                                                    <td>{{ currencyFormatUsd($detail['room_price']) }}</td>
                                                    <td>{{ $detail['extra_bed_price'] ? currencyFormatUsd($detail['extra_bed_price']) : "None"; }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <div class="box-price-kicked m-b-8">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="normal-text">@lang('messages.Price/pax')</div>
                                                <div class="normal-text">@lang('messages.Number of room')</div>
                                                @if ($totalExtraBedPrice>0)
                                                    <div class="normal-text">@lang('messages.Extra Bed')</div>
                                                @endif
                                                <hr class="form-hr">
                                                <div class="subtotal-text">@lang('messages.Suites and Villas')</div>
                                            </div>
                                            <div class="col-6 text-right">
                                                <div class="text-price">{{ currencyFormatUsd($room_price) }}</div>
                                                <div class="text-price">{{ $order->total_room }}</div>
                                                @if ($totalExtraBedPrice>0)
                                                    <div class="text-price">{{ currencyFormatUsd($totalExtraBedPrice) }}</div>
                                                @endif
                                                <hr class="form-hr">
                                                <div class="subtotal-price">{{ currencyFormatUsd($totalRoomPrice) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-sm-12 m-b-18">
                                    <div class="room-container">
                                        <p style="color:brown;">
                                            <i>@lang('messages.You have not selected a room on this booking!')</i>
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if (in_array($order->status, ['Draft', 'Invalid']))
                                <div class="col-md-12 text-right">
                                    <a href="{{ route('view.order-room-hotel-promo.edit',$order->id) }}">
                                        <button type="button" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="@lang('messages.Edit')">
                                            <i class="icon-copy fa fa-pencil" aria-hidden="true"></i> 
                                            {{ $numberOfRooms>0 ? __('messages.Edit') : __('messages.Add') }}
                                        </button>
                                    </a>
                                </div>
                            @endif
                        </div>
                        {{-- ADDITIONAL CHARGE ========================================================================================================================================= --}}
                        @if (count($optionalRates)>0)
                            <div id="optional_service" class="page-subtitle">
                                @lang('messages.Additional Charge')
                            </div>
                            @if (count($order_optional_rates)>0)
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="data-table table nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="width: 10%;">@lang('messages.Date')</th>
                                                    <th style="width: 15%;">@lang('messages.Service')</th>
                                                    <th style="width: 5%;">@lang('messages.Number of Guests')</th>
                                                    <th style="width: 10%;">@lang('messages.Price')</th>
                                                    <th style="width: 10%;">@lang('messages.Total')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order_optional_rates as $order_optional_rate)
                                                    <tr>
                                                        <td>{{ dateFormat($order_optional_rate->service_date) }}</td>
                                                        <td>{{ $order_optional_rate->optional_rate->name }}</td>
                                                        <td>{{ $order_optional_rate->number_of_guest }}</td>
                                                        <td>{{ currencyFormatUsd($order_optional_rate->price_pax) }}</td>
                                                        <td>{{ currencyFormatUsd($order_optional_rate->price_total) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
            
                                        <div class="box-price-kicked m-b-8">
                                            <div class="row">
                                                <div class="col-6 col-md-6">
                                                    <div class="subtotal-text">@lang('messages.Additional Charge')</div>
                                                </div>
                                                <div class="col-6 col-md-6 text-right">
                                                    <div class="subtotal-price">{{ currencyFormatUsd($optionalServiceTotalPrice) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    @if (in_array($order->status, ['Draft', 'Invalid']))
                                        <a href="{{ route('view.order-optional-rate-hotel-promo.edit',$order->id) }}">
                                            <button type="button" class="btn btn-primary" data-toggle="tooltip" data-placement="top" 
                                                title="{{ count($order_optional_rates)>0?__('messages.Edit'):__('messages.Add') }}">
                                                @if (count($order_optional_rates)>0)
                                                    <i class='icon-copy fa fa-pencil' aria-hidden='true'></i> @lang('messages.Edit')
                                                @else
                                                    <i class='icon-copy fa fa-plus' aria-hidden='true'></i> @lang('messages.Add')
                                                @endif
                                            </button>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                        {{-- AIRPORT SHUTTLE ========================================================================================================================================= --}}
                        <div class="page-subtitle">@lang('messages.Flight and Transport Detail')</div>
                        <div class="row">
                            <!-- Arrival Flight -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="opInputArrivalFlight">@lang('messages.Arrival Flight')</label>
                                    <input type="text" id="opInputArrivalFlight" name="arrival_flight" class="form-control @error('arrival_flight') is-invalid @enderror" placeholder="@lang('messages.Arrival Flight')" value="{{ $airport_shuttle_in->flight_number ?? '' }}">
                                    @error('arrival_flight') <div class="alert alert-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <!-- Arrival Date and Time -->
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="opInputArrivalTime">@lang('messages.Arrival Date and Time')</label>
                                    <input readonly type="text" id="opInputArrivalTime" name="arrival_time" class="form-control datetimepicker @error('arrival_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ $airport_shuttle_in ? date('d F Y h:i a', strtotime($airport_shuttle_in->date)) : '' }}">
                                    @error('arrival_time') <div class="alert alert-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <!-- Airport Shuttle In -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="airportShuttleIn">@lang('messages.Airport Shuttle') <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.Request')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label>
                                    <select name="airport_shuttle_in" id="airportShuttleIn" class="custom-select @error('airport_shuttle_in') is-invalid @enderror">
                                        <option value="">@lang('messages.None')</option>
                                        @foreach ($transports as $transport)
                                            <option value="{{ $transport->id }}" 
                                                data-transportin="1" 
                                                data-transporpricein="{{ $transport->calculated_price }}"
                                                data-idtransporpricein="{{ $transport->calculated_price_id }}"
                                                {{ $airport_shuttle_in && $airport_shuttle_in->transport_id == $transport->id ? 'selected' : '' }}>
                                                {{ $transport->brand }} {{ $transport->name }} - ({{ $transport->capacity }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('airport_shuttle_in') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                                </div>
                            </div>
                            <!-- Departure Flight -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="opInputDepartureFlight">@lang('messages.Departure Flight')</label>
                                    <input type="text" id="opInputDepartureFlight" name="departure_flight" class="form-control @error('departure_flight') is-invalid @enderror" placeholder="@lang('messages.Departure Flight')" value="{{ $airport_shuttle_out->flight_number ?? '' }}">
                                    @error('departure_flight') <div class="alert alert-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <!-- Departure Date and Time -->
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="opInputDepartureTime">@lang('messages.Departure Date and Time')</label>
                                    <input readonly type="text" id="opInputDepartureTime" name="departure_time" class="form-control datetimepicker @error('departure_time') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" value="{{ $airport_shuttle_out ? date('d F Y h:i a', strtotime($airport_shuttle_out->date)) : '' }}">
                                    @error('departure_time') <div class="alert alert-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <!-- Airport Shuttle Out -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="airportShuttleOut">@lang('messages.Airport Shuttle') <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.Request')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label>
                                    <select name="airport_shuttle_out" id="airportShuttleOut" class="custom-select @error('airport_shuttle_out') is-invalid @enderror">
                                        <option value="">@lang('messages.None')</option>
                                        @foreach ($transports as $transport)
                                            <option value="{{ $transport->id }}" 
                                                data-transportout="1" 
                                                data-transporpriceout="{{ $transport->calculated_price }}"
                                                data-idtransporpriceout="{{ $transport->calculated_price_id }}"
                                                {{ $airport_shuttle_out && $airport_shuttle_out->transport_id == $transport->id ? 'selected' : '' }}>
                                                {{ $transport->brand }} {{ $transport->name }} - ({{ $transport->capacity }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('airport_shuttle_out') <span class="invalid-feedback"><strong>{{ $message }}</strong></span> @enderror
                                </div>
                            </div>
                            {{-- Total Airport Shuttle Price --}}
                            <div id="total_airport_shuttle_text" class="col-md-12">
                                <div class="box-price-kicked m-b-8">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="subtotal-text">@lang('messages.Airport Shuttle')</div>
                                        </div>
                                        <div class="col-6 text-right">
                                            <div id="total_airport_shuttle_price" class="subtotal-price">
                                                {{ $airport_shuttle_any_zero ? __('messages.To be advised') : currencyFormatUsd($total_price_airport_shuttle) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- NOTE ========================================================================================================================================= --}}
                        <div class="page-subtitle">@lang('messages.Note')</div>
                        <div class="row">
                            <div class="col-md-12">
                            <div class="form-group">
                                <textarea name="remark" class="tiny_mce form-control border-radius-0" placeholder="@lang('messages.Optional')">{{ $order->remark }}</textarea>
                                @error('remark') <div class="alert alert-danger">{{ $message }}</div> @enderror
                            </div>
                            </div>
                        </div>
                        {{-- PRICES ========================================================================================================================================= --}}
                        <div class="page-subtitle">@lang('messages.Price')</div>
                        <div class="row">
                            <div class="col-md-12 m-b-8">
                                <div class="box-price-kicked">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-6 col-md-6">
                                                    <div id="suitesAndVillasText" class="normal-text">@lang('messages.Suites and Villas')</div>
                                                    @if ($optionalServiceTotalPrice > 0)
                                                        <div class="normal-text">@lang('messages.Additional Charge')</div>
                                                    @endif
                                                    
                                                    <div id="airportShuttle" class="normal-text">@lang('messages.Airport Shuttle')</div>
                                                    
                                                    <hr class="form-hr">
                                                    <div class="total-price">@lang('messages.Total Price')</div>
                                                </div>
                                                <div class="col-6 col-md-6 text-right">
                                                    <div id="suitesAndVillasPrice" class="text-price"><span id="suitesAndVillasPriceLable">{{ currencyFormatUsd($order->room_price) }}</span></div>
                                                    @if ($optionalServiceTotalPrice > 0)
                                                        <div class="text-price"><span id="additionalChargePriceLable">{{ currencyFormatUsd($optionalServiceTotalPrice) }}</span></div>
                                                    @endif
                                                    <div id="airportShuttlePrice" class="text-price"><span id="airportShuttleText">{{ $airport_shuttle_any_zero?__('messages.To be advised'):currencyFormatUsd($total_price_airport_shuttle) }}</span></div>
                                                    <hr class="form-hr">
                                                    <div class="total-price">
                                                        <span id="finalPrice">{{ $airport_shuttle_any_zero?__('messages.To be advised'):currencyFormatUsd($order->total_price); }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 ">
                                <div class="notif-modal text-left">
                                    @if ($order->status == "Draft")
                                        @if (Auth::user()->email == "" or Auth::user()->phone == "" or Auth::user()->office == "" or Auth::user()->address == "" or Auth::user()->country == "")
                                            @lang('messages.Please complete your profile data first to be able to submit orders, by clicking this link') -> <a href="/profile">@lang('messages.Edit Profile')</a>
                                        @else
                                            @if ($order->status == "Invalid")
                                                @lang('messages.This order is invalid, please make sure all data is correct!')
                                            @else
                                                @lang('messages.Please make sure all the data is correct before you submit the order!')
                                            @endif
                                        @endif
                                    @elseif ($order->status == "Pending")
                                        @lang('messages.We have received your order, we will contact you as soon as possible to validate the order!')
                                    @elseif ($order->status == "Rejected")
                                        {!! $order->msg !!}
                                    @elseif ($order->status == "Invalid")
                                        {!! $order->msg !!}
                                    @endif
                                </div>
                            </div>
                            <input type="hidden" name="total_price_optional_rate" id="totalPriceOptionalRate" value="{{ $optionalServiceTotalPrice }}">
                            <input type="hidden" name="total_price_suites_and_villas" id="totalPriceSuitesAndVillas" value="{{ $order->room_price }}">
                            <input type="hidden" name="total_price_airport_shuttle" id="totalPriceAirportShuttle" value="{{ $total_price_airport_shuttle }}">
                            <input type="hidden" name="airport_shuttle_in_price" id="airportShuttleInPrice" value="{{ $airport_shuttle_in ? $airport_shuttle_in->price : 0 }}">
                            <input type="hidden" name="airport_shuttle_out_price" id="airportShuttleOutPrice" value="{{ $airport_shuttle_out ? $airport_shuttle_out->price : 0 }}">
                            <input type="hidden" name="transport_in_price_id" id="transportInPriceId" value="">
                            <input type="hidden" name="transport_out_price_id" id="transportOutPriceId" value="">
                        </div>
                    </Form>
                    <div class="card-box-footer">
                        @if (in_array($order->status, ['Draft', 'Invalid']))
                            @if ($order->total_room == "" or $order->total_guests == "")
                                <button type="button" class="btn btn-light"><i class="icon-copy fa fa-info" aria-hidden="true"> </i> @lang('messages.You cannot submit this order')</button>
                            @else
                                @if ($order->status != "Invalid")
                                    @if (Auth::user()->email == "" or Auth::user()->phone == "" or Auth::user()->office == "" or Auth::user()->address == "" or Auth::user()->country == "")
                                        <button disabled type="submit" form="submitOrderHotelPromo" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Submit')</button>
                                    @else
                                        <button type="submit" form="submitOrderHotelPromo" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Submit')</button>
                                    @endif
                                @else
                                    <button type="submit" form="submitOrderHotelPromo" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Submit')</button>
                                @endif
                            @endif
                            <a href="/orders">
                                <button class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                            </a>
                        @elseif ($order->status == "Rejected")
                            <form id="removeOrder" class="hidden" action="/fremove-order/{{ $order->id }}"method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <input type="hidden" name="status" value="Removed">
                                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                            </form>
                            <button type="submit" form="removeOrder" class="btn btn-danger"><i class="icon-copy fa fa-trash-o" aria-hidden="true"></i> @lang('messages.Delete')</button>
                        @else
                            <div class="form-group">
                                <a href="/orders">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            @if (count($attentions)>0)
                <div class="col-md-4 desktop">
                    <div class="row">
                        @include('layouts.attentions')
                    </div>
                </div>
            @endif
        </div>
        @include('layouts.footer')
    </div>
</div>
@include('partials.loading-form', ['id' => 'submitOrderHotelPromo'])
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const formatCurrency = amount => new Intl.NumberFormat('en-US', { 
            style: 'currency', 
            currency: 'USD',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
        const transportIn = document.querySelector("#airportShuttleIn");
        const transportOut = document.querySelector("#airportShuttleOut");
        const totalPriceElement = document.querySelector("#total_airport_shuttle_price");
        const totalShuttleText = document.querySelector("#total_airport_shuttle_text");
        const textAirportShuttle = document.querySelector("#airportShuttle");
        const hiddenTotalInput = document.querySelector("#totalPriceAirportShuttle");
        const inputAirportShuttleInPrice = document.querySelector("#airportShuttleInPrice");
        const inputAirportShuttleOutPrice = document.querySelector("#airportShuttleOutPrice");
        const totalAirportShuttleOutPrice = document.querySelector("#airportShuttleText");
        const inputAirportShuttleInPriceId = document.querySelector("#transportInPriceId");
        const inputAirportShuttleOutPriceId = document.querySelector("#transportOutPriceId");
        const finalPriceValue = document.querySelector("#finalPrice");
        const priceOptionalRate = document.querySelector("#totalPriceOptionalRate");
        const priceSuitesAndVillas = document.querySelector("#totalPriceSuitesAndVillas");
        function updateTotalPrice() {
            const valueIn = transportIn.value.trim();
            const valueOut = transportOut.value.trim();
            const totalPriceOptionalRate = parseFloat(priceOptionalRate.value) || 0;
            const totalPriceSuitesAndVillas = parseFloat(priceSuitesAndVillas.value) || 0;
            const priceIn = parseFloat(transportIn.selectedOptions[0]?.dataset.transporpricein) || 0;
            const priceInId = parseFloat(transportIn.selectedOptions[0]?.dataset.idtransporpricein) || 0;
            const priceOut = parseFloat(transportOut.selectedOptions[0]?.dataset.transporpriceout) || 0;
            const priceOutId = parseFloat(transportOut.selectedOptions[0]?.dataset.idtransporpriceout) || 0;
            const totalAirportShuttlePrice = priceIn + priceOut;
            const finalPriceOrder = totalPriceOptionalRate + totalPriceSuitesAndVillas + totalAirportShuttlePrice;
            if (!valueIn && !valueOut) {
                [totalShuttleText, totalAirportShuttleOutPrice, textAirportShuttle].forEach(el => el.style.display = "none");
                hiddenTotalInput.value = "";
                inputAirportShuttleInPrice.value = "";
                inputAirportShuttleOutPrice.value = "";
                inputAirportShuttleInPriceId.value = "";
                inputAirportShuttleOutPriceId.value = "";
                finalPriceValue.textContent = formatCurrency(finalPriceOrder);
                return;
            }
            [totalShuttleText, totalAirportShuttleOutPrice, textAirportShuttle].forEach(el => el.style.display = "block");
            if ((priceIn === 0 && valueIn) || (priceOut === 0 && valueOut)) {
                totalPriceElement.textContent = totalAirportShuttleOutPrice.textContent = finalPriceValue.textContent = "{{ __('messages.To be advised') }}";
                inputAirportShuttleInPrice.value = priceIn;
                inputAirportShuttleOutPrice.value = priceOut;
                hiddenTotalInput.value = totalAirportShuttlePrice;
                inputAirportShuttleInPriceId.value = priceInId;
                inputAirportShuttleOutPriceId.value = priceOutId;
            } else {
                const formattedTotal = formatCurrency(totalAirportShuttlePrice);
                totalPriceElement.textContent = totalAirportShuttleOutPrice.textContent = formattedTotal;
                hiddenTotalInput.value = totalAirportShuttlePrice;
                inputAirportShuttleInPriceId.value = priceInId;
                inputAirportShuttleOutPriceId.value = priceOutId;
                inputAirportShuttleInPrice.value = priceIn;
                inputAirportShuttleOutPrice.value = priceOut;
                finalPriceValue.textContent = formatCurrency(finalPriceOrder);
            }
        }
        document.addEventListener("change", function (event) {
            if (event.target === transportIn || event.target === transportOut) {
                updateTotalPrice();
            }
        });
        updateTotalPrice();
    });
</script>
@endsection