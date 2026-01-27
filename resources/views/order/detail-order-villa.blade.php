@section('title',__('Detail Order'))
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
                                    ['url' => route('view.orders'), 'label' => __('messages.Order')],
                                    ['label' => $order->orderno],
                                ]
                            ])
                        </nav>
                    </div>
                </div>
            </div>
            @include('partials.alerts')
            <div class="row">
                @if (count($attentions)>0 || $order->status == "Approved" || $order->status == "Paid" || !is_null($receipts) || $invoice)
                    <div class="col-md-4 mobile">
                        <div class="row">
                            @include('layouts.attentions')
                            <div class="col-md-12">
                                <div class="card-box">
                                    @if ($order->status == "Approved" || $order->status == "Paid")
                                        <div class="card-box-title">
                                            <div class="subtitle"><i class="icon-copy fa fa-money" aria-hidden="true"></i> @lang('messages.Payment Status')</div>
                                        </div>
                                        @if (!is_null($receipts))
                                            @foreach ($receipts as $receipt)
                                                @if ($receipt->status == "Valid")
                                                    <div class="receipt-container">
                                                        <div class="receipt-status">
                                                            <i class="icon-copy fa fa-check-circle color-green" aria-hidden="true"></i>
                                                            <div class="pmt-status">
                                                                @lang('messages.Paid') ({{ currencyFormatUsd($receipt->amount) }})
                                                            </div>
                                                        </div>
                                                        <div class="receipt-description">
                                                            <b>{{ $invoice->inv_no }}</b>
                                                            <p>@lang('messages.Paid on') : {{ dateFormat($receipt->payment_date) }}<br>
                                                        </div>
                                                        <div class="receipt-action">
                                                            <a class="action-btn" href="#" data-toggle="modal" data-target="#paid-receipt-mobile-{{ $receipt->id }}">
                                                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" id="paid-receipt-mobile-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content modal-receipt">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                                                    </div>
                                                                    <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                                                    <div class="card-box-footer">
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif($receipt->status == "Pending")
                                                    <div class="receipt-container">
                                                        <div class="receipt-status">
                                                            <i class="icon-copy fa fa-clock-o" aria-hidden="true"></i>
                                                            <div class="pmt-status">
                                                                @lang('messages.On Review')
                                                            </div>
                                                        </div>
                                                        <div class="receipt-description">
                                                            <b>{{ $invoice->inv_no }}</b>
                                                            <i>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</i>
                                                            <i>@lang('messages.Payment Confirmation') : {{ dateFormat($receipt->created_at) }}</i>
                                                        </div>
                                                    </div>
                                                @elseif($receipt->status == "Invalid")
                                                    <div class="receipt-container">
                                                        <div class="receipt-status">
                                                            <i class="icon-copy fa fa-window-close color-red" aria-hidden="true"></i>
                                                            <div class="pmt-status">
                                                                @lang('messages.Invalid')
                                                            </div>
                                                        </div>
                                                        <div class="receipt-description">
                                                            <p><i style="color: red">{{ $receipt->note }}</i></p>
                                                            <b>{{ $invoice->inv_no }}</b>
                                                            <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                                                        </div>
                                                        <div class="receipt-action">
                                                            <a class="action-btn" href="#" data-toggle="modal" data-target="#invalid-receipt-mobile-{{ $receipt->id }}">
                                                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" id="invalid-receipt-mobile-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content modal-receipt">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                                                    </div>
                                                                    <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                                                    <div class="notification-text" style="margin-top: 8px; color:rgb(143, 0, 0);">
                                                                        {!! $receipt->note !!}
                                                                    </div>
                                                                    <div class="card-box-footer">
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="receipt-container">
                                                        <div class="receipt-status">
                                                            <i class="icon-copy fa fa-clock-o" aria-hidden="true"></i>
                                                            <div class="pmt-status">
                                                                @lang('messages.On Review')
                                                            </div>
                                                        </div>
                                                        <div class="receipt-description">
                                                            <b>{{ $invoice->inv_no }}</b>
                                                            <i>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</i>
                                                            <i>@lang('messages.Payment Confirmation') : {{ dateFormat($receipt->created_at) }}</i>
                                                        </div>
                                                        <div class="receipt-action">
                                                            <a class="action-btn" href="#" data-toggle="modal" data-target="#payment-receipt-mobile-{{ $receipt->id }}">
                                                                <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" id="payment-receipt-mobile-{{ $receipt->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content modal-receipt">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="title"><i class="icon-copy fa fa-file-photo-o" aria-hidden="true"></i> @lang('messages.Payment Receipt')</div>
                                                                    </div>
                                                                    <img src="{{ asset('storage/receipt/'.$receipt->receipt_img) }}" alt="">
                                                                    <div class="notification-text" style="margin-top: 8px; color:rgb(143, 0, 0);">
                                                                        {!! $receipt->note !!}
                                                                    </div>
                                                                    <div class="card-box-footer">
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            @if ($doku_payment && $order->status == "Paid")
                                                <div class="receipt-status">
                                                    <i class="icon-copy fa fa-check-circle color-green" aria-hidden="true"></i> @lang('messages.Paid')
                                                </div>
                                                <div class="receipt-description">
                                                    <b>{{ $invoice->inv_no }}</b>
                                                    <p>@lang('messages.Paid on') : {{ dateTimeFormat($doku_payment->payment_date) }}<br>
                                                    <p>@lang('messages.Payment') : {{ $doku_payment->payment_method." ".$doku_payment->provider }}<br>
                                                    <p>@lang('messages.Amount') : {{ currencyFormatIdr($doku_payment->amount) }}<br>
                                                </div>
                                            @else
                                                <div class="pmt-container pending">
                                                    <i class="icon-copy fa fa-hourglass" aria-hidden="true"></i>
                                                    <div class="pmt-status">
                                                        @lang('messages.Waiting Payment')
                                                    </div>
                                                </div>
                                                @if ($invoice)
                                                    <div class="pmt-des">
                                                        <b>{{ $invoice->inv_no }}</b>
                                                        <p>@lang('messages.Payment Dateline') : {{ dateFormat($invoice->due_date) }}</p>
                                                    </div>
                                                @endif
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-md-8">
                    <div class="card-box">
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
                                            @lang('messages.'.$order->service)
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">
                                            @lang('messages.Region')
                                        </td>
                                        <td class="htd-2">
                                            {{ $order->location }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div class="status-order-container">
                                    <div class="status-order-lable">
                                        @lang('messages.Status'):
                                    </div>
                                    <div class="status-order">
                                        @if (isset($statusMap[$order->status]))
                                            <div class="{{ $statusMap[$order->status]['class'] }}">
                                                {!! $statusMap[$order->status]['label'] !!}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- ORDER --}}
                        <div class="page-subtitle">@lang('messages.Order Details')</div>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table tb-list nowrap">
                                    <tbody>
                                        <tr>
                                            <td class="htd-1">
                                                @lang('messages.Villa')
                                            </td>
                                            <td class="htd-2">
                                                {{ $villa->name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="htd-1">
                                                @lang('messages.Number of Room')
                                            </td>
                                            <td class="htd-2">
                                                {{ count($rooms)>1? count($rooms)." ".__('messages.rooms') : count($rooms)." ".__('messages.room') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="htd-1">
                                                @lang('messages.Occupancy')
                                            </td>
                                            <td class="htd-2">
                                                {{ $occupancy_adult }} {{ $occupancy_adult>1 ? __('messages.adults') : __('messages.adult') }} + {{ $occupancy_children }} {{ $occupancy_children>1 ? __('messages.children') : __('messages.child') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="htd-1">
                                                @lang('messages.Number of Guests')
                                            </td>
                                            <td class="htd-2">
                                                {{ $number_of_adult }} {{ $number_of_adult>1 ? __('messages.adults') : __('messages.adult') }} + {{ $number_of_children }} {{ $number_of_children>1 ? __('messages.children') : __('messages.child') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                <table class="table tb-list nowrap">
                                    <tbody>
                                        <tr>
                                            <td class="htd-1">
                                                @lang('messages.Duration')
                                            </td>
                                            <td class="htd-2">
                                                {{ $order->duration > 1?$order->duration." ".__('messages.nights'):$order->duration." ".__('messages.night') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="htd-1">
                                                @lang('messages.Check In')
                                            </td>
                                            <td class="htd-2">
                                                {{ date('d F Y',strtotime($order->checkin)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="htd-1">
                                                @lang('messages.Check Out')
                                            </td>
                                            <td class="htd-2">
                                                {{ date('d F Y',strtotime($order->checkout)) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- ADDITIONAL INFORMATION ============================================================================================= --}}
                        @foreach([
                                'benefits' => 'messages.Benefit',
                                'include' => 'messages.Include',
                                'additional_info' => 'messages.Additional Information',
                                'cancellation_policy' => 'messages.Cancelation Policy',
                            ] as $key => $label)
                            @if (!empty($order->$key))
                                <div class="page-text">
                                    <hr class="form-hr">
                                    <b>@lang($label) :</b> <br>
                                    {!! $order->$key !!}
                                </div>
                            @endif
                        @endforeach
                        {{-- GUESTS DETAILS --}}
                        <div class="page-subtitle">@lang('messages.Guest Details')</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="data-table stripe table hover nowrap">
                                    <thead>
                                        <tr>
                                            <th class="table-plus datatable-nosort" style="width: 10%">@lang('messages.No')</th>
                                            <th>@lang('messages.Name')</th>
                                            <th class="table-plus datatable-nosort">-</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($guests as $no_guest=>$guest)
                                            <tr>
                                                <td class="table-plus">{{ ++$no_guest }}</td>
                                                <td>{{ $guest->name }}</td>
                                                <td>{{ $guest->age }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6">Data tamu tidak tersedia</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- ADDITIONAL CHARGE --}}
                        @if (count($optional_rate_orders)>0)
                            <div id="optional_service" class="page-subtitle">
                                @lang('messages.Additional Charge')
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="data-table table nowrap">
                                        <thead>
                                            <tr>
                                                <th style="width: 10%;">@lang('messages.Date')</th>
                                                <th style="width: 15%;">@lang('messages.Service')</th>
                                                <th style="width: 5%;">@lang('messages.Guests')</th>
                                                <th style="width: 10%;">@lang('messages.Price')</th>
                                                <th style="width: 10%;">@lang('messages.Total')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($optional_rate_orders as $order_optional_rate)
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
                        {{-- ADDITIONAL SERVICES --}}
                        @if ($additional_service_total_price>0)
                            <div id="optional_service" class="page-subtitle">
                                @lang('messages.Additional Services')
                            </div>
                            <div class="row" id="additionalServices">
                                <div class="col-md-12">
                                    <table class="data-table table nowrap">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%;">@lang('messages.Date')</th>
                                                <th style="width: 40%;">@lang('messages.Service')</th>
                                                <th style="width: 10%;">@lang('messages.Quantity')</th>
                                                <th style="width: 10%;">@lang('messages.Price')</th>
                                                <th style="width: 10%;">@lang('messages.Total')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($additionalServices as $service)
                                                <tr>
                                                    <td>{{ $service['date'] }}</td>
                                                    <td>{{ $service['service'] }}</td>
                                                    <td>{{ $service['qty'] }}</td>
                                                    <td>{{ "$ ".$service['price'] }}</td>
                                                    <td>{{ "$ ".$service['total'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="box-price-kicked m-b-8">
                                        <div class="row">
                                            <div class="col-6 col-md-6">
                                                <div class="subtotal-text"> @lang('messages.Additional Service')</div>
                                            </div>
                                            <div class="col-6 col-md-6 text-right">
                                                <div class="subtotal-price">{{ currencyFormatUsd($additional_service_total_price) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{-- AIRPORT SHUTTLE --}}
                        @if (count($airport_shuttles)>0)
                            <div class="page-subtitle">@lang('messages.Airport Shuttle')</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="data-table stripe table nowrap">
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
                                                    <td>{{ $airport_shuttle->nav == "In" ? __('messages.Arrival') : __('messages.Departure') }}</td>
                                                    <td>{{ $airport_shuttle->transport->brand." ".$airport_shuttle->transport->name }}</td>
                                                    <td>{{ $airport_shuttle_any_zero ? __('messages.To be advised') : currencyFormatUsd($airport_shuttle->price) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
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
                        {{-- NOTE ======================================================================================================================================== --}}
                        @if ($order->note)
                            <div class="page-subtitle">@lang('messages.Note')</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="page-text">
                                        {!! $order->note !!}
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{-- PRICES ======================================================================================================================================== --}}
                        <div class="page-subtitle">@lang('messages.Price')</div>
                        <div class="row">
                            <div class="col-md-12 m-b-8">
                                <div class="box-price-kicked">
                                    <div class="row">
                                        @uiEnabled('doku-payment')
                                            @if ($doku_payment)
                                                <div class="col-12 col-md-12">
                                                    <b>@lang('messages.Self Payment')</b>
                                                    <hr class="form-hr">
                                                </div>
                                            @endif
                                        @endUiEnabled
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-6 col-md-6">
                                                    <div id="suitesAndVillasText" class="normal-text">@lang('messages.Private Villa')</div>
                                                    @if ($optionalServiceTotalPrice > 0)
                                                        <div class="normal-text">@lang('messages.Additional Charge')</div>
                                                    @endif
                                                    @if ($additional_service_total_price > 0)
                                                        <div class="normal-text">@lang('messages.Additional Service')</div>
                                                    @endif
                                                    @if ($total_price_airport_shuttle > 0)
                                                        <div id="airportShuttle" class="normal-text">@lang('messages.Airport Shuttle')</div>
                                                    @endif
                                                    <hr class="form-hr">
                                                    @if (!empty($filteredDiscounts))
                                                        @foreach ($filteredDiscounts as $discountName => $value)
                                                            <div class="normal-text">@lang("messages.$discountName")</div>
                                                        @endforeach
                                                        <hr class="form-hr">
                                                    @endif
                                                    @if ($invoice)
                                                        @if ($invoice->currency_id == 1)
                                                            <div class="price-name">@lang('messages.Total Price') USD</div>
                                                        @elseif ($invoice->currency_id == 2)
                                                            <div class="normal-text">@lang('messages.Total Price') USD</div>
                                                            <div class="price-name">@lang('messages.Total Price') CNY</div>
                                                        @elseif ($invoice->currency_id == 3)
                                                            <div class="normal-text">@lang('messages.Total Price') USD</div>
                                                            <div class="price-name">@lang('messages.Total Price') TWD</div>
                                                        @else
                                                            <div class="price-name">@lang('messages.Total Price') IDR</div>
                                                        @endif
                                                    @else
                                                        <div class="price-name">@lang('messages.Total Price') USD</div>
                                                    @endif
                                                </div>
                                                <div class="col-6 col-md-6 text-right">
                                                    @if ($order->request_quotation == "Yes")
                                                        <div id="suitesAndVillasPrice" class="text-price"><span id="suitesAndVillasPriceLable">@lang('messages.To be advised')</span></div>
                                                    @else
                                                        <div id="suitesAndVillasPrice" class="text-price"><span id="suitesAndVillasPriceLable">{{ currencyFormatUsd($order->normal_price) }}</span></div>
                                                    @endif
                                                    @if ($optionalServiceTotalPrice > 0)
                                                        <div class="text-price"><span id="additionalChargePriceLable">{{ currencyFormatUsd($optionalServiceTotalPrice) }}</span></div>
                                                    @endif
                                                    @if ($additional_service_total_price > 0)
                                                        <div class="text-price"><span id="additionalServiceTotalPrice">{{ currencyFormatUsd($additional_service_total_price) }}</span></div>
                                                    @endif
                                                    @if ($total_price_airport_shuttle > 0)
                                                        <div id="airportShuttlePrice" class="text-price"><span id="airportShuttleText">{{ $airport_shuttle_any_zero?__('messages.To be advised'):currencyFormatUsd($total_price_airport_shuttle) }}</span></div>
                                                    @endif
                                                    <hr class="form-hr">
                                                    @if (!empty($filteredDiscounts))
                                                        @foreach ($filteredDiscounts as $label => $value)
                                                            <div class="normal-text">
                                                                @if ($value !== true)<div class="promo-text">{{ currencyFormatUsd($value) }}</div> @endif
                                                            </div>
                                                        @endforeach
                                                        <hr class="form-hr">
                                                    @endif
                                                    <div class="total-price">
                                                        @if ($order->request_quotation == "Yes")
                                                            <span>@lang('messages.To be advised')</span>
                                                        @else
                                                            @if ($invoice)
                                                                @if ($invoice->currency_id == 1)
                                                                    <div class="usd-rate">{{ $airport_shuttle_any_zero?__('messages.To be advised'):currencyFormatUsd($order->final_price); }}</div>
                                                                @elseif ($invoice->currency_id == 2)
                                                                    <div class="normal-text">{{ currencyFormatUsd($order->final_price) }}</div>
                                                                    <div class="usd-rate">{{ $airport_shuttle_any_zero?__('messages.To be advised'):currencyFormatCny($invoice->total_cny); }}</div>
                                                                @elseif ($invoice->currency_id == 3)
                                                                    <div class="normal-text">{{ currencyFormatUsd($order->final_price) }}</div>
                                                                    <div class="usd-rate">{{ $airport_shuttle_any_zero?__('messages.To be advised'):currencyFormatTwd($invoice->total_twd); }}</div>
                                                                @else
                                                                    <div class="usd-rate">{{ $airport_shuttle_any_zero?__('messages.To be advised'):currencyFormatIdr($order->final_price); }}</div>
                                                                @endif
                                                            @else
                                                                <div class="usd-rate">{{ $airport_shuttle_any_zero?__('messages.To be advised'):currencyFormatUsd($order->final_price); }}</div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- DOKU PAYMENT --}}
                            @uiEnabled('doku-payment')
                                @if ($doku_payment)
                                    <div class="col-md-12">
                                        <div class="box-price-kicked">
                                            <div class="row">
                                                <div class="col-12 col-md-12">
                                                    <b>@lang('messages.Pay with DOKU')</b>
                                                    <hr class="form-hr">
                                                </div>
                                                <div class="col-6 col-md-6">
                                                    <div class="normal-text">@lang('messages.Total') IDR</div>
                                                    <div class="normal-text">@lang('messages.Payment Fee')</div>
                                                    <hr class="form-hr">
                                                    <div class="price-name">@lang('messages.Total Price') IDR</div>
                                                </div>
                                                <div class="col-6 col-md-6 text-right">
                                                    <div class="normal-text">{{ currencyFormatIdr($total_price_idr) }}</div>
                                                    <div class="normal-text">{{ currencyFormatIdr($tax_doku) }}</div>
                                                    <hr class="form-hr">
                                                    <div class="usd-rate">{{ currencyFormatIdr($doku_total_price) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endUiEnabled
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
                        <span id="response"></span>
                        <div class="card-box-footer">
                            {{-- INVOICE --}}
                            @if ($invoice && $order->status != "Paid")
                                @uiEnabled('doku-payment')
                                    @if ($order->status == "Approved" && $doku_payment)
                                        @if ($doku_payment->expired_date > $now)
                                            <button id="checkout-button" class="btn btn-img-doku">
                                                <img src="{{ asset('vendors/DOKU/DOKU Payment.png') }}" alt="Logo DOKU">
                                                Pay with DOKU
                                            </button>
                                            <script type="text/javascript">
                                                var checkoutButton = document.getElementById('checkout-button');
                                                var paymentUrl = @json($doku_payment->checkout_url);
                                                checkoutButton.addEventListener('click', function () {
                                                    loadJokulCheckout(paymentUrl);
                                                });
                                            </script>
                                        @endif
                                    @endif
                                @endUiEnabled
                                <button type="button" class="btn btn-primary desktop" data-toggle="modal" data-target="#payment-confirmation-{{ $order->id }}">
                                    <i class="icon-copy fa fa-upload" aria-hidden="true"></i> @lang('messages.Payment Confirmation')
                                </button>
                                {{-- MODAL PAYMENT CONFIRMATION --}}
                                <div class="modal fade" id="payment-confirmation-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card-box">
                                                <div class="card-box-title text-left">
                                                    <div class="title"><i class="icon-copy fa fa-usd" aria-hidden="true"></i>@lang('messages.Payment Confirmation')</div>
                                                </div>
                                                <form id="payment-confirm-{{ $order->id }}" action="/fpayment-confirmation-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="row text-left">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="row m-t-27">
                                                                        <div class="col-5"><p>@lang('messages.Order Number')</p></div>
                                                                        <div class="col-7"><p><b>: {{ $order->orderno }}</b></p></div>
                                                                        <div class="col-5"><p>@lang('messages.Reservation Number')</p></div>
                                                                        <div class="col-7"><p><b>: {{ $reservation->rsv_no }}</b></p></div>
                                                                        <div class="col-5"><p>@lang('messages.Invoice Number')</p></div>
                                                                        <div class="col-7"><p><b>: {{ $invoice->inv_no }}</b></p></div>
                                                                        <div class="col-5"><p>@lang('messages.Due Date')</p></div>
                                                                        <div class="col-7"><p>: {{ dateFormat($invoice->due_date) }}</p></div>
                                                                        <div class="col-5"><p>@lang('messages.Amount')</p></div>
                                                                        <div class="col-7"><p><b>: {{ currencyFormatUsd($order->final_price) }}</b></p></div>
                                                                        <div class="col-12 m-t-18"><p><i class="icon-copy fa fa-exclamation" aria-hidden="true"></i> @lang('messages.Please make the payment before the due date and provide proof of payment to prevent the cancellation of your order.')</p></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="cover" class="form-label">@lang('messages.Receipt Image')</label>
                                                                        <div class="dropzone">
                                                                            <div class="tour-receipt-div">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="receipt_name" class="form-label">@lang('messages.Select Receipt') </label><br>
                                                                        <input type="file" name="receipt_name" id="receipt_name" class="custom-file-input @error('receipt_name') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('receipt_name') }}" required>
                                                                        @error('receipt_name')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                    </div>
                                                </form>
                                                <div class="card-box-footer">
                                                    <button type="submit" form="payment-confirm-{{ $order->id }}" class="btn btn-primary"><i class="icon-copy fa fa-upload" aria-hidden="true"></i> @lang('messages.Send')</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($order->status == "Paid")
                                @if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_en.pdf"))
                                    <a href="#" data-toggle="modal" data-target="#contract-en-{{ $order->id }}">
                                        <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> Invoice EN</button>
                                    </a>
                                    {{-- MODAL VIEW CONTRACT EN --}}
                                    <div class="modal fade" id="contract-en-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                <div class="modal-body pd-5">
                                                    <embed src="/storage/document/invoice-{{ $inv_no."-".$order->id }}_en.pdf" frameborder="10" width="100%" height="850px">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (File::exists("storage/document/invoice-".$inv_no."-".$order->id."_zh.pdf"))
                                    <a href="#" data-toggle="modal" data-target="#contract-zh-{{ $order->id }}">
                                        <button type="button" class="btn btn-primary desktop"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> Invoice ZH</button>
                                    </a>
                                    {{-- MODAL VIEW CONTRACT ZH --}}
                                    <div class="modal fade" id="contract-zh-{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                <div class="modal-body pd-5">
                                                    <embed src="/storage/document/invoice-{{ $inv_no."-".$order->id }}_zh.pdf" frameborder="10" width="100%" height="850px">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                            @endif
                            <div class="form-group">
                                <a href="/orders#orderHotel">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                </a>
                            </div>
                            
                        </div>
                    </div>
                </div>
                @if (count($attentions)>0 || !is_null($receipts) || $invoice)
                    <div class="col-md-4 desktop">
                        <div class="row">
                            @include('layouts.attentions')
                            @include('partials.user-order-payment-status',['device'=>"desktop"])
                        </div>
                    </div>
                @endif
                
            </div>
            @include('layouts.footer')
        </div>
    </div>
@endsection