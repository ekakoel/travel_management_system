@php
    $no = 0;
@endphp
<div class="col-md-8">
    <div class="card-box">
        <div class="card-box-title">
            <div class="subtitle"><i class="fa fa-pencil"></i> @lang('messages.Edit Order')</div>
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
        <form id="submitOrder" action="{{ route('func.submit-order-hotel',$order->id) }}" method="post" enctype="multipart/form-data">
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
                        <tr>
                            <td class="htd-1">
                                @lang('messages.Location')
                            </td>
                            <td class="htd-2">
                                {{ $order->location }}
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
                        @if ($order->promotion)
                            <tr>
                                <td class="htd-1">@lang('messages.Promotions')</td>
                                <td class="htd-2">{{ $promotions_name }}</td>
                            </tr>
                        @endif
                        @foreach ($services as $service)
                            <tr>
                                <td class="htd-1">@lang($service['label'])</td>
                                <td class="htd-2">{{ $service['value'] }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td class="htd-1">@lang('messages.Room')</td>
                            <td class="htd-2">{{ $order->subservice }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table tb-list">
                        @foreach ([
                            'messages.Duration' => $order->duration . " " . __('messages.Nights'),
                            'messages.Check In' => dateFormat($order->checkin),
                            'messages.Check Out' => dateFormat($order->checkout)
                        ] as $label => $value)
                            <tr>
                                <td class="htd-1">@lang($label)</td>
                                <td class="htd-2">{{ $value }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            @foreach ([
                'benefits' => 'messages.Benefit',
                'include' => 'messages.Include',
                'additional_info' => 'messages.Additional Information',
                'cancellation_policy' => 'messages.Cancelation Policy'
            ] as $key => $label)
                @if (!empty($order->$key))
                    <div class="page-text">
                        <hr class="form-hr">
                        <b>@lang($label) :</b> <br>
                        {!! $order->$key !!}
                    </div>
                @endif
            @endforeach

            {{-- SITES AND VILLAS ================================================================================================================================ --}}
            <div id="sitesAndVillas" class="page-subtitle" style="{{ $hasInvalidOrder ? 'background-color: #ffe3e3; border: 2px dotted red;' : '' }}">
                @lang('messages.Suites and Villas')
            </div>
            <div class="row">
                @if ($hasInvalidOrder)
                    <div class="col-sm-12 m-b-18">
                        <div class="room-container">
                            <p style="color:brown;"><i>@lang('messages.You have not selected a room on this booking!')</i></p>
                        </div>
                    </div>
                @else
                    <div class="col-md-12">
                        <table class="data-table table nowrap">
                            <thead>
                                <tr>
                                    <th>@lang('messages.Room')</th>
                                    <th>@lang('messages.Guests')</th>
                                    <th>@lang('messages.Room Price')</th>
                                    <th>@lang('messages.Extra Bed')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($nogr as $index => $number_of_guest_room)
                                    <tr style="{{ $special_day[$index] ? 'background-color: #ffe695;' : '' }}"
                                        data-toggle="tooltip" data-placement="top"
                                        title="{{ $special_day[$index] ? dateFormat($special_date[$index]) . ' ' . $special_day[$index] : '' }}">
                                        <td><div class="table-service-name">{{ $room->rooms }}</div></td>
                                        <td><div class="table-service-name">{{ $number_of_guest_room }}</div></td>
                                        <td><div class="table-service-name">{{ currencyFormatUsd($order->price_pax) }}</div></td>
                                        <td>
                                            @if ($extra_bed_test[$index] == 'Yes')
                                                @php
                                                    $extra_bed = $extraBeds->where('id',$extra_bed_id[$index])->first();
                                                @endphp
                                                @if ($extra_bed)
                                                    <div class="table-service-name">
                                                        {{ $extra_bed->name }} ({{ $extra_bed->type }})
                                                        {{ currencyFormatUsd(($extra_bed->calculatePrice($usdrates, $tax))*$order->duration) }}
                                                    </div>
                                                @else
                                                    <p class="text-danger">
                                                        <i>@lang('messages.Invalid')!</i>
                                                        <i class="icon-copy fa fa-info-circle" aria-hidden="true" style="color: #7e7e7e;"
                                                           data-toggle="tooltip" data-placement="top"
                                                           title="@lang('messages.This room is occupied by more than 2 guests, and requires an extra bed, please edit it first to be able to submit an order')">
                                                        </i>
                                                    </p>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
            
                    <div class="col-md-12">
                        <div class="box-price-kicked m-b-8">
                            <div class="row">
                                <div class="col-6 col-md-6">
                                    @if ($multipleRooms || $showExtraBedPrice || $order->kick_back > 0)
                                        @if ($multipleRooms && $showExtraBedPrice || $multipleRooms && $order->kick_back > 0)
                                            <div class="normal-text">@lang('messages.Room')</div>
                                        @endif
                                        @if ($showExtraBedPrice)
                                            <div class="normal-text">@lang('messages.Extra Bed')</div>
                                        @endif
                                        @if ($order->kick_back > 0)
                                            <div class="normal-text">@lang('messages.Kick Back')</div>
                                        @endif
                                        @if ($multipleRooms && $showExtraBedPrice || $multipleRooms && $order->kick_back > 0)
                                            <hr class="form-hr">
                                        @endif
                                    @endif
                                    <div class="subtotal-text">@lang('messages.Suites and Villas')</div>
                                </div>
                                <div class="col-6 col-md-6 text-right">
                                    @if ($multipleRooms || $showExtraBedPrice || $order->kick_back > 0)
                                        @if ($multipleRooms && $showExtraBedPrice || $multipleRooms && $order->kick_back > 0)
                                            <div class="text-price">{{ currencyFormatUsd($order->price_pax * $order->number_of_room) }}</div>
                                        @endif
                                        @if ($showExtraBedPrice)
                                            <div class="text-price">{{ currencyFormatUsd($order->extra_bed_total_price) }}</div>
                                        @endif
                                        @if ($order->kick_back > 0)
                                            <div class="promo-text">{{ currencyFormatUsd($order->kick_back) }}</div>
                                        @endif
                                        @if ($multipleRooms && $showExtraBedPrice || $multipleRooms && $order->kick_back > 0)
                                            <hr class="form-hr">
                                        @endif
                                    @endif
                                    <div class="subtotal-price">{{ currencyFormatUsd($order->price_total) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-md-8">
                    <div class="checkbox-left">
                        <input name="request_quotation" type="checkbox" style="display: block !important;" value="Yes" {{ $order->request_quotation == "Yes" ? "checked":""; }}> 
                        <p>
                            @lang('messages.Ask for quote rates for rooms more than 8 units')
                        </p>
                    </div>
                </div>
                @if ($canEditOrder)
                    <div class="col-4 text-right">
                        <a href="{{ route('view.edit-order-room', $order->id) }}">
                            <button type="button" class="btn btn-primary">
                                <i class="icon-copy fa fa-{{ $hasInvalidOrder ? 'plus' : 'pencil' }}" aria-hidden="true"></i>
                                @lang('messages.' . ($hasInvalidOrder ? 'Add' : 'Edit'))
                            </button>
                        </a>
                    </div>
                @endif
            </div>
            
            {{-- ADDITIONAL CHARGE =============================================================================================================================== --}}
            @if (count($optionalrates->where('hotels_id',$order->service_id)) > 0)
                @if ($order->number_of_guests > 0)
                    <div id="optional_service" class="page-subtitle">
                        @lang('messages.Additional Charge')
                    </div>
                    @if ($optional_rate_orders != "")
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="data-table table nowrap" >
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">@lang('messages.Date') </th>
                                            <th style="width: 15%;">@lang('messages.Service')</th>
                                            <th style="width: 5%;">@lang('messages.Guests')</th>
                                            <th style="width: 5%;">@lang('messages.Price')/@lang('messages.pax')</th>
                                            <th style="width: 10%;">@lang('messages.Total Price')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->optional_rate_orders as $optional_rate_order)
                                            <tr>
                                                <td>
                                                    <div class="table-service-name">{{ dateFormat($optional_rate_order->service_date) }}</div>
                                                </td>
                                                <td>
                                                    <div class="table-service-name">{{ $optional_rate_order->optional_rate->name }}</div>
                                                </td>
                                                <td>
                                                    <div class="table-service-name">{{ $optional_rate_order->number_of_guest }}</div>
                                                </td>
                                                <td>
                                                    <div class="table-service-name">{{ currencyFormatUsd($optional_rate_order->price_pax) }}</div>
                                                </td>
                                                <td>
                                                    <div class="table-service-name">{{ currencyFormatUsd($optional_rate_order->price_total) }}</div>
                                                </td>
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
                                            <div class="subtotal-price">{{ currencyFormatUsd($order->optional_price) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-12 text-right">
                            @if ($order->status == "Draft" or $order->status == "Rejected" or $order->status == "Invalid")
                                <a href="{{ route("view.edit-order-additional-charge",$order->id) }}">
                                    <button type="button" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="{{ $optional_rate_orders != "" ? __('messages.Edit') : __('messages.Add') }}"><i class="icon-copy fa fa-{{ $optional_rate_orders != "" ? "pencil" : "plus" }}" aria-hidden="true"></i> {{ $optional_rate_orders != "" ? __('messages.Edit') : __('messages.Add') }}</button>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
            {{-- AIRPORT SHUTTLE ========================================================================================================================================= --}}
            <div class="page-subtitle">@lang('messages.Airport Shuttle')</div>
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
                            @foreach ($transports as $transport_out)
                                <option value="{{ $transport_out->id }}" 
                                    data-transportout="1" 
                                    data-transporpriceout="{{ $transport_out->calculated_price }}"
                                    data-idtransporpriceout="{{ $transport_out->calculated_price_id }}"
                                    {{ $airport_shuttle_out && $airport_shuttle_out->transport_id == $transport_out->id ? 'selected' : '' }}>
                                    {{ $transport_out->brand }} {{ $transport_out->name }} - ({{ $transport_out->capacity }})
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
           <div class="page-subtitle">@lang('messages.Remark')</div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <textarea name="note" class="textarea_editor form-control border-radius-0" placeholder="@lang('messages.Optional')">{{ $order->note }}</textarea>
                        @error('note') <div class="alert alert-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
            {{-- PRICES ========================================================================================================================================= --}}
            <div class="page-subtitle">@lang('messages.Price')</div>
            <div class="row">
                <div class="col-md-12 m-b-8">
                    <div class="box-price-kicked">
                        <div class="row">
                            <div class="col-6 col-md-6">
                                <div class="normal-text">@lang('messages.Suites and Villas')</div>
                                @if ($order->optional_price > 0)
                                    <div class="normal-text">@lang('messages.Additional Charge')</div>
                                @endif
                                <div id="airportShuttle" class="normal-text">@lang('messages.Airport Shuttle')</div>
                                <hr class="form-hr">
                                @if ($order->bookingcode_disc > 0)
                                    <div class="normal-text">@lang('messages.Booking Code')</div>
                                @endif

                                @if ($order->discounts > 0)
                                    <div class="normal-text">@lang('messages.Discounts')</div>
                                @endif
                                @if ($promotion_discount > 0)
                                    <div class="normal-text">@lang('messages.Promotion')</div>
                                @endif
                                @if ($order->kick_back > 0 or $order->bookingcode_disc > 0 or $order->discounts > 0 or $promotion_discount > 0)
                                    <hr class="form-hr">
                                @endif
                                <div class="total-price">@lang('messages.Total Price')</div>
                            </div>
                            <div class="col-6 col-md-6 text-right">
                                <div id="suitesAndVillasPrice" class="text-price"><span id="suitesAndVillasPriceLable">{{ currencyFormatUsd($order->price_total) }}</span></div>

                                @if ($order->optional_price > 0)
                                    <div class="text-price"><span id="additionalChargePriceLable">{{ currencyFormatUsd($order->optional_price) }}</span></div>
                                @endif
                                <div id="airportShuttlePrice" class="text-price"><span id="airportShuttleText">{{ $airport_shuttle_any_zero?__('messages.To be advised'):currencyFormatUsd($order->airport_shuttle_price) }}</span></div>
                                <hr class="form-hr">
                                @if ($order->bookingcode_disc > 0)
                                    <div class="promo-text">{{ currencyFormatUsd($order->bookingcode_disc) }}</div>
                                @endif

                                @if ($order->discounts > 0)
                                    <div class="promo-text">{{ currencyFormatUsd($order->discounts) }}</div>
                                @endif
                                @if ($promotion_discount > 0)
                                    <div class="promo-text">{{ currencyFormatUsd($promotion_discount) }}</div>
                                @endif
                            
                                @if ($order->kick_back > 0 or $order->bookingcode_disc > 0 or $order->discounts > 0 or $promotion_discount > 0)
                                    <hr class="form-hr">
                                @endif
                                <div class="total-price"><span id="finalPrice">{{ $airport_shuttle_any_zero ? __('messages.To be advised'):currencyFormatUsd($order->final_price) }}</span></div>
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
                            {{ $order->msg }}
                        @elseif ($order->status == "Invalid")
                            {{ $order->msg }}
                        @endif
                    
                    </div>
                </div>
                

                <input type="hidden" name="orderno" value="{{ $order->orderno }}">
                <input type="hidden" name="page" value="hotel-detail">
                <input type="hidden" name="total_promotion_discount" id="promotionDiscountsTotal" value="{{ $promotion_discount }}">
                <input type="hidden" name="total_price_optional_rate" id="totalPriceOptionalRate" value="{{ $order->optional_price }}">
                <input type="hidden" name="total_price_suites_and_villas" id="totalPriceSuitesAndVillas" value="{{ $order->price_total }}">
                <input type="hidden" name="total_price_airport_shuttle" id="totalPriceAirportShuttle" value="{{ $order->airport_shuttle_price }}">
                <input type="hidden" name="airport_shuttle_in_price" id="airportShuttleInPrice" value="{{ $airport_shuttle_in ? $airport_shuttle_in->price : 0 }}">
                <input type="hidden" name="airport_shuttle_out_price" id="airportShuttleOutPrice" value="{{ $airport_shuttle_out ? $airport_shuttle_out->price : 0 }}">
                <input type="hidden" name="transport_in_price_id" id="transportInPriceId" value="">
                <input type="hidden" name="transport_out_price_id" id="transportOutPriceId" value="">
                <input type="hidden" name="transport_any_zero" id="transport_any_zero" value="{{ $airport_shuttle_any_zero }}">
                <input type="hidden" name="final_price" id="inputFinalPrice" value="{{ $order->price_total + $order->optional_price + $total_price_airport_shuttle }}">


                @if ($order->service == "Hotel")
                    <input type="hidden" name="service" value="Hotel">
                    <input type="hidden" name="normal_price" value="{{ $order->price_pax }}">
                    <input type="hidden" name="kick_back" value="{{ $order->kick_back }}">
                    <input type="hidden" name="include" value="{{ $order->include }}">
                    <input type="hidden" name="additional_info" value="{{ $order->additional_info }}">

                @elseif ($order->service == "Hotel Promo")
                    <input type="hidden" name="service" value="Hotel Promo">

                @elseif ($order->service == "Hotel Package")
                    <input type="hidden" name="normal_price" value="{{ $order->price_pax }}">
                    <input type="hidden" name="booking_code" value="{{ $order->booking_code }}">
                    <input type="hidden" name="service" value="Hotel Package">
                    <input type="hidden" name="package_name" value="{{ $order->name }}">
                    <input type="hidden" name="benefits" value="{{ $order->benefits }}">
                    <input type="hidden" name="include" value="{{ $order->include }}">
                    <input type="hidden" name="additional_info" value="{{ $order->additional_info }}">
                @endif
            </div>
        </Form>
        <div class="card-box-footer">
            @if ($order->status == "Draft")
                    @if ($order->number_of_room == "" or $order->number_of_guests_room == "" or $order->guest_detail == "" or $order->guest_detail == ""  )
                        <button type="button" class="btn btn-light"><i class="icon-copy fa fa-info" aria-hidden="true"> </i> @lang('messages.You cannot submit this order')</button>
                    @else
                        @if ($order->status != "Invalid")
                            @if (Auth::user()->email == "" or Auth::user()->phone == "" or Auth::user()->office == "" or Auth::user()->address == "" or Auth::user()->country == "")
                                <button disabled type="submit" form="submitOrder" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Submit')</button>
                            @else
                                <button type="submit" form="submitOrder" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Submit')</button>
                            @endif
                        @else
                            <p class="notification-danger">@lang('messages.An error has occurred in the Suites and Villas section')</p>
                            <button disabled type="submit" form="submitOrder" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> @lang('messages.Submit')</button>
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


@include('partials.loading-form', ['id' => 'submitOrder'])
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        const pricePromotionDiscounts = document.querySelector("#promotionDiscountsTotal");
        const airportShuttleAnyZero = document.querySelector("#airport_shuttle_any_zero");
        function updateTotalPrice() {
            const valueIn = transportIn.value.trim();
            const valueOut = transportOut.value.trim();
            const totalPriceOptionalRate = parseFloat(priceOptionalRate.value) || 0;
            const totalPriceSuitesAndVillas = parseFloat(priceSuitesAndVillas.value) || 0;
            const promotionDiscountsTotal = parseFloat(pricePromotionDiscounts.value) || 0;
            const priceIn = parseFloat(transportIn.selectedOptions[0]?.dataset.transporpricein) || 0;
            const priceInId = parseFloat(transportIn.selectedOptions[0]?.dataset.idtransporpricein) || 0;
            const priceOut = parseFloat(transportOut.selectedOptions[0]?.dataset.transporpriceout) || 0;
            const priceOutId = parseFloat(transportOut.selectedOptions[0]?.dataset.idtransporpriceout) || 0;
            const totalAirportShuttlePrice = priceIn + priceOut;
            const finalPriceOrder = totalPriceOptionalRate + totalPriceSuitesAndVillas + totalAirportShuttlePrice - promotionDiscountsTotal;
            if ( !valueIn && !valueOut) {
                [totalShuttleText, totalAirportShuttleOutPrice, textAirportShuttle].forEach(el => el.style.display = "none");
                hiddenTotalInput.value = "";
                inputAirportShuttleInPrice.value = "";
                inputAirportShuttleOutPrice.value = "";
                inputAirportShuttleInPriceId.value = "";
                inputAirportShuttleOutPriceId.value = "";
                finalPriceValue.textContent = formatCurrency(finalPriceOrder);
                inputFinalPrice.value = finalPriceOrder;
                return;
            }
            [totalShuttleText, totalAirportShuttleOutPrice, textAirportShuttle].forEach(el => el.style.display = "block");
            if (priceIn === 0 && valueIn || priceOut === 0 && valueOut) {
                totalPriceElement.textContent = totalAirportShuttleOutPrice.textContent = finalPriceValue.textContent = "{{ __('messages.To be advised') }}";
                inputAirportShuttleInPrice.value = priceIn;
                inputAirportShuttleOutPrice.value = priceOut;
                hiddenTotalInput.value = totalAirportShuttlePrice;
                inputAirportShuttleInPriceId.value = priceInId;
                inputAirportShuttleOutPriceId.value = priceOutId;
                inputFinalPrice.value = finalPriceOrder;
            } else {
                const formattedTotal = formatCurrency(totalAirportShuttlePrice);
                totalPriceElement.textContent = totalAirportShuttleOutPrice.textContent = formattedTotal;
                hiddenTotalInput.value = totalAirportShuttlePrice;
                inputAirportShuttleInPriceId.value = priceInId;
                inputAirportShuttleOutPriceId.value = priceOutId;
                inputAirportShuttleInPrice.value = priceIn;
                inputAirportShuttleOutPrice.value = priceOut;
                inputFinalPrice.value = finalPriceOrder;
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