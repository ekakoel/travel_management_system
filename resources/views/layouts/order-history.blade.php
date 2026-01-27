
<div id="orderVilla" class="col-md-12">
    <div class="card-box m-b-18">
        <div class="card-box-title">
            <div class="subtitle"><i class="icon-copy fa fa-clock-o" aria-hidden="true"></i> @lang('messages.Order History')</div>
        </div>
        <div class="input-container">
            <div class="input-group">
                <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                <input id="searchVillaOrderNo" type="text" onkeyup="searchVillaOrderNo()" class="form-control" name="search-active-order-byagn" placeholder="Filter by Order number">
            </div>
        </div>
        <table id="tbVillaOrd" class="data-table table m-b-30">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 20%;">@lang('messages.Order Number')</th>
                    <th style="width: 35%;">@lang('messages.Check-in') - @lang('messages.Check-out')</th>
                    <th style="width: 20%;">@lang('messages.Price')</th>
                    <th style="width: 20%;">@lang('messages.Action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orderhistories as $no => $order_history)
                    @if ($order_history->promotion != "")
                        @php
                            $promotion_name = json_decode($order_history->promotion);
                            $promotion_disc = json_decode($order_history->promotion_disc);
                            $total_promotion_disc = array_sum($promotion_disc);
                        @endphp
                    @else
                        @php
                            $total_promotion_disc = 0;
                        @endphp
                    @endif
                    <tr style="background-color: {{ $order_history->status == 'Rejected' ? '#ffd4d4':"none" }};">
                        <td>{{ ++$no }}</td>
                        <td>
                            <p class="p-0 m-0"><b>{{ $order_history->orderno }}</b></p>
                        </td>
                        
                        <td>
                            <p class="p-0 m-0">{{ dateFormat($order_history->checkin) }} - {{ dateFormat($order_history->checkout) }}</p>
                            
                        </td>
                        <td>
                            <p class="final-price">{{ currencyFormatUsd($order_history->final_price) }}</p>
                        </td>
                        <td class="text-right">
                            <div class="table-action">
                                @if($order_history->status == "Paid")
                                    @if (File::exists("storage/document/invoice-".$order_history->reservations?->invoice?->inv_no."-".$order_history->id."_en.pdf"))
                                        <a href="#" data-toggle="modal" data-target="#contract-en-{{ $order_history->id }}">
                                            <button class="btn-view" data-toggle="tooltip" data-placement="top" title="{{ __('messages.Invoice') }} EN"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i></button>
                                        </a>
                                        {{-- MODAL VIEW CONTRACT EN --}}
                                        <div class="modal fade" id="contract-en-{{ $order_history->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                    <div class="modal-body pd-5">
                                                        <embed src="/storage/document/invoice-{{ $order_history->reservations?->invoice?->inv_no."-".$order_history->id }}_en.pdf" frameborder="10" width="100%" height="850px">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if (File::exists("storage/document/invoice-".$order_history->reservations?->invoice?->inv_no."-".$order_history->id."_zh.pdf"))
                                        <a href="#" data-toggle="modal" data-target="#contract-zh-{{ $order_history->id }}">
                                            <button class="btn-view" data-toggle="tooltip" data-placement="top" title="{{ __('messages.Invoice') }} ZH"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i></button>
                                        </a>
                                        {{-- MODAL VIEW CONTRACT ZH --}}
                                        <div class="modal fade" id="contract-zh-{{ $order_history->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                    <div class="modal-body pd-5">
                                                        <embed src="/storage/document/invoice-{{ $order_history->reservations?->invoice?->inv_no."-".$order_history->id }}_zh.pdf" frameborder="10" width="100%" height="850px">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                 <a href="#" data-toggle="modal" data-target="#detail-order-{{ $order_history->id }}">
                                    <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <!-- Modal -->
                    <div class="modal fade" id="detail-order-{{ $order_history->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content text-left">
                                <div class="card-box">
                                    <div class="card-box-title">
                                        <i class="icon-copy fa fa-tags" aria-hidden="true"></i> 
                                        {{ $order_history->orderno }}
                                    </div>
                                    <div class="card-box-body">
                                        <!-- Detail Order -->
                                        <div class="card-content">
                                            <div class="row">
                                                <div class="col-md-12">
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
                                                                {{ dateFormat($order_history->created_at) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="business-name">{{ $business->name }}</div>
                                                    <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                                                    <hr class="form-hr">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-5">
                                                                    <p>@lang('messages.Order No')</p>
                                                                </div>
                                                                <div class="col-7">
                                                                    <p>: <b>{{ $order_history->orderno }}</b></p>
                                                                </div>
                                                                <div class="col-5">
                                                                    <p>@lang('messages.Order Date')</p>
                                                                </div>
                                                                <div class="col-7">
                                                                    <p>: {{ dateTimeFormat($order_history->created_at) }}</p>
                                                                </div>
                                                                <div class="col-5">
                                                                    <p>@lang('messages.Service')</p>
                                                                </div>
                                                                <div class="col-7">
                                                                    <p>: @lang('messages.'.$order_history->service)</p>
                                                                </div>
                                                                <div class="col-5">
                                                                    <p>@lang('messages.Region')</p>
                                                                </div>
                                                                <div class="col-7">
                                                                    <p>: {{ $order_history->location }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="page-subtitle">@lang('messages.Order Details')</div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-5">
                                                                    <p>@lang('messages.Service')</p>
                                                                </div>
                                                                <div class="col-7">
                                                                    <p>: {{ $order_history->servicename }}</p>
                                                                </div>
                                                                <div class="col-5">
                                                                    <p>@lang('messages.Number of Room')</p>
                                                                </div>
                                                                <div class="col-7">
                                                                    <p>: {{ $order_history->number_of_room." ".__('messages.rooms') }}</p>
                                                                </div>
                                                                <div class="col-5">
                                                                    <p>@lang('messages.Number of Guests')</p>
                                                                </div>
                                                                <div class="col-7">
                                                                    <p>: {{ $order_history->number_of_guests." ".__('messages.guests') }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-5">
                                                                    <p>@lang('messages.Duration')</p>
                                                                </div>
                                                                <div class="col-7">
                                                                    <p>: {{ $order_history->duration > 1?$order_history->duration." ".__('messages.nights'):$order_history->duration." ".__('messages.night') }}</p>
                                                                </div>
                                                                <div class="col-5">
                                                                    <p>@lang('messages.Check In')</p>
                                                                </div>
                                                                <div class="col-7">
                                                                    <p>: {{ dateFormat($order_history->checkin) }}</p>
                                                                </div>
                                                                <div class="col-5">
                                                                    <p>@lang('messages.Check Out')</p>
                                                                </div>
                                                                <div class="col-7">
                                                                    <p>: {{ dateFormat($order_history->checkout) }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            @foreach([
                                                                'benefits' => 'messages.Benefit',
                                                                'include' => 'messages.Include',
                                                                'additional_info' => 'messages.Additional Information',
                                                                'cancellation_policy' => 'messages.Cancelation Policy',
                                                            ] as $key => $label)
                                                                @if (!empty($order_history->$key))
                                                                    <div class="page-text">
                                                                        <hr class="form-hr">
                                                                        <b>@lang($label) :</b> <br>
                                                                        {!! $order_history->$key !!}
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                                @if (count($order_history?->optional_rate_orders)>0)
                                                    <div class="col-md-12">
                                                        <div id="optional_service" class="page-subtitle">
                                                            @lang('messages.Additional Service')
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="page-text">
                                                                    <div class="row">
                                                                        @foreach ($order_history?->optional_rate_orders as $oh_no=>$order_optional_rate)
                                                                            <div class="col-1">{{ ++$oh_no }}.</div>
                                                                            <div class="col-3">{{ dateFormat($order_optional_rate->service_date) }}</div>
                                                                            <div class="col-5">{{ $order_optional_rate->optional_rate->name }}</div>
                                                                            <div class="col-3 text-right">{{ currencyFormatUsd($order_optional_rate->price_total) }}</div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                                <div class="box-price-kicked m-t-8 m-b-8">
                                                                    <div class="row">
                                                                        <div class="col-6 col-md-6">
                                                                            <div class="subtotal-text">@lang('messages.Additional Service')</div>
                                                                        </div>
                                                                        <div class="col-6 col-md-6 text-right">
                                                                            <div class="subtotal-price">{{ currencyFormatUsd($order_history->optional_price) }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($order_history->additional_service != "")
                                                    @php
                                                        $num = 0;
                                                        $additional_charges = json_decode($order_history->additional_service);
                                                        $additional_charges_date = json_decode($order_history->additional_service_date);
                                                        $additional_charges_qty = json_decode($order_history->additional_service_qty);
                                                        $additional_charges_price = json_decode($order_history->additional_service_price);
                                                        if ($additional_charges) {
                                                            $count = count($additional_charges);
                                                        }else {
                                                            $count = 0;
                                                        }
                                                    @endphp
                                                    <div class="col-md-12">
                                                        <div id="optional_service" class="page-subtitle">
                                                            @lang('messages.Additional Charge')
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="page-text">
                                                                    <div class="row">
                                                                        @for ($i = 0; $i < $count; $i++)
                                                                            <div class="col-1">{{ ++$num }}.</div>
                                                                            <div class="col-3">{{ dateFormat($additional_charges_date[$i]) }}</div>
                                                                            <div class="col-5">{{ $additional_charges[$i] }}</div>
                                                                            <div class="col-3 text-right">{{ currencyFormatUsd(($additional_charges_price[$i]*$additional_charges_qty[$i])) }}</div>
                                                                        @endfor
                                                                       
                                                                    </div>
                                                                </div>
                                                                <div class="box-price-kicked m-t-8 m-b-8">
                                                                    <div class="row">
                                                                        <div class="col-6 col-md-6">
                                                                            <div class="subtotal-text">@lang('messages.Additional Charge')</div>
                                                                        </div>
                                                                        <div class="col-6 col-md-6 text-right">
                                                                            <div class="subtotal-price">{{ currencyFormatUsd($order_history->additional_service_total_price) }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if (count($order_history->airport_shuttles) > 0)
                                                    <div class="col-md-12">
                                                        <div id="optional_service" class="page-subtitle">
                                                            @lang('messages.Airport Shuttle')
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="page-text">
                                                                    <div class="row">
                                                                        @foreach ($order_history->airport_shuttles as $as_no=>$airport_shuttle)
                                                                            <div class="col-1">{{ ++$as_no }}.</div>
                                                                            <div class="col-3">{{ dateFormat($airport_shuttle->date) }}</div>
                                                                            <div class="col-5">{{ $airport_shuttle->nav == "In"?"Arrival":"Departure" }}</div>
                                                                            <div class="col-3 text-right">{{ currencyFormatUsd($airport_shuttle->price) }}</div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                                <div class="box-price-kicked m-t-8 m-b-8">
                                                                    <div class="row">
                                                                        <div class="col-6 col-md-6">
                                                                            <div class="subtotal-text">@lang('messages.Airport Shuttle')</div>
                                                                        </div>
                                                                        <div class="col-6 col-md-6 text-right">
                                                                            <div class="subtotal-price">{{ currencyFormatUsd($order_history->airport_shuttle_price) }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($order_history->note)
                                                    <div class="col-md-12">
                                                        <div class="page-subtitle">@lang('messages.Note')</div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="page-text">
                                                                    {!! $order_history->note !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="col-md-12">
                                                    <div class="page-subtitle">@lang('messages.Price')</div>
                                                    <div class="row">
                                                        <div class="col-md-12 m-b-8">
                                                            <div class="box-price-kicked">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-6 col-md-6">
                                                                                @if ($order_history->service == "Tour Package")
                                                                                    <div class="normal-text">@lang('messages.Tour Package')</div>
                                                                                @elseif($order_history->service == "Private Villa")
                                                                                    <div class="normal-text">@lang('messages.Private Villa')</div>
                                                                                @elseif($order_history->service == "Hotel" || $order_history->service == "Hotel Promo" || $order_history->service == "Hotel Package")
                                                                                    <div class="normal-text">@lang('messages.Suites and Villas')</div>
                                                                                @else
                                                                                    <div class="normal-text">@lang('messages.Service')</div>
                                                                                @endif
                                                                                @if ($order_history->optional_price > 0)
                                                                                    <div class="normal-text">@lang('messages.Additional Service')</div>
                                                                                @endif
                                                                                @if ($order_history->additional_service_total_price > 0)
                                                                                    <div class="normal-text">@lang('messages.Additional Charge')</div>
                                                                                @endif
                                                                                @if ($order_history->airport_shuttle_price > 0)
                                                                                    <div id="airportShuttle" class="normal-text">@lang('messages.Airport Shuttle')</div>
                                                                                @endif
                                                                                <hr class="form-hr">
                                                                                @if ($order_history->kick_back > 0 || $order_history->discounts > 0 || $order_history->promotion_disc > 0 || $order_history->bookingcode_disc > 0)
                                                                                    @if ($order_history->kick_back > 0)
                                                                                        <div class="normal-text">@lang('messages.Kick Back')</div>
                                                                                    @endif
                                                                                    @if ($order_history->discounts > 0)
                                                                                        <div class="normal-text">@lang('messages.Discounts')</div>
                                                                                    @endif
                                                                                    @if ($order_history->bookingcode_disc > 0)
                                                                                        <div class="normal-text">@lang('messages.Booking Code')</div>
                                                                                    @endif
                                                                                    @if ($order_history->promotion_disc > 0)
                                                                                        <div class="normal-text">@lang('messages.Promotion')</div>
                                                                                    @endif
                                                                                    <hr class="form-hr">
                                                                                @endif
                                                                                @php
                                                                                    $reservation = $order_history->reservations;
                                                                                    $invoice = $reservation?->invoice;
                                                                                @endphp
                                                                                @if (isset($invoice))
                                                                                    @if ($invoice->currency_id == 1)
                                                                                        <div class="price-name">@lang('messages.Total Price') USD</div>
                                                                                    @elseif ($invoice->currency_id == 2)
                                                                                        <div class="normal-text">@lang('messages.Total Price') USD</div>
                                                                                        <div class="price-name">@lang('messages.Total Price') CNY</div>
                                                                                    @elseif ($invoice->currency_id == 3)
                                                                                        <div class="normal-text">@lang('messages.Total Price') USD</div>
                                                                                        <div class="price-name">@lang('messages.Total Price') TWD</div>
                                                                                    @else
                                                                                        <div class="normal-text">@lang('messages.Total Price') USD</div>
                                                                                        <div class="price-name">@lang('messages.Total Price') IDR</div>
                                                                                    @endif
                                                                                @else
                                                                                    <div class="price-name">@lang('messages.Total Price') USD</div>
                                                                                @endif
                                                                            </div>
                                                                            <div class="col-6 col-md-6 text-right">
                                                                                @if ($order_history->request_quotation == "Yes")
                                                                                    <div id="suitesAndVillasPrice" class="text-price"><span id="suitesAndVillasPriceLable">@lang('messages.To be advised')</span></div>
                                                                                @else
                                                                                    <div id="suitesAndVillasPrice" class="text-price"><span id="suitesAndVillasPriceLable">{{ currencyFormatUsd($order_history->normal_price) }}</span></div>
                                                                                @endif
                                                                                @if ($order_history->optional_price > 0)
                                                                                    <div class="text-price"><span id="additionalChargePriceLable">{{ currencyFormatUsd($order_history->optional_price) }}</span></div>
                                                                                @endif
                                                                                @if ($order_history->additional_service_total_price > 0)
                                                                                    <div class="text-price"><span id="additionalServiceTotalPrice">{{ currencyFormatUsd($order_history->additional_service_total_price) }}</span></div>
                                                                                @endif
                                                                                @if ($order_history->airport_shuttle_price > 0)
                                                                                    <div id="airportShuttlePrice" class="text-price"><span id="airportShuttleText">{{ currencyFormatUsd($order_history->airport_shuttle_price) }}</span></div>
                                                                                @endif
                                                                                <hr class="form-hr">
                                                                                @if ($order_history->kick_back > 0 || $order_history->discounts > 0 || $order_history->promotion_disc > 0)
                                                                                    @if ($order_history->kick_back > 0)
                                                                                        <div class="promo-text">{{ currencyFormatUsd($order_history->kick_back) }}</div>
                                                                                    @endif
                                                                                    @if ($order_history->discounts > 0)
                                                                                        <div class="promo-text">{{ currencyFormatUsd($order_history->discounts) }}</div>
                                                                                    @endif
                                                                                    @if ($order_history->bookingcode_disc > 0)
                                                                                        <div class="promo-text">{{ currencyFormatUsd($order_history->bookingcode_disc) }}</div>
                                                                                    @endif
                                                                                    @if ($order_history->promotion_disc > 0)
                                                                                        <div class="promo-text">{{ currencyFormatUsd($order_history->promotion_disc) }}</div>
                                                                                    @endif
                                                                                    <hr class="form-hr">
                                                                                @endif
                                                                                <div class="total-price">
                                                                                    @if (isset($invoice))
                                                                                        @if ($invoice->currency_id == 1)
                                                                                            <div class="usd-rate">{{ currencyFormatUsd($invoice->total_usd) }}</div>
                                                                                        @elseif ($invoice->currency_id == 2)
                                                                                            <div class="usd-rate">{{ currencyFormatUsd($invoice->total_usd) }}</div>
                                                                                            <div class="usd-rate">{{ currencyFormatcny($invoice->total_cny) }}</div>
                                                                                        @elseif ($invoice->currency_id == 3)
                                                                                            <div class="usd-rate">{{ currencyFormatUsd($invoice->total_usd) }}</div>
                                                                                            <div class="usd-rate">{{ currencyFormatTwd($invoice->total_twd) }}</div>
                                                                                        @else
                                                                                            <div class="usd-rate">{{ currencyFormatUsd($invoice->total_usd) }}</div>
                                                                                            <div class="usd-rate">{{ currencyFormatIdr($invoice->total_idr) }}</div>
                                                                                        @endif
                                                                                    @else
                                                                                        <div class="usd-rate">{{ currencyFormatUsd($order_history->final_price) }}</div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-box-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                                            <i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>
</div>