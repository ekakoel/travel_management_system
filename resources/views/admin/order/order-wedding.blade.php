
<div id="weddingOrders" class="card-box m-b-18">
    <div class="card-box-title">
        <div class="subtitle"><i class="icon-copy fa fa-tags" aria-hidden="true"></i> Wedding Orders</div>
    </div>
    <div class="input-container">
        <div class="input-group">
            <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
            <input id="searchWeddingOrdersByAgn" type="text" onkeyup="searchWeddingOrdersByAgn()" class="form-control" name="search-waiting-order-byagn" placeholder="Search by agent...">
        </div>
        <div class="input-group">
            <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
            <input id="searchWeddingOrderByBride" type="text" onkeyup="searchWeddingOrderByBride()" class="form-control" name="search-waiting-order-type" placeholder="Search by brides...">
        </div>
    </div>
    @if (count($orderWeddings) > 0)
    @php
        $validOrderWeddings = $orderWeddings->where('checkin','>=',$now);
    @endphp
        @if ($validOrderWeddings)
            
            <table id="tbWeddings" class="data-table table table-hover nowrap table-condensed">
                <thead>
                    <tr>
                        <th style="width: 10%;">Agent</th>
                        <th style="width: 20%;">Orders</th>
                        <th style="width: 20%;">Bride's</th>
                        <th style="width: 30%;">Services</th>
                        <th style="width: 10%;">Price</th>
                        <th style="width: 10%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($validOrderWeddings as $order_wedding)
                        @php
                            $additional_charges = $additionalCharges->where('order_wedding_id',$order_wedding->id)->where('status','!=','Rejected');
                            $additional_charge_price = $additional_charges->pluck('price')->sum();
                            $additionalChargeContainZero = $additional_charges->contains('price',0);

                            $transports_orders = $transports->where('order_wedding_id',$order_wedding->id);
                            $transportContainsNullPrice = $transports_orders->contains('price',0);

                            $wedding_accommodations = $weddingAccommodations->where('order_wedding_package_id',$order_wedding->id)->where('room_for','Inv');
                            $accommodation_containt_zero = $wedding_accommodations->contains('public_rate',0);
                            $wedding_accommodation_price = $wedding_accommodations->pluck('public_rate')->sum();
                            $reservation = $reservations->where('id',$order_wedding->rsv_id)->first();
                            if ($reservation) {
                                $invoice = $invoices->where('rsv_id',$reservation->id)->first();
                                if ($invoice) {
                                    $bank = $banks->where('id',$invoice->bank_id)->first();
                                }else{
                                    $bank = NULL;
                                }
                            }else{
                                $invoice = NULL;
                                $bank = NULL;
                            }
                        @endphp
                            @if ($order_wedding->status == "Pending" or $order_wedding->status == "Process" or $order_wedding->status == "Approved" or $order_wedding->status == "Paid")
                                @php
                                    $agent = $order_wedding->agent;
                                @endphp
                                <tr>
                                    <td>
                                        <b>{{ date("m/d/y",strtotime($order_wedding->created_at)) }}</b>
                                        @if ($order_wedding->agent)
                                            <p>{{ $order_wedding->agent->name }}</p>
                                            <p>{{ "@".$order_wedding->agent->office }}</p>
                                        @endif
                                        
                                    </td>
                                    <td>
                                        <b>{{ $order_wedding->orderno }}</b>
                                        <p>{{ $order_wedding->hotel->name }}</p>
                                        <p>{{ $order_wedding->service }}</p>
                                        @if ($order_wedding->status == "Rejected")
                                            <div class="status-rejected inline-left p-r-8"></div>
                                        @elseif ($order_wedding->status == "Paid")
                                            <div class="status-paid inline-left p-r-8"><i class="icon-copy ion-android-checkmark-circle"></i></div>
                                        @elseif ($order_wedding->status == "Approved")
                                            <div class="status-approved inline-left p-r-8"><i class="icon-copy fa fa-check-circle-o" aria-hidden="true"></i></div>
                                        @elseif ($order_wedding->status == "Confirmed")
                                            <div class="status-confirmed inline-left p-r-8"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i></div>
                                            <p><i class="color-blue">@lang('messages.Awaiting Payment')</i></p>
                                        @elseif ($order_wedding->status == "Invalid")
                                            <div class="status-invalid inline-left p-r-8"><i class="icon-copy fa fa-close" aria-hidden="true"></i></div>
                                        @elseif ($order_wedding->status == "Active")
                                            <div class="status-progress inline-left p-r-8"><i class="icon-copy fa fa-clock-o" aria-hidden="true"></i></div>
                                        @elseif ($order_wedding->status == "Pending")
                                            <div class="status-waiting inline-left p-r-8"><span class="icon-copy ti-alarm-clock"></span></div>
                                        @elseif ($order_wedding->status == "Draft")
                                            <div class="status-draft inline-left p-r-8"><span class="icon-copy fa fa-pencil"></span></div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order_wedding->bride)
                                            <p>{{ $order_wedding->bride->groom." & ".$order_wedding->bride->bride }}</p>
                                            @if ($order_wedding->bride->groom_chinese and $order_wedding->bride->bride_chinese)
                                                <p>{{ $order_wedding->bride->groom_chinese." & ".$order_wedding->bride->bride_chinese }}</p>
                                            @endif
                                            <p>{{ $order_wedding->number_of_invitation }} @lang('messages.Invitations')</p>
                                            @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order_wedding->room_bride_id or $order_wedding->room_invitations_id or $order_wedding->ceremony_venue_id or $order_wedding->ceremony_venue_decoration_id or $order_wedding->reception_venue_id
                                        or $order_wedding->reception_venue_decoration_id or $order_wedding->makeup_id or $order_wedding->documentation_id or $order_wedding->entertaintment_id or $order_wedding->additional_services
                                        or $order_wedding->transports_id)
                                            @if ($order_wedding->room_bride_id)
                                                <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i> </span> @lang('messages.Suite & Villa') (@lang('messages.Bride')) <span></p>
                                            @endif
                                            @if ($order_wedding->room_invitations_id)
                                                <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i> </span> @lang('messages.Suite & Villa') (@lang('messages.Invitations')) <span></p>
                                            @endif
                                            @if ($order_wedding->ceremony_venue_id)
                                                <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i> </span> @lang('messages.Ceremony Venue') <span></p>
                                            @endif
                                            @if ($order_wedding->ceremony_venue_decoration_id)
                                                <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i> </span> @lang('messages.Ceremony Venue Decoration') <span></p>
                                            @endif
                                            @if ($order_wedding->reception_venue_id)
                                                <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i> </span> @lang('messages.Reception Venue') <span></p>
                                            @endif
                                            @if ($order_wedding->reception_venue_decoration_id)
                                                <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i> </span> @lang('messages.Reception Venue Decoration') <span></p>
                                            @endif
                                            @if ($order_wedding->makeup_id)
                                                <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i></span> @lang('messages.Make-up') <span></p>
                                            @endif
                                            @if ($order_wedding->documentation_id)
                                                <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i></span> @lang('messages.Documentation') <span></p>
                                            @endif
                                            @if ($order_wedding->entertaintment_id)
                                                <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i></span> @lang('messages.Entertainments') <span></p>
                                            @endif
                                            @if ($order_wedding->additional_services)
                                                <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i></span> @lang('messages.Additional Services') <span></p>
                                            @endif
                                            @if ($order_wedding->transports_id)
                                                <p class="p-0 m-0"><span><i class="icon-copy fi-checkbox"></i></span> @lang('messages.Transport') <span></p>
                                            @endif
                                        @else
                                            -
                                        @endif
                                        
                                    </td>
                                    <td>
                                        @if ($transportContainsNullPrice or $accommodation_containt_zero or $additionalChargeContainZero)
                                            <div class="usd-rate" data-toggle="tooltip" data-placement="left" title="@lang('messages.To be advised')">TBA</div>
                                        @else
                                        {{ '$ ' . number_format($order_wedding->final_price, 0, ',', '.') }} <i>(USD)</i><br>
                                            @if ($bank)
                                                @if ($bank->currency == "IDR")
                                                    {{ 'Rp ' . number_format($invoice->total_idr, 0, ',', '.') }} <i>(IDR)</i>
                                                @elseif($bank->currency == "TWD")
                                                    {{ '$ ' . number_format($invoice->total_twd, 0, ',', '.') }} <i>(TWD)</i>
                                                @elseif($bank->currency == "CNY")
                                                    {{ '¥ ' . number_format($invoice->total_cny, 0, ',', '.') }} <i>(CNY)</i>
                                                @endif
                                            @endif
                                        @endif
                                    </td>

                                    <td class="text-right">
                                        <div class="table-action">
                                            <a href="/validate-orders-wedding-{{ $order_wedding->id }}"  data-toggle="tooltip" data-placement="top" title="Detail Order">
                                                <button class="btn-view"><i class="icon-copy fa fa-eye" aria-hidden="true"></i></button>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                    @endforeach
                </tbody>
            </table>
        @endif
    @else
        <div class="empty-data text-center"><i>No orders are currently pending!</i></div>
    @endif
</div>