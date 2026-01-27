<div class="col-md-12 m-b-18">
    <div class="card-box">
        <div class="card-box-title">
            <div class="title">Status</div>
        </div> 
        <div class="order-status-container">
            @if ($order->status == "Active")
                <div class="status-active-color">Confirmed</div>
                @if ($reservation->send == "yes")
                    - <div class="send-email">Email <i class="icon-copy fa fa-envelope" aria-hidden="true"></i></div>
                @else
                    - <div class="not-send-email">Email <i class="icon-copy fa fa-envelope" aria-hidden="true"></i></div>
                @endif
                @if ($reservation->status == "Active")
                    - <div class="not-approved-order">Waiting <i class="icon-copy ion-clock"></i></div>
                @endif
            @elseif ($order->status == "Pending")
                <div class="status-pending-color">{{ $order->status }}</div>
            @elseif ($order->status == "Invalid")
                <div class="status-invalid-color">{{ $order->status }}</div>
            @elseif ($order->status == "Rejected")
                <div class="status-reject-color">{{ $order->status }}</div>
            @elseif ($order->status == "Confirmed")
                <div class="status-confirmed-color">{{ $order->status }}</div>
            @elseif ($order->status == "Approved")
                <div class="status-approved-color"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ $order->status }}</div>
                @if ($reservation->checkin > $now)
                    - <div class="standby-order"><p><i class="icon-copy ion-clock"> </i> {{ date('D ,d M Y',strtotime($order->checkin)) }}</p></div>
                @elseif ($reservation->checkin <= $now and $reservation->checkout > $now)
                    - <div class="ongoing-order">Ongoing <i class="icon-copy ion-android-walk"></i></div>
                @else
                    - <div class="final-order">Final</div>
                @endif
            @elseif($order->status == "Paid")
                @if ($receipts)
                    <div class="status-paid-color"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ $order->status }}</div>
                @elseif($doku_payment)
                    <div class="status-paid-color"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ $doku_payment->invoice_number }} ({{ date('d M Y',strtotime($doku_payment->payment_date)) }})</div>
                @else
                    <div class="status-paid-color"><i class="icon-copy fa fa-check-circle" aria-hidden="true"></i> {{ $invoice->inv_no }}</div>
                @endif
            @else
                <div class="status-draf-color">{{ $order->status }}</div>
            @endif
        </div>
        @if (count($orderlogs)>0)
            <hr class="form-hr">
            <p><b>Order Log:</b></p>
            <table class="table tb-list">
                @foreach ($orderlogs as $no=>$orderlog)
                    @php
                        $adminorder = $admins->where('id',$orderlog->admin)->first();
                    @endphp
                    <tr>
                        <td>{{ ++$no.". " }}</td>
                        <td> {{ dateTimeFormat($orderlog->created_at) }}</td>
                        <td>{!! $adminorder->code !!}</td>
                        <td><i>{!! $orderlog->action !!}</i></td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
</div>