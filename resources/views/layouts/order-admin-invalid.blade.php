{{-- INVALID ORDERS  ============================================================================================================================= --}}
<div id="invalid-orders" class="card-box m-b-18">
    <div class="card-box-title">
        <div class="subtitle"> <i class="icon-copy fa fa-tags" aria-hidden="true"></i>Invalid Orders</div>
    </div>
    @if (count($invalidorders) > 0)
    <table class="data-table table table-hover table-condensed">
        <thead>
            <tr>
                <th style="width: 10%;">Agent</th>
                <th style="width: 20%;">Orders</th>
                <th style="width: 20%;">Duration</th>
                <th style="width: 30%;">Guest</th>
                <th style="width: 10%;">Price</th>
                <th style="width: 10%;">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invalidorders as $no => $invalidorder)
                @php
                    $agent_invalid = $users->where('id',$invalidorder->user_id)->first()
                @endphp
                @if ($invalidorder->promotion != "")
                    @php
                        $promotion_name = json_decode($invalidorder->promotion);
                        $promotion_disc = json_decode($invalidorder->promotion_disc);
                        $total_promotion_disc = array_sum($promotion_disc);
                        $cpn = count($promotion_name);
                    @endphp
                @else
                    @php
                        $total_promotion_disc = 0;
                    @endphp
                @endif
                <tr>
                    <td>
                        <p>{{ $agent_invalid->name }}</p>
                        <p>{{ "@".$agent_invalid->office }}</p>
                    </td>
                    <td>
                        <b>{{ $invalidorder->orderno }}</b>
                        <p>{{ $invalidorder->service }}</p>
                        <div class="status-invalid inline-left"></div>
                            
                        @if ($invalidorder->kick_back > 0)
                            <div class="status-kick-back inline-left" data-toggle="tooltip" data-placement="top" title="Kick Back ${{ $invalidorder->kick_back }}"><i class="icon-copy fa fa-usd" aria-hidden="true"></i></div>
                        @endif
                        @if ($invalidorder->discounts > 0)
                            <div class="status-discounts inline-left" data-toggle="tooltip" data-placement="top" title="Discounts ${{ $invalidorder->discounts }}"><i class="icon-copy fa fa-percent" aria-hidden="true"></i></div>
                        @endif
                        @if ($invalidorder->bookingcode_disc > 0)
                            <div class="status-bcode inline-left" data-toggle="tooltip" data-placement="top" title="{{ $invalidorder->bookingcode." ($".$invalidorder->bookingcode_disc.")" }}"><i class="icon-copy fa fa-qrcode" aria-hidden="true"></i></div>
                        @endif
                        @if ($total_promotion_disc > 0)
                            <div class="status-promotion inline-left" data-toggle="tooltip" data-placement="top" title="@lang('messages.Promotion'){{ '$'.$total_promotion_disc }}"><i class="icon-copy fa fa-tag" aria-hidden="true"></i></div>
                        @endif
                    </td>
                    <td>
                        {{-- Hotel ============================================================================================================ --}}
                        @if ($invalidorder->service == 'Hotel')
                            {{ $invalidorder->duration }} Night
                            <p>In:
                                {{ dateFormat($invalidorder->checkin) }}</p>
                            <p>Out:
                                {{ dateFormat($invalidorder->checkout) }}</p>
                        @elseif ($invalidorder->service == 'Hotel Package')
                            {{ $invalidorder->duration }} Night
                            <p>In:
                                {{ dateFormat($invalidorder->checkin) }}</p>
                            <p>Out:
                                {{ dateFormat($invalidorder->checkout) }}</p>
                        @elseif ($invalidorder->service == 'Hotel Promo')
                            {{ $invalidorder->duration }} Night
                            <p>In:
                                {{ dateFormat($invalidorder->checkin) }}</p>
                            <p>Out:
                                {{ dateFormat($invalidorder->checkout) }}</p>
                        @elseif ($invalidorder->service == 'Private Villa')
                            {{ $invalidorder->duration }} Night
                            <p>In:
                                {{ dateFormat($invalidorder->checkin) }}</p>
                            <p>Out:
                                {{ dateFormat($invalidorder->checkout) }}</p>
                        {{-- Tour ============================================================================================================ --}}
                        @elseif ($invalidorder->service == 'Tour Package')
                            {{ $invalidorder->duration }}
                            <p>Start:{{ dateFormat($invalidorder->checkin) }}</p>
                            <p>End:{{ dateFormat($invalidorder->checkout) }}</p>
                        {{-- Activity ============================================================================================================ --}}
                        @elseif ($invalidorder->service == 'Activity')
                            {{ $invalidorder->duration }} Hours
                            <p>Start:
                                {{ dateTimeFormat($invalidorder->travel_date) }}</p>
                            <p>End:
                                <?php
                                    $activity_duration = $invalidorder->duration;
                                    $activity_end=date('Y-m-d H.i', strtotime('+'.$activity_duration.'hours', strtotime($invalidorder->travel_date))); 
                                ?>
                                {{ dateTimeFormat($activity_end) }}
                        {{-- Transport ============================================================================================================ --}}        
                        @elseif ($invalidorder->service == 'Transport')
                            @if ($invalidorder->duration == "1")
                                {{ $invalidorder->duration }} Day
                            @else
                                {{ $invalidorder->duration }} Days
                            @endif
                            <p>Pickup Date:{{ dateTimeFormat($invalidorder->travel_date) }}</p>
                            <p>Return Date:
                                <?php
                                    $transport_duration = $invalidorder->duration;
                                    $return_date=date('Y-m-d H.i', strtotime('+'.$transport_duration.'days', strtotime($invalidorder->travel_date))); 
                                ?>
                                {{ dateTimeFormat($return_date) }}</p>
                        @else
                            Not Listed
                        @endif
                    </td>
                    <td>
                        @if ($invalidorder->service == "Wedding Package")
                            {{ $invalidorder->groom_name." & ".$invalidorder->bride_name }}<br>
                            {{ $invalidorder->number_of_guests." Invitations" }}
                        @elseif($invalidorder->service == "Private Villa")
                            {{ $invalidorder->number_of_guests." guests" }}
                        @else
                            @php
                                $gst = json_decode($invalidorder->guest_detail);
                                $ar = is_array($gst);
                            @endphp
                            @if ($ar == 1)
                                @php
                                    $guests = implode(', ',$gst);
                                @endphp
                                {!! $guests !!}
                            @else
                                {!! $invalidorder->guest_detail !!}
                            @endif
                        @endif
                    </td>
                    <td>
                        @if ($invalidorder->kick_back > 0 or $invalidorder->discounts > 0 or $invalidorder->bookingcode_disc > 0 or  $total_promotion_disc > 0)
                            @if (isset($invalidorder->kick_back))
                                @if ($invalidorder->optional_price != "")
                                    <p class="normal-price">{{ '$ ' . number_format(($invalidorder->price_total + $invalidorder->kick_back + $invalidorder->optional_price), 0, ',', '.') }}</p>
                                @else
                                    <p class="normal-price">{{ '$ ' . number_format(($invalidorder->price_total + $invalidorder->kick_back), 0, ',', '.') }}</p>
                                @endif

                            @else
                                <p class="normal-price">{{ '$ ' . number_format(($invalidorder->price_total), 0, ',', '.') }}</p>
                            @endif
                        @endif
                        {{ '$ ' . number_format($invalidorder->final_price, 0, ',', '.') }}
                    </td>
                    {{-- End Status =========================================================================================================== --}}
                    <td class="text-right">
                        <div class="table-action">
                            <a href="/orders-admin-{{ $invalidorder->id }}"  data-toggle="tooltip" data-placement="top" title="Detail Order"> 
                                <button class="btn-view"><i class="icon-copy fa fa-eye" aria-hidden="true"></i></button>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <div class="empty-data text-center"><i>No orders are currently invalid!</i></div>
    @endif  
</div>