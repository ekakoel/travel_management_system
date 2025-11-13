<div class="col-md-12">
    <div class="card-box">
        <div class="card-box-title">
            <div class="subtitle"><i class="fa fa-briefcase"></i>@lang('messages.Tour Package Orders')</div>
        </div>
        <div class="input-container">
            <div class="input-group">
                <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                <input id="searchOrderNo" type="text" onkeyup="searchOrderNo()" class="form-control" name="search-active-order-byagn" placeholder="Order number">
            </div>
            <div class="input-group">
                <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                <input id="searchByGuestName" type="text" onkeyup="searchByGuestName()" class="form-control" name="search-active-order-type" placeholder="Guest name">
            </div>
        </div>
        <table id="tbltp" class="data-table table m-b-30">
            <thead>
                <tr>
                    <th data-priority="1" class="datatable-nosort" style="width: 5%;">@lang('messages.No')</th>
                    <th data-priority="1" style="width: 25%;">@lang('messages.Package')</th>
                    <th data-priority="1" class="datatable-nosort" style="width: 25%;">@lang('messages.Guest Name')</th>
                    <th class="datatable-nosort" style="width: 25%;">@lang('messages.Detail')</th>
                    <th data-priority="1" class="datatable-nosort" style="width: 10%;">@lang('messages.Price')</th>
                    <th class="datatable-nosort" style="width: 10%;">@lang('messages.Action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tourorders as $no => $tourorder)
                    @php
                        $rsv_tour = $reservations->where('id',$tourorder->rsv_id)->first();
                    @endphp
                    @if ($tourorder->promotion != "")
                        @php
                            $promotion_name = json_decode($tourorder->promotion);
                            $promotion_disc = json_decode($tourorder->promotion_disc);
                            $total_promotion_disc = array_sum($promotion_disc);
                            $cpn = count($promotion_name);
                        @endphp
                    @else
                        @php
                            $total_promotion_disc = 0;
                        @endphp
                    @endif
                    @if ($tourorder->status == "Rejected")
                        <tr style="background-color: #ffd4d4;">
                    @elseif($tourorder->status == "Confirmed" and $rsv_tour->send == "yes")
                        <tr data-toggle="tooltip" data-placement="top" title="This order requires your approval!" style="background-color: #fbffa9;">
                    @else
                        <tr>
                    @endif
                    
                        <td>{{ ++$no }}</td>
                        <td>
                            <p class="p-0 m-0"><b>{{ $tourorder->orderno }}</b></p>
                            <p class="p-0 m-0">{{ $tourorder->subservice }}</p>
                            @if (isset($statusMap[$tourorder->status]))
                                <div class="{{ $statusMap[$tourorder->status]['class'] }} inline-left">
                                    {!! $statusMap[$tourorder->status]['label'] !!}
                                </div>
                            @endif
                            @if ($tourorder->discounts > 0)
                                <div class="status-discounts inline-left p-r-8" data-toggle="tooltip" data-placement="top" title="@lang('messages.Discount') ${{ $tourorder->discounts }}"></div>
                            @endif
                            @if ($tourorder->bookingcode_disc > 0)
                                <div class="status-bcode inline-left p-r-8" data-toggle="tooltip" data-placement="top" title="{{ $tourorder->bookingcode." ($".$tourorder->bookingcode_disc.")" }}"></div>
                            @endif
                                @if ($total_promotion_disc > 0)
                                <div class="status-promotion inline-left p-r-8" data-toggle="tooltip" data-placement="top" title="@lang('messages.Promotion'){{ ' $'.$total_promotion_disc }}"></div>
                            @endif
                        </td>
                        <td>
                            <p>{!! $tourorder->guest_detail !!}</p>
                        </td>
                        <td>
                            <p>{{ $tourorder->duration }}</p>
                            <p>{{ "Start: ".dateFormat($tourorder->travel_date) }}</p>
                            <p>{{ "End: ".dateFormat($tourorder->checkout) }}</p>
                        </td>
                        <td>
                            <p>{{ $tourorder->number_of_guests." pax" }}</p>
                            @if ($tourorder->discounts > 0 or $tourorder->bookingcode_disc > 0 or  $total_promotion_disc > 0)
                                <p class="normal-price">{{ '$ ' . number_format(($tourorder->price_total), 0, ',', '.') }}</p>
                            @endif
                            <p>{{ '$ ' . number_format(($tourorder->final_price), 0, ',', '.') }}</p>
                        </td>
                        <td class="text-right">
                            @php
                                $rsv_order = $reservations->where('id',$tourorder->rsv_id)->first();
                            @endphp
                            <div class="table-action">
                                @if ($tourorder->status == "Draft")
                                    <a href="{{ route('view.edit-order-tour',$tourorder->id) }}">
                                        <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                    </a>
                                    <form class="display-content" action="/delete-order/{{ $tourorder->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                        <button class="btn-delete" onclick="return confirm('@lang('messages.Are you sure?')');" type="submit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Delete')"><i class="icon-copy fa fa-trash"></i></button>
                                    </form>
                                @elseif ($tourorder->status == "Rejected")
                                    <a href="{{ route('view.detail-order-tour',$tourorder->id) }}">
                                        <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                    </a>
                                    <form class="display-content" action="/delete-order/{{ $tourorder->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                        <button class="btn-delete" onclick="return confirm('@lang('messages.Are you sure?')');" type="submit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Delete')"><i class="icon-copy fa fa-trash"></i></button>
                                    </form>
                                @elseif ($tourorder->status == "Confirmed" and $rsv_tour->send == "yes")
                                    <a href="{{ route('view.detail-order-tour',$tourorder->id) }}">
                                        <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                    </a>
                                    <form id="approveOrder" class="hidden" action="/fapprove-order-{{ $tourorder->id }}"method="post" enctype="multipart/form-data">
                                        @csrf
                                        @method('put')
                                    </form>
                                    <button type="submit" form="approveOrder" class="btn-approve" data-toggle="tooltip" data-placement="top" title="@lang('messages.Approve Order')"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i></button>
                                @else
                                    <a href="{{ route('view.detail-order-tour',$tourorder->id) }}">
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

