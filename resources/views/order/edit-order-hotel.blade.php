@php
    $no = 0;
    $transferRows = $airport_shuttles
        ->sortBy('date')
        ->values()
        ->map(function ($shuttle) {
            return [
                'type' => $shuttle->nav === 'Out' ? 'departure' : 'arrival',
                'flight_number' => $shuttle->flight_number ?? '',
                'flight_time' => $shuttle->date ? date('Y-m-d H:i', strtotime($shuttle->date)) : '',
                'transport_id' => $shuttle->transport_id,
            ];
        })
        ->all();

    if (empty($transferRows)) {
        $transferRows = [[
            'type' => '',
            'flight_number' => '',
            'flight_time' => '',
            'transport_id' => null,
        ]];
    }
@endphp
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
                                                    $extra_bed_room_price = (float) ($extraBedPrices[$index] ?? 0);
                                                @endphp
                                                @if ($extra_bed)
                                                    <div class="table-service-name">
                                                        {{ $extra_bed->name }} ({{ $extra_bed->type }})
                                                        {{ currencyFormatUsd($extra_bed_room_price) }}
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
            <div class="row" id="airportShuttleEditor">
                <div class="col-md-12">
                    <div id="airportShuttleRows">
                        @foreach ($transferRows as $index => $transferRow)
                            <div class="row align-items-end airport-shuttle-row m-b-12" data-transfer-row>
                                <div class="col-xl-2 col-lg-3 col-md-6">
                                    <div class="form-group">
                                        <label>@lang('messages.Type')</label>
                                        <select name="flight_type[]" class="custom-select" data-flight-type>
                                            <option value="">@lang('messages.Select Type')</option>
                                            <option value="arrival" {{ $transferRow['type'] === 'arrival' ? 'selected' : '' }}>@lang('messages.Arrival')</option>
                                            <option value="departure" {{ $transferRow['type'] === 'departure' ? 'selected' : '' }}>@lang('messages.Departure')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-lg-3 col-md-6">
                                    <div class="form-group">
                                        <label>@lang('messages.Flight Number')</label>
                                        <input
                                            type="text"
                                            name="flight_number[]"
                                            class="form-control"
                                            placeholder="@lang('messages.Insert flight number')"
                                            value="{{ $transferRow['flight_number'] }}"
                                        >
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-6">
                                    <div class="form-group">
                                        <label>@lang('messages.Date and time')</label>
                                        <input
                                            readonly
                                            type="text"
                                            name="flight_time[]"
                                            class="form-control booking-datetime-input"
                                            placeholder="@lang('messages.Select date and time')"
                                            value="{{ $transferRow['flight_time'] }}"
                                        >
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-8 col-md-6">
                                    <div class="form-group">
                                        <label>@lang('messages.Airport Shuttle') <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.Request')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label>
                                        <select name="flight_transport_id[]" class="custom-select booking-transfer-select" data-flight-transport>
                                            <option value="" data-transport-price="0" data-transport-price-id="">@lang('messages.Select Transport')</option>
                                            @foreach ($transports as $transport)
                                                <option
                                                    value="{{ $transport->id }}"
                                                    data-transport-price="{{ $transport->calculated_price }}"
                                                    data-transport-price-id="{{ $transport->calculated_price_id }}"
                                                    {{ (string) $transferRow['transport_id'] === (string) $transport->id ? 'selected' : '' }}
                                                >
                                                    {{ $transport->brand }} {{ $transport->name }} - ({{ $transport->capacity }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-1 col-lg-1 col-md-12">
                                    <button type="button" class="btn btn-danger airport-shuttle-row__remove w-100" data-remove-flight {{ count($transferRows) === 1 && $index === 0 ? 'disabled' : '' }}>
                                        <i class="icon-copy fa fa-close" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary m-b-16" id="addAirportShuttleRow">
                        <i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Add More Flight')
                    </button>
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const formatCurrency = amount => new Intl.NumberFormat('en-US', { 
            style: 'currency', 
            currency: 'USD',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
        const rowContainer = document.querySelector("#airportShuttleRows");
        const addRowButton = document.querySelector("#addAirportShuttleRow");
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
        const inputFinalPrice = document.querySelector("#inputFinalPrice");

        function initDatePickers(scope) {
            if (typeof flatpickr !== "function") {
                return;
            }

            scope.querySelectorAll(".booking-datetime-input").forEach(function (input) {
                if (input._flatpickr) {
                    input._flatpickr.destroy();
                }

                flatpickr(input, {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    minuteIncrement: 5,
                    minDate: "today",
                    defaultDate: input.value || new Date(),
                    allowInput: false,
                    clickOpens: true,
                    disableMobile: true,
                });
            });
        }

        function getRows() {
            return Array.from(rowContainer.querySelectorAll("[data-transfer-row]"));
        }

        function updateRemoveButtons() {
            const rows = getRows();
            rows.forEach(function (row) {
                const button = row.querySelector("[data-remove-flight]");
                if (button) {
                    button.disabled = rows.length === 1;
                }
            });
        }

        function createRow() {
            const row = document.createElement("div");
            row.className = "row align-items-end airport-shuttle-row m-b-12";
            row.setAttribute("data-transfer-row", "");
            row.innerHTML = `
                <div class="col-xl-2 col-lg-3 col-md-6">
                    <div class="form-group">
                        <label>@lang('messages.Type')</label>
                        <select name="flight_type[]" class="custom-select" data-flight-type>
                            <option value="">@lang('messages.Select Type')</option>
                            <option value="arrival">@lang('messages.Arrival')</option>
                            <option value="departure">@lang('messages.Departure')</option>
                        </select>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-3 col-md-6">
                    <div class="form-group">
                        <label>@lang('messages.Flight Number')</label>
                        <input type="text" name="flight_number[]" class="form-control" placeholder="@lang('messages.Insert flight number')">
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-6">
                    <div class="form-group">
                        <label>@lang('messages.Date and time')</label>
                        <input readonly type="text" name="flight_time[]" class="form-control booking-datetime-input" placeholder="@lang('messages.Select date and time')">
                    </div>
                </div>
                <div class="col-xl-4 col-lg-8 col-md-6">
                    <div class="form-group">
                        <label>@lang('messages.Airport Shuttle') <i style="color: #7e7e7e;" data-toggle="tooltip" data-placement="top" title="@lang('messages.Request')" class="icon-copy fa fa-info-circle" aria-hidden="true"></i></label>
                        <select name="flight_transport_id[]" class="custom-select booking-transfer-select" data-flight-transport>
                            <option value="" data-transport-price="0" data-transport-price-id="">@lang('messages.Select Transport')</option>
                            @foreach ($transports as $transport)
                                <option value="{{ $transport->id }}" data-transport-price="{{ $transport->calculated_price }}" data-transport-price-id="{{ $transport->calculated_price_id }}">
                                    {{ $transport->brand }} {{ $transport->name }} - ({{ $transport->capacity }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-xl-1 col-lg-1 col-md-12">
                    <button type="button" class="btn btn-danger airport-shuttle-row__remove w-100" data-remove-flight>
                        <i class="icon-copy fa fa-close" aria-hidden="true"></i>
                    </button>
                </div>
            `;

            rowContainer.appendChild(row);
            initDatePickers(row);
            updateRemoveButtons();
            updateTotalPrice();
        }

        function syncPrimaryLegacyFields() {
            let primaryIn = null;
            let primaryOut = null;

            getRows().forEach(function (row) {
                const type = row.querySelector('[name="flight_type[]"]')?.value || "";
                const transport = row.querySelector('[name="flight_transport_id[]"]');
                const flightNumber = row.querySelector('[name="flight_number[]"]')?.value?.trim() || "";

                if (!primaryIn && type === "arrival" && transport && transport.value) {
                    primaryIn = {
                        price: parseFloat(transport.selectedOptions[0]?.dataset.transportPrice) || 0,
                        priceId: transport.selectedOptions[0]?.dataset.transportPriceId || "",
                        flightNumber: flightNumber
                    };
                }

                if (!primaryOut && type === "departure" && transport && transport.value) {
                    primaryOut = {
                        price: parseFloat(transport.selectedOptions[0]?.dataset.transportPrice) || 0,
                        priceId: transport.selectedOptions[0]?.dataset.transportPriceId || "",
                        flightNumber: flightNumber
                    };
                }
            });

            inputAirportShuttleInPrice.value = primaryIn ? primaryIn.price : "";
            inputAirportShuttleOutPrice.value = primaryOut ? primaryOut.price : "";
            inputAirportShuttleInPriceId.value = primaryIn ? primaryIn.priceId : "";
            inputAirportShuttleOutPriceId.value = primaryOut ? primaryOut.priceId : "";
        }

        function updateTotalPrice() {
            const totalPriceOptionalRate = parseFloat(priceOptionalRate.value) || 0;
            const totalPriceSuitesAndVillas = parseFloat(priceSuitesAndVillas.value) || 0;
            const promotionDiscountsTotal = parseFloat(pricePromotionDiscounts.value) || 0;
            let hasSelectedTransport = false;
            let hasZeroPrice = false;
            let totalAirportShuttlePrice = 0;

            getRows().forEach(function (row) {
                const transport = row.querySelector('[name="flight_transport_id[]"]');
                if (!transport || !transport.value) {
                    return;
                }

                hasSelectedTransport = true;
                const selected = transport.selectedOptions[0];
                const rowPrice = parseFloat(selected?.dataset.transportPrice) || 0;
                totalAirportShuttlePrice += rowPrice;

                if (rowPrice === 0) {
                    hasZeroPrice = true;
                }
            });

            const finalPriceOrder = totalPriceOptionalRate + totalPriceSuitesAndVillas + totalAirportShuttlePrice - promotionDiscountsTotal;

            syncPrimaryLegacyFields();

            if (!hasSelectedTransport) {
                [totalShuttleText, totalAirportShuttleOutPrice, textAirportShuttle].forEach(el => el.style.display = "none");
                hiddenTotalInput.value = "";
                finalPriceValue.textContent = formatCurrency(finalPriceOrder);
                inputFinalPrice.value = finalPriceOrder;
                return;
            }

            [totalShuttleText, totalAirportShuttleOutPrice, textAirportShuttle].forEach(el => el.style.display = "block");

            if (hasZeroPrice) {
                totalPriceElement.textContent = totalAirportShuttleOutPrice.textContent = finalPriceValue.textContent = "{{ __('messages.To be advised') }}";
                hiddenTotalInput.value = totalAirportShuttlePrice;
                inputFinalPrice.value = finalPriceOrder;
            } else {
                const formattedTotal = formatCurrency(totalAirportShuttlePrice);
                totalPriceElement.textContent = totalAirportShuttleOutPrice.textContent = formattedTotal;
                hiddenTotalInput.value = totalAirportShuttlePrice;
                inputFinalPrice.value = finalPriceOrder;
                finalPriceValue.textContent = formatCurrency(finalPriceOrder);
            }
        }

        rowContainer.addEventListener("click", function (event) {
            const removeButton = event.target.closest("[data-remove-flight]");
            if (!removeButton) {
                return;
            }

            const rows = getRows();
            if (rows.length <= 1) {
                return;
            }

            removeButton.closest("[data-transfer-row]").remove();
            updateRemoveButtons();
            updateTotalPrice();
        });

        rowContainer.addEventListener("change", function (event) {
            if (event.target.matches('[name="flight_type[]"], [name="flight_transport_id[]"], [name="flight_time[]"]')) {
                updateTotalPrice();
            }
        });

        rowContainer.addEventListener("input", function (event) {
            if (event.target.matches('[name="flight_number[]"]')) {
                syncPrimaryLegacyFields();
            }
        });

        addRowButton.addEventListener("click", function () {
            createRow();
        });

        initDatePickers(document);
        updateRemoveButtons();
        updateTotalPrice();
    });
</script>
