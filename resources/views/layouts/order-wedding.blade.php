<div class="col-md-12 m-b-18">
    <div class="card-box p-b-18">
        <div class="card-box-title">
            <div class="subtitle"><i class="icon-copy fi-torso-business"></i><i class="icon-copy fi-torso-female"></i> Wedding Orders</div>
        </div>
        <div class="input-container">
            <div class="input-group">
                <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                <input id="searchWedOrderNo" type="text" onkeyup="searchWedOrderNo()" class="form-control" name="search-active-order-byagn" placeholder="Order number">
            </div>
            <div class="input-group">
                <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                <input id="searchByBrides" type="text" onkeyup="searchByBrides()" class="form-control" name="search-active-order-type" placeholder="Bride's">
            </div>
        </div>
        <table id="tblwd" class="data-table table nowrap m-b-30">
            <thead>
                <tr>
                    <th style="width: 5%;">@lang('messages.Date')</th>
                    <th style="width: 25%;">@lang('messages.Order')</th>
                    <th style="width: 20%;">@lang("messages.Bride's")</th>
                    <th style="width: 40%;">@lang('messages.Services')</th>
                    <th style="width: 10%;">@lang('messages.Action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($weddingorders as $no => $weddingorder)
                    @php
                        $order_wedding = $wedding_order->where('id',$weddingorder->wedding_order_id)->first();
                        $wedding_brides = $brides->where('id',$weddingorder->brides_id)->first();
                    @endphp
                    <tr>
                        <td>
                            {{ date("m/d/y",strtotime($weddingorder->created_at)) }}<br>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col-sm-12">
                                    <p class="p-0 m-0"><b>{{ $weddingorder->orderno }}</b></p>
                                    <p>{{ $weddingorder->service }}</p>
                                    @if (isset($statusMap[$weddingorder->status]))
                                        <div class="{{ $statusMap[$weddingorder->status]['class'] }} inline-left">
                                            {!! $statusMap[$weddingorder->status]['label'] !!}
                                        </div>
                                    @endif
                                    @if ($weddingorder->discounts > 0)
                                        <div class="status-discounts inline-left" data-toggle="tooltip" data-placement="top" title="{{ __('messages.Discounts')." $". $weddingorder->discounts }}"><i class="icon-copy fa fa-percent" aria-hidden="true"></i></div>
                                    @endif
                                    @if ($weddingorder->bookingcode_disc > 0)
                                        <div class="status-bcode inline-left" data-toggle="tooltip" data-placement="top" title="{{ __('messages.Booking code discounts')." $".$weddingorder->bookingcode_disc }}"><i class="icon-copy fa fa-percent" aria-hidden="true"></i></div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="p-0 m-0">{{ $wedding_brides->groom." & ".$wedding_brides->bride }}</p>
                            @if ($weddingorder->service == "Reception Venue")
                                <p>{{ date("m/d/y",strtotime($weddingorder->reception_date_start)) }} (18:00)</p>
                            @elseif ($weddingorder->service == "Ceremony Venue")
                                <p>{{ date("m/d/y",strtotime($weddingorder->wedding_date)) }} ({{ date('H.i',strtotime($weddingorder->slot)) }})</p>
                            @endif
                        </td>
                        <td>
                            @if ($weddingorder->room_bride_id or $weddingorder->room_invitations_id or $weddingorder->ceremony_venue_id or $weddingorder->ceremony_venue_decoration_id or $weddingorder->reception_venue_id
                            or $weddingorder->reception_venue_decoration_id or $weddingorder->makeup_id or $weddingorder->documentation_id or $weddingorder->entertaintment_id or $weddingorder->additional_services
                            or $weddingorder->transports_id)
                                @if ($weddingorder->room_bride_id)
                                    <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i> </span> @lang('messages.Suite & Villa') (@lang('messages.Bride')): <span></p>
                                @endif
                                @if ($weddingorder->room_invitations_id)
                                    <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i> </span> @lang('messages.Suite & Villa') (@lang('messages.Invitations')): <span></p>
                                @endif
                                @if ($weddingorder->ceremony_venue_id)
                                    <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i> </span> @lang('messages.Ceremony Venue'): <span></p>
                                @endif
                                @if ($weddingorder->ceremony_venue_decoration_id)
                                    <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i> </span> @lang('messages.Ceremony Venue Decoration'): <span></p>
                                @endif
                                @if ($weddingorder->reception_venue_id)
                                    <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i> </span> @lang('messages.Reception Venue'): <span></p>
                                @endif
                                @if ($weddingorder->reception_venue_decoration_id)
                                    <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i> </span> @lang('messages.Reception Venue Decoration'): <span></p>
                                @endif
                                @if ($weddingorder->makeup_id)
                                    <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i></span> @lang('messages.Make-up'): <span></p>
                                @endif
                                @if ($weddingorder->documentation_id)
                                    <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i></span> @lang('messages.Documentation'): <span></p>
                                @endif
                                @if ($weddingorder->entertaintment_id)
                                    <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i></span> @lang('messages.Entertainments'): <span></p>
                                @endif
                                @if ($weddingorder->additional_services)
                                    <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i></span> @lang('messages.Additional Services'): <span></p>
                                @endif
                                @if ($weddingorder->transports_id)
                                    <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i></span> @lang('messages.Transport'): <span></p>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="table-action">
                                @if ($weddingorder->status == "Draft")
                                    <a href="/edit-order-wedding-{{ $weddingorder->orderno }}">
                                        <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                    </a>
                                    <form class="display-content" action="/delete-wedding-order/{{ $weddingorder->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                        <button class="btn-delete" onclick="return confirm('@lang('messages.Are you sure?')');" type="submit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Delete')"><i class="icon-copy fa fa-trash"></i></button>
                                    </form>
                                @elseif ($weddingorder->status == "Rejected")
                                    <a href="/detail-order-wedding-{{ $weddingorder->orderno }}">
                                        <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                    </a>
                                    <form class="display-content" action="/delete-wedding-order/{{ $weddingorder->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                        <button class="btn-delete" onclick="return confirm('@lang('messages.Are you sure?')');" type="submit" data-toggle="tooltip" data-placement="top" title="@lang('messages.Delete')"><i class="icon-copy fa fa-trash"></i></button>
                                    </form>
                                @elseif ($weddingorder->status == "Confirmed" and $rsv_tour->send == "yes")
                                    <a href="/detail-order-wedding-{{ $weddingorder->orderno }}">
                                        <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                    </a>
                                    <form id="approveOrder" class="hidden" action="/fapprove-order-{{ $weddingorder->id }}"method="post" enctype="multipart/form-data">
                                        @csrf
                                        @method('put')
                                    </form>
                                    <button type="submit" form="approveOrder" class="btn-approve" data-toggle="tooltip" data-placement="top" title="@lang('messages.Approve Order')"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i></button>
                                @else
                                    <a href="/detail-order-wedding-{{ $weddingorder->orderno }}">
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