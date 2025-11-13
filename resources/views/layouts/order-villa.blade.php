
<div id="orderVilla" class="col-md-12">
    <div class="card-box m-b-18">
        <div class="card-box-title">
            <div class="subtitle"><i class="dw dw-building-1"></i>@lang('messages.Private Villa Orders')</div>
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
                    <th style="width: 1%;">@lang('messages.No')</th>
                    <th style="width: 10%;">@lang('messages.Service')</th>
                    <th style="width: 10%;">@lang('messages.Number of Guests')</th>
                    <th style="width: 10%;">@lang('messages.Duration')</th>
                    <th style="width: 10%;">@lang('messages.Price')</th>
                    <th style="width: 5%;">@lang('messages.Action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($villaorders as $no => $villa_order)
                    @if ($villa_order->promotion != "")
                        @php
                            $promotion_name = json_decode($villa_order->promotion);
                            $promotion_disc = json_decode($villa_order->promotion_disc);
                            $total_promotion_disc = array_sum($promotion_disc);
                        @endphp
                    @else
                        @php
                            $total_promotion_disc = 0;
                        @endphp
                    @endif
                    <tr style="background-color: {{ $villa_order->status == 'Rejected' ? '#ffd4d4':"none" }};">
                        <td>{{ ++$no }}</td>
                        <td>
                            <p class="p-0 m-0"><b>{{ $villa_order->orderno }}</b></p>
                            <p class="p-0 m-0">{{ $villa_order->servicename }}</p>
                            @if ($villa_order->status === 'Approved')
                                <p><i class="color-blue">{{ __('messages.Awaiting Payment') }}</i></p>
                            @endif
                            @if (isset($statusMap[$villa_order->status]))
                                <div class="{{ $statusMap[$villa_order->status]['class'] }} inline-left">
                                    {!! $statusMap[$villa_order->status]['label'] !!}
                                </div>
                            @endif
                            @if ($villa_order->discounts > 0)
                                <div class="status-discounts inline-left" data-toggle="tooltip" data-placement="top" title="{{ __('messages.Discounts')." $". $villa_order->discounts }}"><i class="icon-copy fa fa-percent" aria-hidden="true"></i></div>
                            @endif
                            @if ($villa_order->bookingcode_disc > 0)
                                <div class="status-bcode inline-left" data-toggle="tooltip" data-placement="top" title="{{ __('messages.Booking code discounts')." $".$villa_order->bookingcode_disc }}"><i class="icon-copy fa fa-percent" aria-hidden="true"></i></div>
                            @endif
                            @if ($villa_order->kick_back > 0)
                                <div class="status-kick-back inline-left" data-toggle="tooltip" data-placement="top" title="{{ __('messages.Kick Back')." $".$villa_order->kick_back }}"><i class="icon-copy fa fa-percent" aria-hidden="true"></i></div>
                            @endif
                            @if ($total_promotion_disc > 0)
                                <div class="status-promotion inline-left" data-toggle="tooltip" data-placement="top" title="{{ __('messages.Promotions')." $".$total_promotion_disc }}"><i class="icon-copy fa fa-percent" aria-hidden="true"></i></div>
                            @endif
                        </td>
                        <td>
                            <p>
                                {{ $villa_order->number_of_guests." " }}@lang('messages.guests')
                            </p>
                        </td>
                        <td>
                            <p class="p-0 m-0">{{ $villa_order->duration }} @lang('messages.Nights')</p>
                            <p class="p-0 m-0">@lang('messages.In'): {{ dateFormat($villa_order->checkin) }}</p>
                            <p class="p-0 m-0">@lang('messages.Out'): {{ dateFormat($villa_order->checkout) }}</p>
                        </td>
                        <td>
                            @if ($villa_order->discounts > 0 || $villa_order->bookingcode_disc > 0 || $villa_order->kick_back >0 || $total_promotion_disc > 0)
                                <p class="normal-price">{{ currencyFormatUsd($villa_order->final_price + $villa_order->discounts + $villa_order->bookingcode_disc + $villa_order->kick_back + $total_promotion_disc) }}</p>
                                <p class="final-price">{{ currencyFormatUsd($villa_order->final_price) }}</p>
                            @else
                                <p class="final-price">{{ currencyFormatUsd($villa_order->final_price) }}</p>
                            @endif
                        </td>
                        <td class="text-right">
                            @php
                                $rsv_order = $reservations->where('id',$villa_order->rsv_id)->first();
                            @endphp
                            <div class="table-action">
                                @if ($villa_order->status == "Draft" || $villa_order->status == "Invalid")
                                    <a href="{{ route('view.edit-order-villa',$villa_order->id) }}">
                                        <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                    </a>
                                    <form class="display-content" action="/delete-order/{{ $villa_order->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                        <button class="btn-delete" onclick="return confirm('@lang('messages.Are you sure?')');" type="submit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Delete')"><i class="icon-copy fa fa-trash"></i></button>
                                    </form>
                                @elseif ($villa_order->status == "Rejected")
                                    <a href="{{ route('view.detail-order-villa',$villa_order->id) }}">
                                        <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                    </a>
                                    <form class="display-content" action="/delete-order/{{ $villa_order->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                        <button class="btn-delete" onclick="return confirm('@lang('messages.Are you sure?')');" type="submit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Delete')"><i class="icon-copy fa fa-trash"></i></button>
                                    </form>
                                @elseif ($villa_order->status == "Confirmed")
                                    <a href="{{ route('view.detail-order-villa',$villa_order->id) }}">
                                        <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                    </a>
                                @else
                                    <a href="{{ route('view.detail-order-villa',$villa_order->id) }}">
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