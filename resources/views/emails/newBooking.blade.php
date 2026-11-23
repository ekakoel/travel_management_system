<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Order {{ $order->orderno }}</title>
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <style>
        .text{
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    ----------------------------------------------------------------------------------------------------------------------------<br>
    <h1 style="padding: 0 !important; margin: 0 !important;">{{ 'New Order '.$order->orderno }}</h1>
    {{ 'Sales / Agent : '.$agent->name." (".$agent->office.")" }} - {{ dateFormat($order->created_at) }}<br>
    @if ($order->request_quotation == "Yes")
        <b style="padding: 0 !important; margin: 0 !important; font-size:1.2rem; color:red;">Book a room of more than 8 units</b><br>
    @endif
    ----------------------------------------------------------------------------------------------------------------------------<br>
        <div class="email-title">
            <b>Order Detail</b><br>
        </div>
        <table class="table email-table">
            <tr>
                <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                    @if ($order->service == "Tour Package")
                        Tour Package<br>
                        Number of Guest<br>
                        Duration<br>
                        Tour Start<br>
                        Tour End<br>
                    @elseif ($order->service == "Hotel")
                        Hotel<br>
                        Room<br>
                        Duration<br>
                        @if ($order->request_quotation == "Yes")
                            Number of room<br>
                        @else
                            Number of Room<br>
                        @endif
                        Number of Guests<br>
                        Check-in<br>
                        Check-out<br>
                    @elseif ($order->service == "Hotel Promo")
                    Hotel Promo<br>
                        Hotel<br>
                        Room<br>
                        Duration<br>
                        @if ($order->request_quotation == "Yes")
                            Number of room<br>
                        @else
                            Number of Room<br>
                        @endif
                        Number of Guests<br>
                        Check-in<br>
                        Check-out<br>
                    @elseif ($order->service == "Private Villa")
                        Service<br>
                        Villa<br>
                        Number of Room<br>
                        Number of Guests<br>
                        Duration<br>
                        Check-in<br>
                        Check-out<br>
                    @elseif ($order->service == "Hotel Package")
                        Hotel<br>
                        Room<br>
                        Duration<br>
                        @if ($order->request_quotation == "Yes")
                            Number of room<br>
                        @else
                            Number of Room<br>
                        @endif
                        Number of Guests<br>
                        Check-in<br>
                        Check-out<br>
                    @elseif ($order->service == "Activity")
                        Activity<br>
                        Capacity<br>
                        Partner<br>
                        Type<br>
                        Duration<br>
                        Activity Date<br>
                    @elseif ($order->service == "Transport")
                        Transport<br>
                        Type<br>
                        Capacity<br>
                        Service<br>
                        Duration<br>
                        Src - Dst<br>
                        Pickup Date<br>
                    @else
                    @endif
                </td>
                <td class="ftd-2">
                    @if ($order->service == "Tour Package")
                        {{ $order->subservice }}<br>
                        {{ $order->number_of_guests." guests" }}<br>
                        {{ $order->duration }}<br>
                        {{ dateFormat($order->checkin) }}<br>
                        {{ dateFormat($order->checkout) }}<br>
                    @elseif ($order->service == "Hotel")
                        {{ $order->servicename }}<br>
                        {{ $order->subservice }}<br>
                        {{ $order->duration." nights" }}<br>
                        @if ($order->request_quotation == "Yes")
                            TBA <i>(to be advised)</i>
                        @else
                            {{ $order->number_of_room." unit" }}<br>
                        @endif
                        {{ $order->number_of_guests." guests" }}<br>
                        {{ dateFormat($order->checkin) }}<br>
                        {{ dateFormat($order->checkout) }}<br>
                    @elseif ($order->service == "Hotel Promo")
                        @php
                            $promo_name = $order->promo_name;
                        @endphp
                        {{ $promo_name }}<br>
                        {{ $order->servicename }}<br>
                        {{ $order->subservice }}<br>
                        {{ $order->duration." nights" }}<br>
                        @if ($order->request_quotation == "Yes")
                            TBA <i>(to be advised)</i>
                        @else
                            {{ $order->number_of_room." unit" }}<br>
                        @endif
                        {{ $order->number_of_guests." guests" }}<br>
                        {{ dateFormat($order->checkin) }}<br>
                        {{ dateFormat($order->checkout) }}<br>
                    @elseif ($order->service == "Private Villa")
                        Private Villa<br>
                        {{ $order->servicename }}<br>
                        {{ $order->number_of_room }}<br>
                        {{ $order->number_of_guests." guests" }}<br>
                        {{ $order->duration." nights" }}<br>
                        {{ dateFormat($order->checkin) }}<br>
                        {{ dateFormat($order->checkout) }}<br>
                    @elseif ($order->service == "Hotel Package")
                        {{ $order->package_name }}<br>
                        {{ $order->servicename }}<br>
                        {{ $order->subservice }}<br>
                        {{ $order->duration." nights" }}<br>
                        @if ($order->request_quotation == "Yes")
                            TBA <i>(to be advised)</i>
                        @else
                            {{ $order->number_of_room." unit" }}<br>
                        @endif
                        {{ $order->number_of_guests." guests" }}<br>
                        {{ dateFormat($order->checkin) }}<br>
                        {{ dateFormat($order->checkout) }}<br>
                    @elseif ($order->service == "Activity")
                        {{ $order->subservice }}<br>
                        {{ $order->capacity." pax" }}<br>
                        {{ $order->servicename }}<br>
                        {{ $order->service_type }}<br>
                        {{ $order->duration." hours" }}<br>
                        {{ dateFormat($order->checkin) }}<br>
                    @elseif ($order->service == "Transport")
                        {{ $order->servicename }}<br>
                        {{ $order->subservice }}<br>
                        {{ $order->capacity." seat" }}<br>
                        {{ $order->service_type }}<br>
                        @if ($order->service_type == "Daily Rent")
                            {{ $order->duration." day" }}<br>
                        @else
                            {{ $order->duration." Hours" }}<br>
                        @endif
                        {{ $order->src." - ". $order->dst }}<br>
                        {{ dateFormat($order->checkin) }}<br>
                    @else
                    @endif
                </td>
            </tr>
        </table>
    @if (isset($airport_shuttles))
        @if (count($airport_shuttles)>0)
        ----------------------------------------------------------------------------------------------------------------------------<br>
            <div class="email-title">
                <b>Transport</b><br>
            </div>
            @foreach ($airport_shuttles as $no=>$airport_shuttle)
                <div class="text">{{ ++$no }}. Date {{ dateFormat($airport_shuttle->date) }}, Airport Shuttle, {{ $airport_shuttle->transport->brand." ". $airport_shuttle->transport->name.", ".$airport_shuttle->src." - ".$airport_shuttle->dst  }} </div>
            @endforeach
        @endif
    @endif
    @if ($order->special_day != "")
        @php
            $special_day = json_decode($order->special_day);
            $special_date = json_decode($order->special_date);
            $cs_day = count($special_day);
            $room = 1;
        @endphp
        @if ($special_date >= $now)
            ----------------------------------------------------------------------------------------------------------------------------<br>
            <div class="email-title">
                <b>Special Day</b><br>
            </div>
            @for ($i = 0; $i < $cs_day; $i++)
                @if (isset($special_day[$i]))
                    {{ "Room ".($room+$i)." any ". $special_day[$i]." on ". dateFormat($special_date[$i]) }}<br>
                @endif
            @endfor
        @endif
    @endif
    @if (isset($order->note))
        ----------------------------------------------------------------------------------------------------------------------------<br>
        <div class="email-title">
            <b>Remark</b><br>
        </div>
        {!! $order->note !!}<br>
    @endif
    ----------------------------------------------------------------------------------------------------------------------------<br>
    This email has been sent automatically, to confirm the order, please use the following link <a target="__blank" href="{{ $order_link }}"><button class="btn btn-primary" style="color: rgb(0, 60, 255); padding: 4px 18px;border-radius: 100px;">Order Detail</button></a><br>
    ----------------------------------------------------------------------------------------------------------------------------<br>
    <i>If an error occurs, please contact the administrator or IT staff, Bali Kami Tour!</i>
        <br>
        <br>
    <p style="text-decoration-line: underline;">Online booking system {{"V". config('app.app_version') }} | PT. Bali Kami | {{ date("Y",strtotime($now)) }}</p>
</body>
</html>
