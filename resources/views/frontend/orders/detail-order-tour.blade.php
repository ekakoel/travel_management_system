@section('title', __('messages.Orders'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title"><i class="icon-copy dw dw-eye"></i>&nbsp; @lang('messages.Order Details')</div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/dashboard">@lang('messages.Dashboard')</a></li>
                                    <li class="breadcrumb-item"><a href="/orders">Orders</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ $order->orderno }}</li>
                                </ol>
                            </nav>
                    </div>
                    </div>
                </div>
                <div class="row">
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
                        @if (\Session::has('success'))
                            <div class="alert alert-success">
                                <ul>
                                    <li>{!! \Session::get('success') !!}</li>
                                </ul>
                            </div>
                        @endif
                    </div>
                    @if (Auth::user()->email == "")
                        <div class="col-xl-12 mb-30">
                            <div class="card-box">
                                <div class="notification-box">
                                    <i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i>
                                    Please complete your profile data first to be able to submit orders by clicking this link -> <a href="/profile">Edit Profile</a></i>
                                </div>
                            </div>
                        </div>
                    @elseif (Auth::user()->phone == "")
                        <div class="col-xl-12 mb-30">
                            <div class="card-box">
                                <div class="notification-box">
                                    <i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i>
                                    Please complete your profile data first to be able to submit orders by clicking this link -> <a href="/profile">Edit Profile</a></i>
                                </div>
                            </div>
                        </div>
                    @elseif (Auth::user()->office == "")
                        <div class="col-xl-12 mb-30">
                            <div class="card-box">
                                <div class="notification-box">
                                    <i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i>
                                    Please complete your profile data first to be able to submit orders by clicking this link -> <a href="/profile">Edit Profile</a></i>
                                </div>
                            </div>
                        </div>
                    @elseif (Auth::user()->address == "")
                        <div class="col-xl-12 mb-30">
                            <div class="card-box">
                                <div class="notification-box">
                                    <i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i>
                                    Please complete your profile data first to be able to submit orders by clicking this link -> <a href="/profile">Edit Profile</a></i>
                                </div>
                            </div>
                        </div>
                    @elseif (Auth::user()->country == "")
                        <div class="col-xl-12 mb-30">
                            <div class="card-box">
                                <div class="notification-box">
                                    <i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i>
                                    Please complete your profile data first to be able to submit orders by clicking this link -> <a href="/profile">Edit Profile</a></i>
                                </div>
                            </div>
                        </div>
                    @else
                    @endif
                </div>
                <div class="product-wrap">
                    <div class="product-detail-wrap">
                        <div class="row">
                            @if (count($attentions)>0 || !is_null($receipts) || $invoice)
                                <div class="col-md-4 mobile">
                                    <div class="row">
                                        @include('layouts.attentions')
                                        @include('partials.user-order-payment-status',['device'=>"mobile"])
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-8">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <div class="subtitle">@lang('messages.Order Details')</div>
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
                                                        {{ dateFormat($order->created_at) }}
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
                                    {{-- ORDER  --}}
                                    <div class="page-subtitle">@lang('messages.Order')</div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table tb-list">
                                                <tr>
                                                    <td class="htd-1">
                                                        @lang('messages.Tour Package')
                                                    </td>
                                                    <td class="htd-2">
                                                        {{ $tour->$langName }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">
                                                        @lang('messages.Type')
                                                    </td>
                                                    <td class="htd-2">
                                                        {{ $tour->type?->$langType }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">
                                                        @lang('messages.Tour Area')
                                                    </td>
                                                    <td class="htd-2">
                                                        {{ $tour->$langArea }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">
                                                        @lang('messages.Number of Guests')
                                                    </td>
                                                    <td class="htd-2">
                                                        {{ $order->number_of_guests." guests" }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table tb-list">
                                                <tr>
                                                    <td class="htd-1">
                                                        @lang('messages.Duration')
                                                    </td>
                                                    <td class="htd-2">
                                                        {{ $tour->duration_days."D" }}{{ $tour->duration_nights>0? " / ".$tour->duration_nights."N":"" }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">
                                                        @lang('messages.Tour Start')
                                                    </td>
                                                    <td class="htd-2">
                                                        {{ dateFormat($order->checkin) }}
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">
                                                        @lang('messages.Tour End')
                                                    </td>
                                                    <td class="htd-2">
                                                        {{ dateFormat($order->checkout) }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">
                                                        @lang('messages.Pickup Location')
                                                    </td>
                                                    <td class="htd-2">
                                                        {{ $order->pickup_location }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="htd-1">
                                                        @lang('messages.Dropoff Location')
                                                    </td>
                                                    <td class="htd-2">
                                                        {{ $order->dropoff_location }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    {{-- ADDITIONAL SERVICES --}}
                                    @if ($order->additional_service_total_price>0)
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
                                                            <div class="subtotal-price">{{ currencyFormatUsd($order->additional_service_total_price) }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="page-subtitle">@lang('messages.Guest Details')</div>
                                    <div class="row">
                                        <div class="col-md-12 p-l-27">
                                            <div class="p-l-8">
                                                {!! $order->guest_detail !!}
                                            </div>
                                        </div>
                                    </div>
                                    @if ($tour->itinerary != "")
                                        <div class="page-subtitle">@lang('messages.Itinerary')</div>
                                        <div class="page-text">
                                            {!! $tour->$langItinerary !!}
                                        </div>
                                    @endif
                                    @if ($tour->include != "")
                                        <div class="page-subtitle">@lang('messages.Inclusion')</div>
                                        <div class="page-text">
                                            {!! $tour->$langInclude !!}
                                        </div>
                                    @endif
                                    @if ($tour->exclude != "")
                                        <div class="page-subtitle">@lang('messages.Exclusion')</div>
                                        <div class="page-text">
                                            {!! $tour->$langExclude !!}
                                        </div>
                                    @endif
                                    @if ($tour->additional_info != "")
                                        <div class="page-subtitle">@lang('messages.Additional Information')</div>
                                        <div class="page-text">
                                            {!! $tour->$langAdditionalInfo !!}
                                        </div>
                                    @endif
                                    @if ($tour->cancellation_policy != "")
                                        <div class="page-subtitle">@lang('messages.Cancelation Policy')</div>
                                        <div class="page-text">
                                            <p>{!! $tour->$langCancellationPolicy !!}</p>
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
                                                                <div class="normal-text">@lang('messages.Price/pax')</div>
                                                                <div class="normal-text">@lang('messages.Number of Guests')</div>
                                                                @if ($order->additional_service_total_price > 0)
                                                                    <div class="normal-text">@lang('messages.Additional Service')</div>
                                                                @endif
                                                                <hr class="form-hr">
                                                                @if (!empty($filteredDiscounts))
                                                                    @foreach ($filteredDiscounts as $discountName => $value)
                                                                        <div class="normal-text">@lang("messages.$discountName")</div>
                                                                    @endforeach
                                                                    <hr class="form-hr">
                                                                @endif
                                                                <div class="price-name">@lang('messages.Total Price')</div>
                                                                {{-- @if ($optionalServiceTotalPrice > 0)
                                                                    <div class="normal-text">@lang('messages.Additional Charge')</div>
                                                                @endif --}}
                                                                {{-- @if ($additional_service_total_price > 0)
                                                                    <div class="normal-text">@lang('messages.Additional Service')</div>
                                                                @endif --}}
                                                                {{-- @if ($total_price_airport_shuttle > 0)
                                                                    <div id="airportShuttle" class="normal-text">@lang('messages.Airport Shuttle')</div>
                                                                @endif --}}
                                                                {{-- <hr class="form-hr">
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
                                                                @endif --}}
                                                            </div>
                                                            <div class="col-6 col-md-6 text-right">
                                                                <div class="text-price">{{ currencyFormatUsd($order->price_pax) }}</div>
                                                                <div class="text-price">{{ $order->number_of_guests }}</div>
                                                                @if ($order->additional_service_total_price > 0)
                                                                    <div class="text-price"><span>{{ currencyFormatUsd($order->additional_service_total_price) }}</span></div>
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
                                                                <div class="usd-rate">{{ currencyFormatUsd($order->final_price) }}</div>
                                                                {{-- @if ($order->request_quotation == "Yes")
                                                                    <div id="suitesAndVillasPrice" class="text-price"><span id="suitesAndVillasPriceLable">@lang('messages.To be advised')</span></div>
                                                                @else
                                                                    <div id="suitesAndVillasPrice" class="text-price"><span id="suitesAndVillasPriceLable">{{ currencyFormatUsd($order->price_total) }}</span></div>
                                                                @endif --}}
                                                                {{-- @if ($optionalServiceTotalPrice > 0)
                                                                    <div class="text-price"><span id="additionalChargePriceLable">{{ currencyFormatUsd($optionalServiceTotalPrice) }}</span></div>
                                                                @endif --}}
                                                                {{-- @if ($additional_service_total_price > 0)
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
                                                                </div> --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                    <div class="card-box-footer">
                                        @if ($invoice && $order->status != "Paid")
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#payment-confirmation-{{ $order->id }}">
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
                                        @endif
                                        @if ($order->status == "Draft")
                                            <div class="form-group">
                                                @if ($order->status != "Invalid")
                                                    @if (Auth::user()->email == "" or Auth::user()->phone == "" or Auth::user()->office == "" or Auth::user()->address == "" or Auth::user()->country == "")
                                                        <button type="button" class="btn btn-light"><i class="icon-copy fa fa-info" aria-hidden="true"> </i> @lang('messages.You cannot submit this order')</button>
                                                    @else
                                                        <button type="submit" form="edit-order" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Submit')</button>
                                                    @endif
                                                @endif
                                                <a href="/orders">
                                                    <button class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                                </a>
                                            </div>
                                        @elseif ($order->status == "Rejected")
                                            <form id="removeOrder" class="hidden" action="/fremove-order/{{ $order->id }}"method="post" enctype="multipart/form-data">
                                                @csrf
                                                @method('put')
                                                <input type="hidden" name="status" value="Removed">
                                                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                            </form>
                                            <button type="submit" form="removeOrder" class="btn btn-danger"><i class="icon-copy fa fa-trash-o" aria-hidden="true"></i> @lang('messages.Delete')</button>
                                        @else
                                            <a href="/orders">
                                                <button type="button" class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="loading-icon hidden pre-loader">
                                    <div class="pre-loader-box">
                                        <div class="sys-loader-logo w3-center"> <img class="w3-spin" src="{{ asset('storage/icon/spinner.png') }}" alt="Bali Kami Tour Logo"></div>
                                        <div class="loading-text">
                                            Submitting an Order...
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
