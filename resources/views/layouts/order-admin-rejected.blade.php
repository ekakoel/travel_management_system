 {{-- REJECTED ORDERS  ============================================================================================================================= --}}
 <div id="rejectedorders" class="card-box p-b-18 m-b-18">
    <div class="card-box-title">
        
            <div class="subtitle" ><i class="icon-copy fa fa-tags" aria-hidden="true"></i>Rejected Orders</div>
        
    </div>
    @if (count($rejectedorders) > 0)
        <table class="data-table table">
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
                @foreach ($rejectedorders as $rejectedorder)
                    @php
                        $agent_rejected = $users->where('id',$rejectedorder->user_id)->first()
                    @endphp
                    @if ($rejectedorder->promotion != "")
                        @php
                            $promotion_name = json_decode($rejectedorder->promotion);
                            $promotion_disc = json_decode($rejectedorder->promotion_disc);
                            $total_promotion_disc = array_sum(json_decode($rejectedorder->promotion_disc));
                            $cpn = count($promotion_name);
                        @endphp
                    @else
                        @php
                            $total_promotion_disc = 0;
                        @endphp
                    @endif
                    <tr>
                        <td>
                            <p>{{ $agent_rejected->name }}</p>
                            <p>{{ "@".$agent_rejected->office }}</p>
                        </td>
                        <td>
                            <b>{{ $rejectedorder->orderno }}</b>
                            <p>{{ $rejectedorder->service }}</p>
                            <div class="status-rejected inline-left">Confirmed</div>
                            @if ($rejectedorder->kick_back > 0)
                                <div class="status-kick-back inline-left" data-toggle="tooltip" data-placement="top" title="Kick Back ${{ $rejectedorder->kick_back }}"><i class="icon-copy fa fa-usd" aria-hidden="true"></i></div>
                            @endif
                            @if ($rejectedorder->discounts > 0)
                                <div class="status-discounts inline-left" data-toggle="tooltip" data-placement="top" title="Discounts ${{ $rejectedorder->discounts }}"><i class="icon-copy fa fa-percent" aria-hidden="true"></i></div>
                            @endif
                            @if ($rejectedorder->bookingcode_disc > 0)
                                <div class="status-bcode inline-left" data-toggle="tooltip" data-placement="top" title="{{ $rejectedorder->bookingcode." ($".$rejectedorder->bookingcode_disc.")" }}"><i class="icon-copy fa fa-qrcode" aria-hidden="true"></i></div>
                            @endif
                            @if ($total_promotion_disc > 0)
                                <div class="status-promotion inline-left" data-toggle="tooltip" data-placement="top" title="@lang('messages.Promotion'){{ '$'.$total_promotion_disc }}"><i class="icon-copy fa fa-tag" aria-hidden="true"></i></div>
                            @endif
                        </td>
                        <td>
                            {{-- Hotel ============================================================================================================ --}}
                            @if ($rejectedorder->service == 'Hotel')
                                {{ $rejectedorder->duration }} Night
                                <p>In:
                                    {{ dateFormat($rejectedorder->checkin) }}</p>
                                <p>Out:
                                    {{ dateFormat($rejectedorder->checkout) }}</p>
                            @elseif ($rejectedorder->service == 'Hotel Package')
                                {{ $rejectedorder->duration }} Night
                                <p>In:
                                    {{ dateFormat($rejectedorder->checkin) }}</p>
                                <p>Out:
                                    {{ dateFormat($rejectedorder->checkout) }}</p>
                            @elseif ($rejectedorder->service == 'Hotel Promo')
                                {{ $rejectedorder->duration }} Night
                                <p>In:
                                    {{ dateFormat($rejectedorder->checkin) }}</p>
                                <p>Out:
                                    {{ dateFormat($rejectedorder->checkout) }}</p>
                            @elseif ($rejectedorder->service == 'Private Villa')
                                {{ $rejectedorder->duration }} Night
                                <p>In:
                                    {{ dateFormat($rejectedorder->checkin) }}</p>
                                <p>Out:
                                    {{ dateFormat($rejectedorder->checkout) }}</p>
                            {{-- Tour ============================================================================================================ --}}
                            @elseif ($rejectedorder->service == 'Tour Package')
                                {{ $rejectedorder->duration }}
                                <p>Start:
                                    {{ dateFormat($rejectedorder->travel_date) }}</p>
                                <p>Ends:
                                    @if ($rejectedorder->duration == "1D")
                                        <?php $rejectedorder_end=date('Y-m-d H.i', strtotime("+10 hours", strtotime($rejectedorder->travel_date))); ?>
                                        {{ dateFormat($rejectedorder_end) }}
                                    @elseif ($rejectedorder->duration == "2D/1N")
                                        <?php $rejectedorder_end=date('Y-m-d H.i', strtotime("+34 hours", strtotime($rejectedorder->travel_date))); ?>
                                        {{ dateFormat($rejectedorder_end) }}
                                    @elseif ($rejectedorder->duration == "3D/2N")
                                        <?php $rejectedorder_end=date('Y-m-d H.i', strtotime("+58 hours", strtotime($rejectedorder->travel_date))); ?>
                                        {{ dateFormat($rejectedorder_end) }}
                                    @elseif ($rejectedorder->duration == "4D/3N")
                                        <?php $rejectedorder_end=date('Y-m-d H.i', strtotime("+82 hours", strtotime($rejectedorder->travel_date))); ?>
                                        {{ dateFormat($rejectedorder_end) }}
                                    @elseif ($rejectedorder->duration == "5D/4N")
                                        <?php $rejectedorder_end=date('Y-m-d H.i', strtotime("+106 hours", strtotime($rejectedorder->travel_date))); ?>
                                        {{ dateFormat($rejectedorder_end) }}
                                    @elseif ($rejectedorder->duration == "6D/5N")
                                        <?php $rejectedorder_end=date('Y-m-d H.i', strtotime("+130 hours", strtotime($rejectedorder->travel_date))); ?>
                                        {{ dateFormat($rejectedorder_end) }}
                                    @elseif ($rejectedorder->duration == "7D/6N")
                                        <?php $rejectedorder_end=date('Y-m-d H.i', strtotime("+154 hours", strtotime($rejectedorder->travel_date))); ?>
                                        {{ dateFormat($rejectedorder_end) }}
                                    @else
                                    @endif
                                </p>
                            {{-- Activity ============================================================================================================ --}}
                            @elseif ($rejectedorder->service == 'Activity')
                                {{ $rejectedorder->duration }} Hours
                                <p>Start:
                                    {{ dateFormat($rejectedorder->travel_date) }}</p>
                                <p>End:
                                    <?php
                                        $activity_duration = $rejectedorder->duration;
                                        $activity_end=date('Y-m-d H.i', strtotime('+'.$activity_duration.'hours', strtotime($rejectedorder->travel_date))); 
                                    ?>
                                    {{ dateFormat($activity_end) }}
                            {{-- Transport ============================================================================================================ --}}        
                            @elseif ($rejectedorder->service == 'Transport')
                                @if ($rejectedorder->duration == "1")
                                    {{ $rejectedorder->duration }} Day
                                @else
                                    {{ $rejectedorder->duration }} Days
                                @endif
                                <p>Pickup Date:{{ dateFormat($rejectedorder->travel_date) }}</p>
                                <p>Return Date:
                                    <?php
                                        $transport_duration = $rejectedorder->duration;
                                        $return_date=date('Y-m-d H.i', strtotime('+'.$transport_duration.'days', strtotime($rejectedorder->travel_date))); 
                                    ?>
                                    {{ dateFormat($return_date) }}</p>
                            @else
                                Not Listed
                            @endif
                        </td>
                        <td>
                            @if ($rejectedorder->service == "Wedding Package")
                                {{ $rejectedorder->groom_name." & ".$rejectedorder->bride_name }}<br>
                                {{ $rejectedorder->number_of_guests." Invitations" }}
                            @elseif($rejectedorder->service == "Private Villa")
                                {{ $rejectedorder->number_of_guests." guests" }}
                            @else
                                @php
                                    $gst = json_decode($rejectedorder->guest_detail);
                                    $ar = is_array($gst);
                                @endphp
                                @if ($ar == 1)
                                    @php
                                        $guests = implode(', ',$gst);
                                    @endphp
                                    {!! $guests !!}
                                @else
                                    {!! $rejectedorder->guest_detail !!}
                                @endif
                            @endif
                        </td>
                        <td>
                            @if ($rejectedorder->kick_back > 0 or $rejectedorder->discounts > 0 or $rejectedorder->bookingcode_disc > 0 or  $total_promotion_disc > 0)
                                @if (isset($rejectedorder->kick_back))
                                    @if ($rejectedorder->optional_price != "")
                                        <p class="normal-price">{{ '$ ' . number_format(($rejectedorder->price_total + $rejectedorder->kick_back + $rejectedorder->optional_price), 0, ',', '.') }}</p>
                                    @else
                                        <p class="normal-price">{{ '$ ' . number_format(($rejectedorder->price_total + $rejectedorder->kick_back), 0, ',', '.') }}</p>
                                    @endif

                                @else
                                    <p class="normal-price">{{ '$ ' . number_format(($rejectedorder->price_total), 0, ',', '.') }}</p>
                                @endif
                            @endif
                            {{ '$ ' . number_format($rejectedorder->final_price, 0, ',', '.') }}
                        </td>
                        {{-- End Status =========================================================================================================== --}}
                        <td class="text-right">
                            <div class="table-action">
                                <a href="/orders-admin-{{ $rejectedorder->id }}"  data-toggle="tooltip" data-placement="top" title="Detail Order"> 
                                    <button class="btn-view"><i class="icon-copy fa fa-eye" aria-hidden="true"></i></button>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-data text-center"><i>No orders are currently rejected!</i></div>
    @endif
</div>