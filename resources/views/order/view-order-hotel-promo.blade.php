@section('title',__('Edit Order'))
@extends('layouts.head')
@section('content')
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <i class="icon-copy fa fa-tags"></i>&nbsp; 
                            @lang('messages.Detail Order')
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
                        </div>
                        {{-- ADDITIONAL CHARGE --}}
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
                        @endif
                        {{-- AIRPORT SHUTTLE --}}
                        @if (count($airport_shuttles)>0)
                            <div class="page-subtitle">@lang('messages.Flight and Transport Detail')</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="data-table table nowrap">
                                        <thead>
                                            <tr>
                                                <th>@lang('messages.Date')</th>
                                                <th>@lang('messages.Flight')</th>
                                                <th>@lang('messages.Type')</th>
                                                <th>@lang('messages.Transport')</th>
                                                <th>@lang('messages.Price')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($airport_shuttles as $airport_shuttle)
                                                <tr>
                                                    <td>{{ $airport_shuttle->date ? dateTimeFormat($airport_shuttle->date) : "-" }}</td>
                                                    <td>{{ $airport_shuttle->flight_number ? $airport_shuttle->flight_number : "-" }}</td>
                                                    <td>{{ $airport_shuttle->nav === "In" ? __('messages.Arrival') : __('messages.Departure') }}</td>
                                                    <td>{{ $airport_shuttle->transport->brand." ".$airport_shuttle->transport->name }}</td>
                                                    <td>{{ $airport_shuttle->price > 0 ? currencyFormatUsd($airport_shuttle->price) : __('messages.To be advised') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
                        @endif
                        {{-- NOTE ========================================================================================================================================= --}}
                        @if ($order->remark)
                            <div class="page-subtitle">@lang('messages.Note')</div>
                            <div class="row">
                                <div class="col-md-12">
                                    {!! $order->remark !!}
                                </div>
                            </div>
                        @endif
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
                        </div>
                        <div class="card-box-footer">
                            <div class="form-group">
                                <a href="/orders">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                </a>
                            </div>
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
@endsection