
<div id="orderHotel" class="col-md-12">
    <div class="card-box m-b-18">
        <div class="card-box-title">
            <div class="subtitle"><i class="dw dw-hotel"></i>@lang('messages.Hotel Orders')</div>
        </div>
        <div class="input-container">
            <div class="input-group">
                <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                <input id="searchHotelOrderNo" type="text" onkeyup="searchHotelOrderNo()" class="form-control" name="search-active-order-byagn" placeholder="Filter by Order number">
            </div>
        </div>
        <table id="tbHotelOrd" class="data-table table m-b-30">
            <thead>
                <tr>
                    <th style="width: 1%;">@lang('messages.No')</th>
                    <th style="width: 10%;">@lang('messages.Service')</th>
                    <th style="width: 10%;">@lang('messages.Room')</th>
                    <th style="width: 10%;">@lang('messages.Duration')</th>
                    <th style="width: 10%;">@lang('messages.Price')</th>
                    <th style="width: 5%;">@lang('messages.Action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hotelorders as $no => $hotelorder)
                    @php
                        $rsv_hotel = $reservations->where('id',$hotelorder->rsv_id)->first();
                    @endphp
                    @if ($hotelorder->promotion != "")
                        @php
                            $promotion_name = json_decode($hotelorder->promotion);
                            $promotion_disc = json_decode($hotelorder->promotion_disc);
                            $total_promotion_disc = array_sum($promotion_disc);
                        @endphp
                    @else
                        @php
                            $total_promotion_disc = 0;
                        @endphp
                    @endif
                    @if ($hotelorder->number_of_guests_room > 0)
                        @php
                            $tp_extrabed = json_decode($hotelorder->extra_bed_price);
                            
                            if (isset($tp_extrabed)) {
                                $extrabedprice = array_sum($tp_extrabed);
                            }else{
                                $extrabedprice = 0;
                            }
                            $final_price = ($hotelorder->price_pax * $hotelorder->number_of_room) + $hotelorder->optional_price + $extrabedprice - $hotelorder->kick_back - $hotelorder->discounts;
                        @endphp
                    @else
                        @php
                            $hotelguestname = 0;
                            $total_price = 0;
                        @endphp
                    @endif
                    @if ($hotelorder->status == "Rejected")
                        <tr style="background-color: #ffd4d4;">
                    @elseif($hotelorder->status == "Confirmed" and $rsv_hotel->send == "yes")
                        <tr data-toggle="tooltip" data-placement="top" title="@lang("messages.This order requires your approval")!" style="background-color: #fbffa9;">
                    @else
                        <tr>
                    @endif
                        <td>{{ ++$no }}</td>
                        <td>
                            <p class="p-0 m-0"><b>{{ $hotelorder->orderno }}</b></p>
                            <p class="p-0 m-0">@lang('messages.'.$hotelorder->service)</p>
                            <p class="p-0 m-0">{{ $hotelorder->servicename }}</p>
                            @if ($hotelorder->status === 'Approved')
                                <p><i class="color-blue">{{ __('messages.Awaiting Payment') }}</i></p>
                            @endif
                            @if (isset($statusMap[$hotelorder->status]))
                                <div class="{{ $statusMap[$hotelorder->status]['class'] }} inline-left">
                                    {!! $statusMap[$hotelorder->status]['label'] !!}
                                </div>
                            @endif
                            @if ($hotelorder->discounts > 0)
                                <div class="status-discounts inline-left" data-toggle="tooltip" data-placement="top" title="{{ __('messages.Discounts')." $". $hotelorder->discounts }}"><i class="icon-copy fa fa-percent" aria-hidden="true"></i></div>
                            @endif
                            @if ($hotelorder->bookingcode_disc > 0)
                                <div class="status-bcode inline-left" data-toggle="tooltip" data-placement="top" title="{{ __('messages.Booking code discounts')." $".$hotelorder->bookingcode_disc }}"><i class="icon-copy fa fa-percent" aria-hidden="true"></i></div>
                            @endif
                            @if ($hotelorder->kick_back > 0)
                                <div class="status-kick-back inline-left" data-toggle="tooltip" data-placement="top" title="{{ __('messages.Kick Back')." $".$hotelorder->kick_back }}"><i class="icon-copy fa fa-percent" aria-hidden="true"></i></div>
                            @endif
                            @if ($total_promotion_disc > 0)
                                <div class="status-promotion inline-left" data-toggle="tooltip" data-placement="top" title="{{ __('messages.Promotions')." $".$total_promotion_disc }}"><i class="icon-copy fa fa-percent" aria-hidden="true"></i></div>
                            @endif
                        </td>
                        <td>
                            <p>
                                {{ $hotelorder->subservice }}<br>
                                @if ($hotelorder->request_quotation != "Yes")
                                    {{ $hotelorder->number_of_room." " }}@lang('messages.Unit')
                                @endif
                               
                            </p>
                        </td>
                        <td>
                            @if ($hotelorder->request_quotation == "Yes")
                                <div class="checkbox">
                                    <p style="color:blue;" class="m-t-8 m-b-18">
                                       @lang('messages.Requesting a quote for bookings of more than 8 rooms')
                                    </p>
                                </div>
                            @else
                                @if ($hotelorder->service == 'Hotel')
                                    <p class="p-0 m-0">{{ $hotelorder->duration }} @lang('messages.Nights')</p>
                                    <p class="p-0 m-0">@lang('messages.In'): {{ dateFormat($hotelorder->checkin) }}</p>
                                    <p class="p-0 m-0">@lang('messages.Out'): {{ dateFormat($hotelorder->checkout) }}</p>
                                @elseif ($hotelorder->service == 'Hotel Package')
                                    <p class="p-0 m-0">{{ $hotelorder->duration }} @lang('messages.Nights')</p>
                                    <p class="p-0 m-0">@lang('messages.In'): {{ dateFormat($hotelorder->checkin) }}</p>
                                    <p class="p-0 m-0">@lang('messages.Out'): {{ dateFormat($hotelorder->checkout) }}</p>
                                @elseif ($hotelorder->service == 'Hotel Promo')
                                    <p class="p-0 m-0">{{ $hotelorder->duration }} @lang('messages.Nights')</p>
                                    <p class="p-0 m-0">@lang('messages.In'): {{ dateFormat($hotelorder->checkin) }}</p>
                                    <p class="p-0 m-0">@lang('messages.Out'): {{ dateFormat($hotelorder->checkout) }}</p>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if ($hotelorder->kick_back > 0 or $hotelorder->discounts > 0 or $hotelorder->bookingcode_disc > 0 or  $total_promotion_disc > 0)
                                @if (isset($hotelorder->optional_price))
                                    <p class="normal-price">{{ currencyFormatUsd($hotelorder->final_price + $hotelorder->kick_back + $hotelorder->bookingcode_disc + $total_promotion_disc + $hotelorder->discounts) }}</p>
                                    <p class="final-price">{{ currencyFormatUsd($hotelorder->final_price) }}</p>
                                @else
                                    <p class="normal-price">{{ currencyFormatUsd($hotelorder->final_price + $hotelorder->kick_back + $hotelorder->bookingcode_disc + $total_promotion_disc + $hotelorder->discounts) }}</p>
                                    <p class="final-price">{{ currencyFormatUsd($hotelorder->final_price) }}</p>
                                @endif
                            @else
                                <p class="final-price">{{ currencyFormatUsd($hotelorder->final_price) }}</p>
                            @endif
                        </td>
                        <td class="text-right">
                            @php
                                $rsv_order = $reservations->where('id',$hotelorder->rsv_id)->first();
                            @endphp
                            <div class="table-action">
                                @if ($hotelorder->status == "Draft")
                                    <a href="{{ route('view.edit-order-hotel',$hotelorder->id) }}">
                                        <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                    </a>
                                    <form class="display-content" action="/delete-order/{{ $hotelorder->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                        <button class="btn-delete" onclick="return confirm('@lang('messages.Are you sure?')');" type="submit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Delete')"><i class="icon-copy fa fa-trash"></i></button>
                                    </form>
                                @elseif ($hotelorder->status == "Rejected")
                                    <a href="{{ route('view.detail-order-hotel',$hotelorder->id) }}">
                                        <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                    </a>
                                    <form class="display-content" action="/delete-order/{{ $hotelorder->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                        <button class="btn-delete" onclick="return confirm('@lang('messages.Are you sure?')');" type="submit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Delete')"><i class="icon-copy fa fa-trash"></i></button>
                                    </form>
                                @elseif ($hotelorder->status == "Confirmed")
                                    <a href="{{ route('view.detail-order-hotel',$hotelorder->id) }}">
                                        <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                    </a>
                                    <form id="approveOrder" class="hidden" action="/fapprove-order-{{ $hotelorder->id }}"method="post" enctype="multipart/form-data">
                                        @csrf
                                        @method('put')
                                    </form>
                                    <button type="submit" form="approveOrder" class="btn-approve" data-toggle="tooltip" data-placement="top" title="@lang('messages.Approve Order')"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i></button>
                                @else
                                    <a href="{{ route('view.detail-order-hotel',$hotelorder->id) }}">
                                        <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                    </a>
                                @endif
                                
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>