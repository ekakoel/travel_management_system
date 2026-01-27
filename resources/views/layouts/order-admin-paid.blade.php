<div id="paidorders" class="card-box m-b-18">
    <div class="card-box-title">
        <div class="subtitle"><i class="icon-copy fa fa-tags" aria-hidden="true"></i>Paid Orders</div>
    </div>
    <div class="input-container">
        <div class="input-group">
            <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
            <input id="searchPaidOrderByAgn" type="text" onkeyup="searchPaidOrderByAgn()" class="form-control" name="search-active-order-byagn" placeholder="Search by agent">
        </div>
        <div class="input-group">
            <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
            <input id="searchPaidOrderByTyp" type="text" onkeyup="searchPaidOrderByTyp()" class="form-control" name="search-active-order-type" placeholder="Search by order no">
        </div>
    </div>
    @if (count($paidorders) > 0)
        <table id="tbPaidOrders" class="data-table table table-hover table-condensed">
            <thead>
                <tr>
                    <th style="width: 10%;">Agent</th>
                    <th style="width: 20%;">Orders</th>
                    <th style="width: 20%;">Duration</th>
                    <th style="width: 30%;">Guests</th>
                    <th style="width: 10%;">Price</th>
                    <th style="width: 10%;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($paidorders as $paidorder)
                    @if ($paidorder->promotion != "")
                        @php
                            $promotion_name = json_decode($paidorder->promotion);
                            $promotion_disc = json_decode($paidorder->promotion_disc);
                            $total_promotion_disc = array_sum($promotion_disc);
                            $cpn = count($promotion_name);
                        @endphp
                    @else
                        @php
                            $total_promotion_disc = 0;
                        @endphp
                    @endif
                    @php
                        $agent_active = $users->where('id',$paidorder->user_id)->first();
                    @endphp
                    <tr>
                        <td>
                            <p>{{ $agent_active->name }}</p>
                            <p>{{ "@".$agent_active->office }}</p>
                        </td>
                        <td>
                            
                            <b>{{ $paidorder->orderno }}</b>
                            <p>{{ $paidorder->service }}</p>
                            <div class="status-paid inline-left"></div>
                            
                            @if ($paidorder->kick_back > 0)
                                <div class="status-kick-back inline-left" data-toggle="tooltip" data-placement="top" title="Kick Back ${{ $paidorder->kick_back }}"><i class="icon-copy fa fa-usd" aria-hidden="true"></i></div>
                            @endif
                            @if ($paidorder->discounts > 0)
                                <div class="status-discounts inline-left" data-toggle="tooltip" data-placement="top" title="Discounts ${{ $paidorder->discounts }}"><i class="icon-copy fa fa-percent" aria-hidden="true"></i></div>
                            @endif
                            @if ($paidorder->bookingcode_disc > 0)
                                <div class="status-bcode inline-left" data-toggle="tooltip" data-placement="top" title="{{ $paidorder->bookingcode." ($".$paidorder->bookingcode_disc.")" }}"><i class="icon-copy fa fa-qrcode" aria-hidden="true"></i></div>
                            @endif
                            @if ($total_promotion_disc > 0)
                                <div class="status-promotion inline-left" data-toggle="tooltip" data-placement="top" title="@lang('messages.Promotion'){{ '$'.$total_promotion_disc }}"><i class="icon-copy fa fa-tag" aria-hidden="true"></i></div>
                            @endif
                        </td>
                        <td>
                            {{-- Hotel ============================================================================================================ --}}
                            @if ($paidorder->service == 'Hotel')
                                {{ $paidorder->duration }} Night
                                <p>In:
                                    {{ dateFormat($paidorder->checkin) }}</p>
                                <p>Out:
                                    {{ dateFormat($paidorder->checkout) }}</p>
                            @elseif ($paidorder->service == 'Hotel Package')
                                {{ $paidorder->duration }} Night
                                <p>In:
                                    {{ dateFormat($paidorder->checkin) }}</p>
                                <p>Out:
                                    {{ dateFormat($paidorder->checkout) }}</p>
                            @elseif ($paidorder->service == 'Hotel Promo')
                                {{ $paidorder->duration }} Night
                                <p>In:
                                    {{ dateFormat($paidorder->checkin) }}</p>
                                <p>Out:
                                    {{ dateFormat($paidorder->checkout) }}</p>
                            @elseif ($paidorder->service == 'Private Villa')
                                {{ $paidorder->duration }} Night
                                <p>In:
                                    {{ dateFormat($paidorder->checkin) }}</p>
                                <p>Out:
                                    {{ dateFormat($paidorder->checkout) }}</p>
                            {{-- Tour ============================================================================================================ --}}
                            @elseif ($paidorder->service == 'Tour Package')
                                {{ $paidorder->duration }}
                                <p>Start:{{ dateFormat($paidorder->checkin) }}</p>
                                <p>End:{{ dateFormat($paidorder->checkout) }}</p>
                            {{-- Activity ============================================================================================================ --}}
                            @elseif ($paidorder->service == 'Activity')
                                {{ $paidorder->duration }} Hours
                                <p>Start:
                                    {{ dateTimeFormat($paidorder->travel_date) }}</p>
                                <p>End:
                                    <?php
                                        $activity_duration = $paidorder->duration;
                                        $activity_end=date('Y-m-d H.i', strtotime('+'.$activity_duration.'hours', strtotime($paidorder->travel_date))); 
                                    ?>
                                    {{ dateTimeFormat($activity_end) }}
                            {{-- Transport ============================================================================================================ --}}        
                            @elseif ($paidorder->service == 'Transport')
                                @if ($paidorder->duration == "1")
                                    {{ $paidorder->duration }} Day
                                @else
                                    {{ $paidorder->duration }} Days
                                @endif
                                <p>Pickup Date:{{ dateTimeFormat($paidorder->travel_date) }}</p>
                                <p>Return Date:
                                    <?php
                                        $transport_duration = $paidorder->duration;
                                        $return_date=date('Y-m-d H.i', strtotime('+'.$transport_duration.'days', strtotime($paidorder->travel_date))); 
                                    ?>
                                    {{ dateTimeFormat($return_date) }}</p>
                            @else
                                Not Listed
                            @endif
                        </td>
                        <td>
                            @if ($paidorder->service == "Wedding Package")
                                {{ $paidorder->groom_name." & ".$paidorder->bride_name }}<br>
                                {{ $paidorder->number_of_guests." Invitations" }}
                            @elseif($paidorder->service == "Private Villa")
                                {{ $paidorder->number_of_guests." guests" }}
                            @else
                                @php
                                    $gst = json_decode($paidorder->guest_detail);
                                    $ar = is_array($gst);
                                @endphp
                                @if ($ar == 1)
                                    @php
                                        $guests = implode(', ',$gst);
                                    @endphp
                                    {!! $guests !!}
                                @else
                                    {!! $paidorder->guest_detail !!}
                                @endif
                            @endif

                        </td>
                        <td>
                            @if ($paidorder->kick_back > 0 or $paidorder->discounts > 0 or $paidorder->bookingcode_disc > 0 or  $total_promotion_disc > 0)
                                <p class="normal-price">{{ '$ ' . number_format(($paidorder->final_price + $paidorder->kick_back + $paidorder->discounts + $paidorder->bookingcode_disc + $total_promotion_disc), 0, ',', '.') }}</p>
                            @endif
                            {{ '$ ' . number_format($paidorder->final_price, 0, ',', '.') }}
                        </td>
                        {{-- End Status =========================================================================================================== --}}
                        <td class="text-right">
                            <div class="table-action">
                                @if ($paidorder == "Paid")
                                    <i class="icon-copy dw dw-price-tag color-green" data-toggle="tooltip" data-placement="top" title="With Payment Receipt"></i>
                                @endif
                                <a href="/orders-admin-{{ $paidorder->id }}"  data-toggle="tooltip" data-placement="top" title="Detail Order"> 
                                    <button class="btn-view"><i class="icon-copy fa fa-eye" aria-hidden="true"></i></button>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-data text-center"><i>No orders are currently active!</i></div>
    @endif
    
</div>