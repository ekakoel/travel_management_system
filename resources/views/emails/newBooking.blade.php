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
    @if ($order->service == "Wedding Package")
        <div class="email-title">
            <b>Bride details</b><br>
        </div>
        <table class="table email-table m-b-18">
            <tr>
                <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                    Groom
                </td>
                <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                <td class="ftd-2">
                    {{ $order_wedding_brides->groom." (".$order_wedding_brides->groom_chinese.")" }}
                </td>
            </tr>
            <tr>
                <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                    Telephone
                </td>
                <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                <td class="ftd-2">
                    {{ $order_wedding_brides->groom_contact }}
                </td>
            </tr>
            <tr>
                <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                    Bride
                </td>
                <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                <td class="ftd-2">
                    {{ $order_wedding_brides->bride." (".$order_wedding_brides->bride_chinese.")" }}
                </td>
            </tr>
            <tr>
                <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                    Telephone
                </td>
                <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                <td class="ftd-2">
                    {{ $order_wedding_brides->bride_contact }}
                </td>
            </tr>
        </table>
        ----------------------------------------------------------------------------------------------------------------------------<br>
        <div class="email-title">
            <b>Wedding details</b><br>
        </div>
        <table class="table email-table m-b-18">
            <tr>
                <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                    Wedding Package
                </td>
                <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                <td class="ftd-2">
                    {{ $wedding->name }}
                </td>
            </tr>
            <tr>
                <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                    Wedding Date
                </td>
                <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                <td class="ftd-2">
                    {{ dateTimeFormat($order_wedding->wedding_date) }}
                </td>
            </tr>
            <tr>
                <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                    Number of Invitations
                </td>
                <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                <td class="ftd-2">
                    {{ $order->number_of_guests." Invitations" }}
                </td>
            </tr>
            <tr>
                <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                    Check-in
                </td>
                <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                <td class="ftd-2">
                    {{ dateFormat($order->checkin) }}
                </td>
            </tr>
            <tr>
                <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                    Check-out
                </td>
                <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                <td class="ftd-2">
                    {{ dateFormat($order->checkout) }}
                </td>
            </tr>
        </table>
        ----------------------------------------------------------------------------------------------------------------------------<br>
        <div class="email-title">
            <b>Services</b><br>
        </div>
        <table class="table email-table m-b-18">
            @php
                $d_fixeds_id = json_decode($order_wedding->wedding_fixed_service_id);
                $d_wedding_venues_id = json_decode($order_wedding->wedding_venue_id);
                $d_wedding_makeups_id = json_decode($order_wedding->wedding_makeup_id);
                $d_wedding_rooms_id = json_decode($order_wedding->wedding_room_id);
                $d_wedding_documentations_id = json_decode($order_wedding->wedding_documentation_id);
                $d_wedding_decorations_id = json_decode($order_wedding->wedding_decoration_id);
                $d_wedding_dinner_venues_id = json_decode($order_wedding->wedding_dinner_venue_id);
                $d_wedding_entertainments_id = json_decode($order_wedding->wedding_entertainment_id);
                $d_wedding_transports_id = json_decode($order_wedding->wedding_transport_id);
                $d_wedding_others_id = json_decode($order_wedding->wedding_other_id);
            @endphp
            @if ($d_fixeds_id)
                <tr>
                    <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                        Fixed Services
                    </td>
                    <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                    <td class="ftd-2">
                        @if ($d_fixeds_id)
                            @foreach ($d_fixeds_id as $d_fixed_id)
                                @php
                                    $fixed_service = $wedding_services->where('id',$d_fixed_id)->first();
                                @endphp
                                @if (count($d_fixeds_id)>1)
                                    • {{ $fixed_service->service }}<br>
                                @else
                                    {{ $fixed_service->service }}
                                @endif
                            @endforeach
                        @endif
                    </td>
                </tr>
            @endif
            @if ($d_wedding_venues_id)
                <tr>
                    <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                        Wedding Venue
                    </td>
                    <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                    <td class="ftd-2">
                        @php
                            $d_wedding_venues_id = json_decode($order_wedding->wedding_venue_id);
                        @endphp
                        @if ($d_wedding_venues_id)
                            @foreach ($d_wedding_venues_id as $d_wedding_venue_id)
                                @php
                                    $wedding_venue = $wedding_services->where('id',$d_wedding_venue_id)->first();
                                @endphp
                                @if (count($d_wedding_venues_id)>1)
                                    • {{ $wedding_venue->service }}<br>
                                @else
                                    {{ $wedding_venue->service }}
                                @endif
                            @endforeach
                        @endif
                    </td>
                </tr>
            @endif
            @if ($d_wedding_rooms_id)
                <tr>
                    <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                        Suites and Villas
                    </td>
                    <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                    <td class="ftd-2">
                        @php
                            $d_wedding_rooms_id = json_decode($order_wedding->wedding_room_id);
                        @endphp
                        @if ($d_wedding_rooms_id)
                            @foreach ($d_wedding_rooms_id as $d_wedding_room_id)
                                @php
                                    $wedding_room = $wedding_room_services->where('id',$d_wedding_room_id)->first();
                                @endphp
                                @if (count($d_wedding_rooms_id)>1)
                                    • {{ $wedding_room->rooms }}<br>
                                @else
                                    {{ $wedding_room->rooms }}
                                @endif
                            @endforeach
                        @endif
                    </td>
                </tr>
            @endif
            @if ($d_wedding_documentations_id)
                <tr>
                    <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                        Wedding Documentation
                    </td>
                    <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                    <td class="ftd-2">
                        @php
                            $d_wedding_documentations_id = json_decode($order_wedding->wedding_documentation_id);
                        @endphp
                        @if ($d_wedding_documentations_id)
                            @foreach ($d_wedding_documentations_id as $d_wedding_documentation_id)
                                @php
                                    $wedding_documentation = $wedding_services->where('id',$d_wedding_documentation_id)->first();
                                @endphp
                                @if (count($d_wedding_documentations_id)>1)
                                    • {{ $wedding_documentation->service }}<br>
                                @else
                                    {{ $wedding_documentation->service }}
                                @endif
                            @endforeach
                        @endif
                    </td>
                </tr>
            @endif
            @if ($d_wedding_decorations_id)
                <tr>
                    <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                        Wedding Decoration
                    </td>
                    <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                    <td class="ftd-2">
                        @php
                            $d_wedding_decorations_id = json_decode($order_wedding->wedding_decoration_id);
                        @endphp
                        @if ($d_wedding_decorations_id)
                            @foreach ($d_wedding_decorations_id as $d_wedding_decoration_id)
                                @php
                                    $wedding_decoration = $wedding_services->where('id',$d_wedding_decoration_id)->first();
                                @endphp
                                @if (count($d_wedding_decorations_id)>1)
                                    • {{ $wedding_decoration->service }}<br>
                                @else
                                    {{ $wedding_decoration->service }}
                                @endif
                            @endforeach
                        @endif
                    </td>
                </tr>
            @endif
            @if ($d_wedding_dinner_venues_id)
                <tr>
                    <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                        Dinner Venue
                    </td>
                    <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                    <td class="ftd-2">
                        @php
                            $d_wedding_dinner_venues_id = json_decode($order_wedding->wedding_dinner_venue_id);
                        @endphp
                        @if ($d_wedding_dinner_venues_id)
                            @foreach ($d_wedding_dinner_venues_id as $d_wedding_dinner_venue_id)
                                @php
                                    $wedding_dinner_venue = $wedding_services->where('id',$d_wedding_dinner_venue_id)->first();
                                @endphp
                                @if (count($d_wedding_dinner_venues_id)>1)
                                    • {{ $wedding_dinner_venue->service }}<br>
                                @else
                                    {{ $wedding_dinner_venue->service }}
                                @endif
                            @endforeach
                        @endif
                    </td>
                </tr>
            @endif
            @if ($d_wedding_entertainments_id)
                <tr>
                    <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                        Wedding Entertainment
                    </td>
                    <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                    <td class="ftd-2">
                        @php
                            $d_wedding_entertainments_id = json_decode($order_wedding->wedding_entertainment_id);
                        @endphp
                        @if ($d_wedding_entertainments_id)
                            @foreach ($d_wedding_entertainments_id as $d_wedding_entertainment_id)
                                @php
                                    $wedding_entertainment = $wedding_services->where('id',$d_wedding_entertainment_id)->first();
                                @endphp
                                @if (count($d_wedding_entertainments_id)>1)
                                    • {{ $wedding_entertainment->service }}<br>
                                @else
                                    {{ $wedding_entertainment->service }}
                                @endif
                            @endforeach
                        @endif
                    </td>
                </tr>
            @endif
            @if ($d_wedding_others_id)
                <tr>
                    <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                        Other Services
                    </td>
                    <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                    <td class="ftd-2">
                        @php
                            $d_wedding_others_id = json_decode($order_wedding->wedding_other_id);
                        @endphp
                        @if ($d_wedding_others_id)
                            @foreach ($d_wedding_others_id as $d_wedding_other_id)
                                @php
                                    $wedding_other = $wedding_services->where('id',$d_wedding_other_id)->first();
                                @endphp
                                @if (count($d_wedding_others_id)>1)
                                    • {{ $wedding_other->service }}<br>
                                @else
                                    {{ $wedding_other->service }}
                                @endif
                            @endforeach
                        @endif
                    </td>
                </tr>
            @endif
            @if ($d_wedding_transports_id)
                <tr>
                    <td class="ftd-1" style="width:180px !important; vertical-align: baseline;">
                        Transport
                    </td>
                    <td style="padding-left:36px !important; padding-right:8px !important; vertical-align: baseline;">: </td>
                    <td class="ftd-2">
                        @php
                            $d_wedding_transports_id = json_decode($order_wedding->wedding_transport_id);
                        @endphp
                        @if ($d_wedding_transports_id)
                            @foreach ($d_wedding_transports_id as $d_wedding_transport_id)
                                @php
                                    $wedding_transport = $wedding_transport_services->where('id',$d_wedding_transport_id)->first();
                                @endphp
                                @if (count($d_wedding_transports_id)>1)
                                    • {{ $wedding_transport->brand." ".$wedding_transport->name }}<br>
                                @else
                                    {{ $wedding_transport->brand." ".$wedding_transport->name }}
                                @endif
                            @endforeach
                        @endif
                    </td>
                </tr>
            @endif
        </table>
    @else
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
    @endif
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