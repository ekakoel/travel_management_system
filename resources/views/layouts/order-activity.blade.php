<div class="col-md-12">
    <div class="card-box m-b-18">
        <div class="card-box-title">
            <div class="subtitle"><i class="fa fa-child"></i>@lang('messages.Activity Orders')</div>
        </div>
        <div class="input-container">
            <div class="input-group">
                <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                <input id="searchActivityOrderNo" type="text" onkeyup="searchActivityOrderNo()" class="form-control" name="search-active-order-byagn" placeholder="Order number">
            </div>
            <div class="input-group">
                <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                <input id="searchActivityByGuestName" type="text" onkeyup="searchActivityByGuestName()" class="form-control" name="search-active-order-type" placeholder="Guest name">
            </div>
        </div>
        <table id="tbActivityOrder" class="data-table table m-b-30">
            <thead>
                <tr>
                    <th data-priority="1" class="datatable-nosort" style="width: 5%;">@lang('messages.No')</th>
                    <th data-priority="1" class="datatable-nosort" style="width: 25%;">@lang('messages.Activity')</th>
                    <th data-priority="1" class="datatable-nosort" style="width: 25%;">@lang('messages.Guest Name')</th>
                    <th style="width: 25%;">@lang('messages.Duration')</th>
                    <th data-priority="1" class="datatable-nosort" style="width: 10%;">@lang('messages.Price')</th>
                    <th style="width: 10%;">@lang('messages.Action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($activityorders as $no => $activityorder)
                    @php
                        $rsv_activity = $reservations->where('id',$activityorder->rsv_id)->first();
                    @endphp
                    @if ($activityorder->promotion != "")
                        @php
                            $promotion_name = json_decode($activityorder->promotion);
                            $promotion_disc = json_decode($activityorder->promotion_disc);
                            $total_promotion_disc = array_sum($promotion_disc);
                            $cpn = count($promotion_name);
                        @endphp
                    @else
                        @php
                            $total_promotion_disc = 0;
                        @endphp
                    @endif
                    @if ($activityorder->status == "Rejected")
                        <tr style="background-color: #ffd4d4;">
                    @elseif($activityorder->status == "Confirmed" and $rsv_activity->send == "yes")
                        <tr data-toggle="tooltip" data-placement="top" title="This order requires your approval!" style="background-color: #fbffa9;">
                    @else
                        <tr>
                    @endif
                        <td>{{ ++$no }}</td>
                        <td>
                            <p class="p-0 m-0"><b>{{ $activityorder->orderno }}</b></p>
                            <p class="p-0 m-0">{{ $activityorder->subservice }}</p>
                            <p class="p-0 m-0">{{ $activityorder->servicename }}</p>
                            @if ($activityorder->status == "Rejected")
                                <div class="status-rejected inline-left p-r-8"></div>
                            @elseif ($activityorder->status == "Paid")
                                <div class="status-paid inline-left p-r-8"></div>
                            @elseif ($activityorder->status == "Approved")
                                <div class="status-approved inline-left p-r-8"></div>
                            @elseif ($activityorder->status == "Confirmed")
                                <div class="status-confirmed inline-left p-r-8"></div>
                            @elseif ($activityorder->status == "Invalid")
                                <div class="status-invalid inline-left p-r-8"></div>
                            @elseif ($activityorder->status == "Active")
                                <div class="status-progress inline-left p-r-8"></div>
                            @elseif ($activityorder->status == "Pending")
                                <div class="status-waiting inline-left p-r-8"></div>
                            @elseif ($activityorder->status == "Draft")
                                <div class="status-draft inline-left p-r-8"></div>
                            @endif
                            @if ($activityorder->discounts > 0)
                                <div class="status-discounts inline-left p-r-8" data-toggle="tooltip" data-placement="top" title="@lang('messages.Discount') ${{ $activityorder->discounts }}"></div>
                            @endif
                            @if ($activityorder->bookingcode_disc > 0)
                                <div class="status-bcode inline-left p-r-8" data-toggle="tooltip" data-placement="top" title="{{ $activityorder->bookingcode." ($".$activityorder->bookingcode_disc.")" }}"></div>
                            @endif
                                @if ($total_promotion_disc > 0)
                                <div class="status-promotion inline-left p-r-8" data-toggle="tooltip" data-placement="top" title="@lang('messages.Promotion'){{ ' $'.$total_promotion_disc }}"></div>
                            @endif
                        </td>
                        <td>
                            <p class="p-0 m-0">{{ $activityorder->guest_detail }}</p>
                        </td>
                        <td>
                            <p class="p-0 m-0">{{ $activityorder->number_of_guests." Pax" }}</p>
                            <p class="p-0 m-0">{{ $activityorder->duration }}</p>
                            <p class="p-0 m-0">{{ dateFormat($activityorder->travel_date) }}</p>
                        </td>
                        <td>
                            @if ($activityorder->discounts > 0 or $activityorder->bookingcode_disc > 0 or  $total_promotion_disc > 0)
                                <p class="normal-price">{{ '$ ' . number_format(($activityorder->price_total), 0, ',', '.') }}</p>
                            @endif
                            <p>{{ '$ ' . number_format($activityorder->final_price, 0, ',', '.') }}</p>
                        </td>
                        <td class="text-right">
                            @php
                                $rsv_order = $reservations->where('id',$activityorder->rsv_id)->first();
                            @endphp
                            <div class="table-action">
                                @if ($activityorder->status == "Draft")
                                    <a href="{{ route('view.edit-order-hotel',$activityorder->id) }}">
                                        <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                    </a>
                                    <form class="display-content" action="/delete-order/{{ $activityorder->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                        <button class="btn-delete" onclick="return confirm('@lang('messages.Are you sure?')');" type="submit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Delete')"><i class="icon-copy fa fa-trash"></i></button>
                                    </form>
                                @elseif ($activityorder->status == "Rejected")
                                    <a href="/detail-order-{{ $activityorder->id }}">
                                        <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                    </a>
                                    <form class="display-content" action="/delete-order/{{ $activityorder->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                        <button class="btn-delete" onclick="return confirm('@lang('messages.Are you sure?')');" type="submit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Delete')"><i class="icon-copy fa fa-trash"></i></button>
                                    </form>
                                @elseif ($activityorder->status == "Confirmed" and $rsv_activity->send == "yes")
                                    <a href="/detail-order-{{ $activityorder->id }}">
                                        <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                    </a>
                                    <form id="approveOrder" class="hidden" action="/fapprove-order-{{ $activityorder->id }}"method="post" enctype="multipart/form-data">
                                        @csrf
                                        @method('put')
                                    </form>
                                    <button type="submit" form="approveOrder" class="btn-approve" data-toggle="tooltip" data-placement="top" title="@lang('messages.Approve Order')"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i></button>
                                @else
                                    <a href="/detail-order-{{ $activityorder->id }}">
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