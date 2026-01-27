<div id="orderTransport" class="col-md-12">
    <div class="card-box">
        <div class="card-box-title">
            <div class="subtitle"><i class="fa fa-car"></i>@lang('messages.Transport Orders')</div>
        </div>
        <div class="input-container">
            <div class="input-group">
                <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                <input id="searchTransportOrderNo" type="text" onkeyup="searchTransportOrderNo()" class="form-control" name="search-active-order-byagn" placeholder="Order number">
            </div>
            <div class="input-group">
                <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                <input id="searchTransportByGuestName" type="text" onkeyup="searchTransportByGuestName()" class="form-control" name="search-active-order-type" placeholder="Guest name">
            </div>
        </div>
        <table id="tbTransport" class="data-table table m-b-30">
            <thead>
                <tr>
                    <th style="width: 1%;">@lang('messages.No')</th>
                    <th style="width: 10%;">@lang('messages.Transport')</th>
                    <th style="width: 10%;">@lang('messages.Guest Name')</th>
                    <th style="width: 10%;">@lang('messages.Service')</th>
                    <th style="width: 10%;">@lang('messages.Price')</th>
                    <th style="width: 5%;">@lang('messages.Action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transportorders as $no => $transportorder)
                    @php
                        $rsv_trans = $reservations->where('id',$transportorder->rsv_id)->first();
                    @endphp
                    @if ($transportorder->promotion_disc != "")
                        @php
                            $promotion_name = json_decode($transportorder->promotion,true);
                            $promotion_disc = json_decode($transportorder->promotion_disc,true);
                            $total_promotion_disc = array_sum($promotion_disc);
                            $cpn = count($promotion_name);
                        @endphp
                    @else
                        @php
                            $total_promotion_disc = 0;
                        @endphp
                    @endif
                    @if ($transportorder->status == "Rejected")
                        <tr style="background-color: #ffd4d4;">
                    @elseif($transportorder->status == "Confirmed" and $rsv_trans->send == "yes")
                        <tr data-toggle="tooltip" data-placement="top" title="@lang('messages.This order requires your approval')" style="background-color: #fbffa9;">
                    @else
                        <tr>
                    @endif
                        <td>{{ ++$no }}</td>
                        <td>
                            <div class="row">
                                <div class="col-sm-12">
                                    <p class="p-0 m-0"><b>{{ $transportorder->orderno }}</b></p>
                                    <p class="p-0 m-0">{{ $transportorder->subservice }}</p>
                                    <p class="p-0 m-0">{{ dateTimeFormat($transportorder->pickup_date) }}</p>
                                    <p class="p-0 m-0">{{ $transportorder->servicename }}</p>
                                    @if ($transportorder->status == "Rejected")
                                        <div class="status-rejected inline-left p-r-8"></div>
                                    @elseif ($transportorder->status == "Paid")
                                        <div class="status-paid inline-left p-r-8"><i class="icon-copy ion-android-checkmark-circle"></i></div>
                                    @elseif ($transportorder->status == "Approved")
                                        <div class="status-approved inline-left p-r-8"><i class="icon-copy fa fa-check-circle-o" aria-hidden="true"></i></div>
                                    @elseif ($transportorder->status == "Confirmed")
                                        <div class="status-confirmed inline-left p-r-8"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i></div>
                                        <p><i class="color-blue">@lang('messages.Awaiting Payment')</i></p>
                                    @elseif ($transportorder->status == "Invalid")
                                        <div class="status-invalid inline-left p-r-8"><i class="icon-copy fa fa-close" aria-hidden="true"></i></div>
                                    @elseif ($transportorder->status == "Active")
                                        <div class="status-progress inline-left p-r-8"><i class="icon-copy fa fa-clock-o" aria-hidden="true"></i></div>
                                    @elseif ($transportorder->status == "Pending")
                                        <div class="status-waiting inline-left p-r-8"><span class="icon-copy ti-alarm-clock"></span></div>
                                    @elseif ($transportorder->status == "Draft")
                                        <div class="status-draft inline-left"></div>
                                    @endif
                                    @if ($transportorder->discounts > 0)
                                        <div class="status-discounts inline-left p-r-8" data-toggle="tooltip" data-placement="top" title="@lang('messages.Discount') ${{ $transportorder->discounts }}"></div>
                                    @endif
                                    @if ($transportorder->bookingcode_disc > 0)
                                        <div class="status-bcode inline-left p-r-8" data-toggle="tooltip" data-placement="top" title="{{ $transportorder->bookingcode." ($".$transportorder->bookingcode_disc.")" }}"></div>
                                    @endif
                                        @if ($total_promotion_disc > 0)
                                        <div class="status-promotion inline-left p-r-8" data-toggle="tooltip" data-placement="top" title="@lang('messages.Promotion'){{ ' $'.$total_promotion_disc }}"></div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="p-0 m-0">{!! $transportorder->guest_detail !!}</p>
                        </td>
                        <td>
                            <p class="p-0 m-0">{{ $transportorder->number_of_guests." " }}@lang('messages.Guests')</p>
                            @if ($transportorder->subservice == "Daily Rent")
                                <p class="p-0 m-0">{{ $transportorder->duration." " }}@lang('messages.Days')</p>
                            @else
                                <p class="p-0 m-0">{{ "Src : ". $transportorder->pickup_location }}</p>
                                <p class="p-0 m-0">{{ "Dst : ". $transportorder->dropoff_location }}</p>
                            @endif
                        </td>
                        <td>
                            @if ($transportorder->discounts > 0 || $transportorder->bookingcode_disc > 0 ||  $total_promotion_disc > 0)
                                <p class="normal-price">{{ '$ ' . number_format(($transportorder->final_price + $transportorder->discounts + $transportorder->bookingcode_disc + $total_promotion_disc), 0, ',', '.') }}</p>
                            @endif
                            <p>{{ '$ ' . number_format($transportorder->final_price, 0, ',', '.') }}</p>
                        </td>
                        <td class="text-right">
                            <div class="table-action">
                                @if ($transportorder->status == "Draft")
                                    <a href="{{ route('view.edit-order-transport',$transportorder->id) }}">
                                        <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                    </a>
                                    <form class="display-content" action="/delete-order/{{ $transportorder->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                        <button class="btn-delete" onclick="return confirm('@lang('messages.Are you sure?')');" type="submit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Delete')"><i class="icon-copy fa fa-trash"></i></button>
                                    </form>
                                @elseif ($transportorder->status == "Rejected")
                                    <a href="{{ route('view.detail-order-transport',$transportorder->id) }}">
                                        <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                    </a>
                                    <form class="display-content" action="/delete-order/{{ $transportorder->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                        <button class="btn-delete" onclick="return confirm('@lang('messages.Are you sure?')');" type="submit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Delete')"><i class="icon-copy fa fa-trash"></i></button>
                                    </form>
                                @elseif ($transportorder->status == "Confirmed" and $rsv_trans->send == "yes")
                                    <a href="{{ route('view.detail-order-transport',$transportorder->id) }}">
                                        <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                    </a>
                                    <form id="approveOrder" class="hidden" action="/fapprove-order-{{ $transportorder->id }}"method="post" enctype="multipart/form-data">
                                        @csrf
                                        @method('put')
                                    </form>
                                    <button type="submit" form="approveOrder" class="btn-approve" data-toggle="tooltip" data-placement="top" title="@lang('messages.Approve Order')"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i></button>
                                @else
                                    <a href="{{ route('view.detail-order-transport',$transportorder->id) }}">
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