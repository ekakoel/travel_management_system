@php
    use Carbon\Carbon;
@endphp
<div id="confirmedorders" class="card-box m-b-18">
    <div class="card-box-title">
        <div class="subtitle"><i class="icon-copy fa fa-tags" aria-hidden="true"></i>Confirmed Orders</div>
    </div>
    <div class="input-container">
        <div class="input-group">
            <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
            <input id="searchConfirmedOrderByAgn" type="text" onkeyup="searchConfirmedOrderByAgn()" class="form-control" name="search-active-order-byagn" placeholder="Search by agent">
        </div>
        <div class="input-group">
            <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
            <input id="searchConfirmedOrderByType" type="text" onkeyup="searchConfirmedOrderByType()" class="form-control" name="search-active-order-type" placeholder="Search by order no">
        </div>
    </div>
    @if (count($confirmedorders) > 0)
    
        <table id="tbConfirmedOrders" class="data-table table table-hover table-condensed">
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
                @foreach ($confirmedorders as $confirmedorder)
                    @if ($confirmedorder->promotion != "")
                        @php
                            $promotion_name = json_decode($confirmedorder->promotion);
                            $promotion_disc = json_decode($confirmedorder->promotion_disc);
                            $total_promotion_disc = array_sum($promotion_disc);
                            $cpn = count($promotion_name);
                        @endphp
                    @else
                        @php
                            $total_promotion_disc = 0;
                        @endphp
                    @endif
                    @php
                        $agent_active = $users->where('id',$confirmedorder->user_id)->first();
                        $order_id = $confirmedorder->id;
                        $invoice = $invoices->where('rsv_id',$confirmedorder->rsv_id)->first();
                        $due_date = Carbon::parse($invoice->due_date);
                        $final_date = Carbon::parse($now);
                        $cin = Carbon::parse($confirmedorder->checkin);
                        $day_limit = $due_date->diffInDays($final_date);
                        $release_limit = $final_date->diffInDays($cin);
                    @endphp
                    @if ($release_limit < 4)
                        <tr class="background-reconfirm-2" data-toggle="tooltip" data-placement="top" title="Release The Order {{ dateFormat($invoice->due_date)." (".($release_limit + 1)." days)" }}">
                    @elseif($release_limit < 7)
                        <tr class="background-reconfirm-4" data-toggle="tooltip" data-placement="top" title="Reconfirm : {{ dateFormat($invoice->due_date)." (".($release_limit + 1)." days)" }}">
                    @elseif($release_limit < 10)
                        <tr class="background-reconfirm-7" data-toggle="tooltip" data-placement="top" title="Reconfirm : {{ dateFormat($invoice->due_date)." (".($release_limit + 1)." days)" }}">
                    @else
                        <tr class="background-normal">
                    @endif
                            <td>
                                <p>{{ $agent_active->name }}</p>
                                <p>{{ "@".$agent_active->office }}</p>
                            </td>
                            <td>
                                <b>{{ $confirmedorder->orderno }}</b>
                                <p>{{ $confirmedorder->service }}</p>
                                <div class="status-confirmed inline-left"></div>
                                @if ($confirmedorder->kick_back > 0)
                                    <div class="status-kick-back inline-left" data-toggle="tooltip" data-placement="top" title="Kick Back ${{ $confirmedorder->kick_back }}"><i class="icon-copy fa fa-usd" aria-hidden="true"></i></div>
                                @endif
                                @if ($confirmedorder->discounts > 0)
                                    <div class="status-discounts inline-left" data-toggle="tooltip" data-placement="top" title="Discounts ${{ $confirmedorder->discounts }}"><i class="icon-copy fa fa-percent" aria-hidden="true"></i></div>
                                @endif
                                @if ($confirmedorder->bookingcode_disc > 0)
                                    <div class="status-bcode inline-left" data-toggle="tooltip" data-placement="top" title="{{ $confirmedorder->bookingcode." ($".$confirmedorder->bookingcode_disc.")" }}"><i class="icon-copy fa fa-qrcode" aria-hidden="true"></i></div>
                                @endif
                                @if ($total_promotion_disc > 0)
                                    <div class="status-promotion inline-left" data-toggle="tooltip" data-placement="top" title="@lang('messages.Promotion'){{ '$'.$total_promotion_disc }}"><i class="icon-copy fa fa-tag" aria-hidden="true"></i></div>
                                @endif
                            </td>
                            <td>
                                {{-- Hotel ============================================================================================================ --}}
                                @if ($confirmedorder->service == 'Hotel')
                                    {{ $confirmedorder->duration }} Night
                                    <p>In:
                                        {{ dateFormat($confirmedorder->checkin) }}</p>
                                    <p>Out:
                                        {{ dateFormat($confirmedorder->checkout) }}</p>
                                @elseif ($confirmedorder->service == 'Hotel Package')
                                    {{ $confirmedorder->duration }} Night
                                    <p>In:
                                        {{ dateFormat($confirmedorder->checkin) }}</p>
                                    <p>Out:
                                        {{ dateFormat($confirmedorder->checkout) }}</p>
                                @elseif ($confirmedorder->service == 'Hotel Promo')
                                    {{ $confirmedorder->duration }} Night
                                    <p>In:
                                        {{ dateFormat($confirmedorder->checkin) }}</p>
                                    <p>Out:
                                        {{ dateFormat($confirmedorder->checkout) }}</p>
                                @elseif ($confirmedorder->service == 'Private Villa')
                                    {{ $confirmedorder->duration }} Night
                                    <p>In:
                                        {{ dateFormat($confirmedorder->checkin) }}</p>
                                    <p>Out:
                                        {{ dateFormat($confirmedorder->checkout) }}</p>
                                {{-- Tour ============================================================================================================ --}}
                                @elseif ($confirmedorder->service == 'Tour Package')
                                    {{ $confirmedorder->duration }}
                                    <p>Start:{{ dateFormat($confirmedorder->checkin) }}</p>
                                    <p>End:{{ dateFormat($confirmedorder->checkout) }}</p>
                                {{-- Activity ============================================================================================================ --}}
                                @elseif ($confirmedorder->service == 'Activity')
                                    {{ $confirmedorder->duration }} Hours
                                    <p>Start:
                                        {{ dateTimeFormat($confirmedorder->travel_date) }}</p>
                                    <p>End:
                                        <?php
                                            $activity_duration = $confirmedorder->duration;
                                            $activity_end=date('Y-m-d H.i', strtotime('+'.$activity_duration.'hours', strtotime($confirmedorder->travel_date))); 
                                        ?>
                                        {{ dateTimeFormat($activity_end) }}
                                {{-- Transport ============================================================================================================ --}}        
                                @elseif ($confirmedorder->service == 'Transport')
                                    @if ($confirmedorder->duration == "1")
                                        {{ $confirmedorder->duration }} Day
                                    @else
                                        {{ $confirmedorder->duration }} Days
                                    @endif
                                    <p>Pickup Date:{{ dateTimeFormat($confirmedorder->travel_date) }}</p>
                                    <p>Return Date:
                                        <?php
                                            $transport_duration = $confirmedorder->duration;
                                            $return_date=date('Y-m-d H.i', strtotime('+'.$transport_duration.'days', strtotime($confirmedorder->travel_date))); 
                                        ?>
                                        {{ dateTimeFormat($return_date) }}</p>
                                @else
                                    Not Listed
                                @endif
                            </td>
                            <td>
                                @if ($confirmedorder->service == "Wedding Package")
                                    {{ $confirmedorder->groom_name." & ".$confirmedorder->bride_name }}<br>
                                    {{ $confirmedorder->number_of_guests." Invitations" }}
                                @elseif($confirmedorder->service == "Private Villa")
                                    {{ $confirmedorder->number_of_guests." guests" }}
                                @else
                                    @php
                                        $gst = json_decode($confirmedorder->guest_detail);
                                        $ar = is_array($gst);
                                    @endphp
                                    @if ($ar == 1)
                                        @php
                                            $guests = implode(', ',$gst);
                                        @endphp
                                        {!! $guests !!}
                                    @else
                                        {!! $confirmedorder->guest_detail !!}
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if ($confirmedorder->kick_back > 0 or $confirmedorder->discounts > 0 or $confirmedorder->bookingcode_disc > 0 or  $total_promotion_disc > 0)
                                    @if (isset($confirmedorder->kick_back))
                                        @if ($confirmedorder->optional_price != "")
                                            <p class="normal-price">{{ '$ ' . number_format(($confirmedorder->price_total + $confirmedorder->kick_back + $confirmedorder->optional_price), 0, ',', '.') }}</p>
                                        @else
                                            <p class="normal-price">{{ '$ ' . number_format(($confirmedorder->price_total + $confirmedorder->kick_back), 0, ',', '.') }}</p>
                                        @endif

                                    @else
                                        <p class="normal-price">{{ '$ ' . number_format(($confirmedorder->price_total), 0, ',', '.') }}</p>
                                    @endif
                                @endif
                                {{ '$ ' . number_format($confirmedorder->final_price, 0, ',', '.') }}
                            </td>
                        
                            {{-- End Status =========================================================================================================== --}}
                            <td class="text-right">
                                <div class="table-action">
                                    <a href="/orders-admin-{{ $confirmedorder->id }}"  data-toggle="tooltip" data-placement="top" title="Detail Order"> 
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