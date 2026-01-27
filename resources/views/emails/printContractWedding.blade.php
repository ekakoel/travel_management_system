@php
    $r=1;
    $i=1;
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>{{ $order->orderno }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style>
        body{
            padding:0;
            margin: 0;
            /* font-family: sans-serif; */
            font-family: "notosans" !important;
            letter-spacing: 1px;
            line-height: 1 !important;
            /* font-family: "chinese-font"; */
        }
        p{
            font-size: 0.8rem !important;
            line-height: 1.3;
        }
        .m-b-18{
            margin-bottom: 18px;
        }
        .m-t-18{
            margin-top: 18px;
        }
        td{
            vertical-align: text-top !important;
        }
        .card-box{
            padding: 18px;
            margin-bottom: 8px;
            /* border-bottom: 1px dotted black; */
            border: ridge;
        }
        .heading{
            width: 100%;
            display: inline-flex;
        }
        .heading .heading-right .contract-date{
            background-color: #bb0000;
            text-align-last: right;
            font-size: 1rem;
            padding: 8px 18px;
        }
        .heading .heading-left,.heading-right{
            align-self: self-end;
        }
        .contract-date{
            background-color: #bb0000;
            text-align-last: right;
            font-size: 1rem;
            font-weight: 600;
            padding: 8px 18px;
            color: white;
        }
        .card-box .heading .title{
            width: 100%;
            display: grid;
            text-align: end;
            font-size: 2.7rem;
            font-family: "notosans" !important;
            font-weight: 700;
            text-transform: capitalize;
            place-content: flex-end;
            color: #0033cc;
        }
        .bank-account{
            border: 1px solid #858585;
            padding: 8px 8px 0 8px;
            border-radius: 4px;
            font-size: 0.7rem;
        }
        .business-name{
            font-size: 1.8rem;
            font-weight: 600;
        }
        .business-sub{
            font-style: italic;
            font-size: 1rem;
        }
        .table-container{
            display: flex;
        }
        table {
            margin: 0 0 8px 0;
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        .tb-container{
            display: -webkit-inline-box;
            width: 100%
        }
        .tb-list{
            width: 100%;
            height: fit-content;
        }
        table .tb-heading{
            background-color: #bb0000;
            font-size: 1rem;
            font-weight: 600;
            padding: 8px 18px;
            color: white;
        }
        .tb-heading td{
            padding: 8px 18px;
        }
        .table-list td{
            padding: 1px 8px;
            border: none;
            vertical-align: text-top;
        }

        .table-order th {
            border:1px solid #4d4d4d;
            background-color: #696969;
            text-align: left;
            padding: 4px 8px;
            color: white;
        }
        .table-order p{
            margin: 0 !important;
            font-size: 0.8rem !important;
        }
        .table-order td {
            padding: 2px 8px;
            border: 1px solid #4d4d4d;
            text-align: left;
            vertical-align: text-top;
        }

        .table-order tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .btn-print{
            position: fixed;
            right: 0;
            bottom: 0;
            background-color: white;
            width: 100%;
            padding: 18px;
            text-align: right;
        }
        .btn-print button{
            padding: 8px 47px;
            border-radius: 8px;
            background-color: #818181;
            color: #020202;
            font-size: 1.2rem;
            box-shadow: 2px 6px 5px 0px #414040;
            
            cursor: pointer;
        }
        .btn-print button:hover{
            padding: 8px 47px;
            border-radius: 8px;
            background-color: #0000c1;
            color: white;
            font-size: 1.2rem;
            box-shadow: 1px 0px 0px 0px #414040;
        }
        .content{
            font-size: 0.8rem;
            padding: 0 0 8px 0;
        }
        .content .notification-text{
            font-style: italic;
        }
        .subtitle{
            font-size: 0.9rem;
            font-family: "notosans" !important;
            padding-bottom: 8px;
            /* padding-top: 15px; */
        }
        .final-price{
            font-family: "notosans" !important;
            font-size: 0.9rem;
        }
        .guest-container{
            display: inline-flex;
            gap: 27px;
            flex-wrap: wrap;
        }
        .doc-print{
            margin: 4% 18% 8% 18%;
        }
        @media print {
            .hide-print {
                display: none;
            }
            .doc-print{
                margin: 0;
            }
            .card-box{
            padding: 18px 0 0 0;
            margin-bottom: 0;
            /* border-bottom: 1px dotted black; */
            border: none;
        }
        }
        @media (max-width: 579px) {
            .guest-container{
                gap: 8px
            }
        }
    </style>
</head>
    <body class="doc-print">
        {{-- CONTRACT EN --}}
        <div class="card-box" style="page-break-after: always;">
            <div class="heading">
                <table class="table">
                    <tr>
                        <td> 
                            <div class="heading-left">
                                <div class="business-name">{{ $business->name }}</div>
                                <div class="business-sub">{{ __('messages.'.$business->caption) }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="heading-right" style="text-align: right;">
                                <div class="title">CONTRACT</div>
                            </div>
                        </td>
                    </tr>
                    <tr class="tb-heading" style="width: 100%">
                        <td>
                        </td>
                        <td>
                            <div class="order-date" style="text-align: right">{{ date('D, d M y',strtotime($order->updated_at)) }}</div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="content">
                <table class="table tb-head">
                    <tr>
                        <td style="width: 20%;">
                            Reservation No.
                        </td>
                        <td style="width: 30%;">
                            : {{ $reservation->rsv_no }}
                        </td>
                       
                        <td style="width: 20%;">
                            Arrival Flight
                        </td>
                        <td style="width: 30%;">
                            @if (isset($order->arrival_flight))
                                : {{ $order->arrival_flight }}
                            @else
                                : ..........................
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            Reservation Date
                        </td>
                        <td style="width: 30%;">
                            : {{ dateFormat($reservation->created_at) }}
                        </td>
                        
                        <td style="width: 20%;">
                            Arrival Time
                        </td>
                        <td style="width: 30%;">
                            @if ($order->arrival_time)
                                : {{ dateTimeFormat($order->arrival_time) }}
                            @else
                                : ..........................
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            Pickup Name
                        </td>
                        <td style="width: 30%;">
                            @if (isset($bride))
                                : Mr.{{ $bride->groom." ".$bride->bride_chinese }}
                            @else
                                : ..........................
                            @endif
                        </td>
                        <td style="width: 20%;">
                            Departure Flight
                        </td>
                        <td style="width: 30%;">
                            @if (isset($order->departure_flight))
                                : {{ $order->departure_flight }}
                            @else
                                : ..........................
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            Pickup Phone
                        </td>
                        <td style="width: 30%;">
                            @if (isset($bride))
                                : {{ $bride->bride_contact }}
                            @else
                                : ..........................
                            @endif
                        </td>
                        <td style="width: 20%;">
                            Departure Time
                        </td>
                        <td style="width: 30%;">
                            @if ($order->departure_time)
                                : {{ dateTimeFormat($order->departure_time) }}
                            @else
                                : ..........................
                            @endif
                        </td>
                        
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            Sales / Agent
                        </td>
                        <td style="width: 30%;">
                            : {{ $agent->name }}
                        </td>
                        @if (isset($guide->name))
                            <td style="width: 20%;">
                                Guide Name
                            </td>
                            <td style="width: 30%;">
                                : {{ $guide->name }}
                            </td>
                        @endif
                        
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            Office
                        </td>
                        <td style="width: 30%;">
                            : {{ $agent->office }}
                        </td>
                        @if (isset($guide->phone))
                            <td style="width: 20%;">
                                Guide Phone
                            </td>
                            <td style="width: 30%;">
                                : {{ $guide->phone }}
                            </td>
                        @endif
                    </tr>
                    @if (isset($driver->name))
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="width: 20%;">
                                Driver Name
                            </td>
                            <td style="width: 30%;">
                                : {{ $driver->name }}
                            </td>
                        </tr>
                    @endif
                    @if (isset($driver->phone))
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="width: 20%;">
                                Driver Phone
                            </td>
                            <td style="width: 30%;">
                                : {{ $driver->phone }}
                            </td>
                        </tr>
                    @endif
                </table>
                {{-- Bride's --}}
                <table class="table-order m-t-18">
                    <tr>
                        <th colspan="2">Bride's Detail</th>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Groom
                        </td>
                        <td style="width: 70%">
                            Mr. {{ $bride->groom." ".$bride->groom_chinese }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Bride
                        </td>
                        <td style="width: 70%">
                            Mrs. {{ $bride->bride." ".$bride->bride_chinese }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Wedding Package
                        </td>
                        <td style="width: 70%">
                            {{ $order->servicename }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Wedding Date
                        </td>
                        <td style="width: 70%">
                            {{ dateTimeFormat($order->wedding_date) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Number of Invitations
                        </td>
                        <td style="width: 70%">
                            {{ $order_wedding->number_of_invitation }} guests
                        </td>
                    </tr>
                </table>
                {{-- Services --}}
                <table class="table-order">
                    <tr>
                        <th colspan="2">Services</th>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Include
                        </td>
                        <td style="width: 70%">
                            @php
                                $w_fixed_services_id = json_decode($order_wedding->wedding_fixed_service_id);
                            @endphp
                            @if ($w_fixed_services_id)
                                @foreach ($w_fixed_services_id as $w_fixed_service_id)
                                    @php
                                        $wedding_fixed_service = $weddingFixedServices->where('id',$w_fixed_service_id)->first();
                                    @endphp
                                    @if (count($w_fixed_services_id)>1)
                                        • {{ $wedding_fixed_service->service }}<br>
                                    @else
                                        {{ $wedding_fixed_service->service }}
                                    @endif
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Bride's Hotel
                        </td>
                        <td style="width: 70%">
                            @php
                                $rooms_id = json_decode($order_wedding->wedding_room_id);
                            @endphp
                            @if ($rooms_id)
                                @foreach ($rooms_id as $room_id)
                                    @php
                                        $room_service = $rooms->where('id',$room_id)->first();
                                    @endphp
                                    @if (count($rooms_id)>1)
                                        • {{ $order->subservice.", ". $room_service->rooms.", ".$order->duration." night" }} (<i>{{ dateFormat($order->checkin)." - ".dateFormat($order->checkout) }}</i>)<br>
                                    @else
                                        {{ $order->subservice.", ". $room_service->rooms.", ".$order->duration." night" }} (<i>{{ dateFormat($order->checkin)." - ".dateFormat($order->checkout) }}</i>)
                                    @endif
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Wedding Venue
                        </td>
                        <td style="width: 70%">
                            @php
                                $w_venues_id = json_decode($order_wedding->wedding_venue_id);
                            @endphp
                            @if ($w_venues_id)
                                @foreach ($w_venues_id as $w_venue_id)
                                    @php
                                        $wedding_venue = $weddingVenues->where('id',$w_venue_id)->first();
                                    @endphp
                                    @if (count($w_venues_id)>1)
                                        • {{ $wedding_venue->service.", capacity ".$wedding_venue->capacity." guests" }}<br>
                                    @else
                                        {{ $wedding_venue->service.", capacity ".$wedding_venue->capacity." guests" }}
                                    @endif
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            MUA <i>(Make-up Artist)</i>
                        </td>
                        <td style="width: 70%">
                            @php
                                $w_makeups_id = json_decode($order_wedding->wedding_makeup_id);
                            @endphp
                            @if ($w_makeups_id)
                                @foreach ($w_makeups_id as $w_makeup_id)
                                    @php
                                        $wedding_makeup = $weddingMakeups->where('id',$w_makeup_id)->first();
                                    @endphp
                                    @if (count($w_makeups_id)>1)
                                        • {{ $wedding_makeup->service." (".$wedding_makeup->duration." ".$wedding_makeup->time.")" }}<br>
                                    @else
                                        {{ $wedding_makeup->service." (".$wedding_makeup->duration." ".$wedding_makeup->time.")" }}
                                    @endif
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Decoration
                        </td>
                        <td style="width: 70%">
                            @php
                                $w_decorations_id = json_decode($order_wedding->wedding_decoration_id);
                            @endphp
                            @if ($w_decorations_id)
                                @foreach ($w_decorations_id as $w_decoration_id)
                                    @php
                                        $wedding_decoration = $weddingDecorations->where('id',$w_decoration_id)->first();
                                    @endphp
                                    @if (count($w_decorations_id)>1)
                                        • {{ $wedding_decoration->service." (".$wedding_decoration->duration." ".$wedding_decoration->time.")" }}<br>
                                    @else
                                        {{ $wedding_decoration->service." (".$wedding_decoration->duration." ".$wedding_decoration->time.")" }}
                                    @endif
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Dinner Venue
                        </td>
                        <td style="width: 70%">
                            @php
                                $w_dinner_venues_id = json_decode($order_wedding->wedding_dinner_venue_id);
                            @endphp
                            @if ($w_dinner_venues_id)
                                @foreach ($w_dinner_venues_id as $w_dinner_venue_id)
                                    @php
                                        $wedding_dinner_venue = $weddingDinnerVenues->where('id',$w_dinner_venue_id)->first();
                                    @endphp
                                    @if (count($w_dinner_venues_id)>1)
                                        • {{ $wedding_dinner_venue->service." (".$wedding_dinner_venue->duration." ".$wedding_dinner_venue->time.")" }}<br>
                                    @else
                                        {{ $wedding_dinner_venue->service." (".$wedding_dinner_venue->duration." ".$wedding_dinner_venue->time.")" }}
                                    @endif
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Entertainment
                        </td>
                        <td style="width: 70%">
                            @php
                                $w_entertainments_id = json_decode($order_wedding->wedding_entertainment_id);
                            @endphp
                            @if ($w_entertainments_id)
                                @foreach ($w_entertainments_id as $w_entertainment_id)
                                    @php
                                        $wedding_entertainment = $weddingEntertainments->where('id',$w_entertainment_id)->first();
                                    @endphp
                                    @if (count($w_entertainments_id)>1)
                                        • {{ $wedding_entertainment->service." (".$wedding_entertainment->duration." ".$wedding_entertainment->time.")" }}<br>
                                    @else
                                        {{ $wedding_entertainment->service." (".$wedding_entertainment->duration." ".$wedding_entertainment->time.")" }}
                                    @endif
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Documentation
                        </td>
                        <td style="width: 70%">
                            @php
                                $w_documentations_id = json_decode($order_wedding->wedding_documentation_id);
                            @endphp
                            @if ($w_documentations_id)
                                @foreach ($w_documentations_id as $w_documentation_id)
                                    @php
                                        $wedding_documentation = $weddingDocumentations->where('id',$w_documentation_id)->first();
                                    @endphp
                                    @if (count($w_documentations_id)>1)
                                        • {{ $wedding_documentation->service." (".$wedding_documentation->duration." ".$wedding_documentation->time.")" }}<br>
                                    @else
                                        {{ $wedding_documentation->service." (".$wedding_documentation->duration." ".$wedding_documentation->time.")" }}
                                    @endif
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Transportation
                        </td>
                        <td style="width: 70%">
                            @php
                                $w_transportations_id = json_decode($order_wedding->wedding_transport_id);
                            @endphp
                            @if ($w_transportations_id)
                                @foreach ($w_transportations_id as $w_transportation_id)
                                    @php
                                        $wedding_transportation = $weddingTransportations->where('id',$w_transportation_id)->first();
                                    @endphp
                                    @if (count($w_transportations_id)>1)
                                        • {{ $wedding_transportation->brand." ".$wedding_transportation->name." (Airport Shuttle)" }}<br>
                                    @else
                                        {{ $wedding_transportation->brand." ".$wedding_transportation->name." (Airport Shuttle)" }}
                                    @endif
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Other Services
                        </td>
                        <td style="width: 70%">
                            @php
                                $w_others_id = json_decode($order_wedding->wedding_other_id);
                            @endphp
                            @if ($w_others_id)
                                @foreach ($w_others_id as $w_other_id)
                                    @php
                                        $wedding_other = $weddingOthers->where('id',$w_other_id)->first();
                                    @endphp
                                     @if (count($w_others_id)>1)
                                        • {{ $wedding_other->service." (".$wedding_other->duration." ".$wedding_other->time.")" }}<br>
                                    @else
                                        {{ $wedding_other->service." (".$wedding_other->duration." ".$wedding_other->time.")" }}
                                    @endif
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Additional Services
                        </td>
                        <td style="width: 70%">
                            @php
                                $w_additional_services_id = json_decode($order->additional_service);
                                $w_additional_service_qty_id = json_decode($order->additional_service_qty);
                            @endphp
                            @if ($w_additional_services_id)
                                @foreach ($w_additional_services_id as $number=>$w_additional_service_id)
                                    @if (count($w_additional_services_id)>1)
                                        • {{ $w_additional_service_id." (".$w_additional_service_qty_id[$number].")" }}<br>
                                    @else
                                        {{ $w_additional_service_id." (".$w_additional_service_qty_id[$number].")" }}
                                    @endif
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    
                </table>
                {{-- ADDITIONAL INFORMATION --}}
                <table class="table-order">
                    <tr>
                        <th colspan="2">Additional Information</th>
                    </tr>
                    @if (isset($order->benefits))
                        <tr>
                            <td style="width: 20%">Benefits</td>
                            <td style="width: 80%">{!! $order->benefits !!}</td>
                        </tr>
                    @endif
                    @if (isset($order->destinations))
                        <tr>
                            <td style="width: 20%">Destination</td>
                            <td style="width: 80%">{!! $order->destinations !!}</td>
                        </tr>
                    @endif
                    @if (isset($order->itinerary))
                        <tr>
                            <td style="width: 20%">Itinerary</td>
                            <td style="width: 80%">{!! $order->itinerary !!}</td>
                        </tr>
                    @endif
                    @if (isset($order->include))
                        <tr>
                            <td style="width: 20%">Include</td>
                            <td style="width: 80%">{!! $order->include !!}</td>
                        </tr>
                    @endif
                    @if (isset($order->include))
                        <tr>
                            <td style="width: 20%">Information</td>
                            <td style="width: 80%">{!! $order->additional_info !!}</td>
                        </tr>
                    @endif
                </table>
                @if (isset($order->note))
                    <table class="table-order">
                        <tr>
                            <th style="max-width: 100%">Remark</th>
                        </tr>
                        <tr>
                            <td>{!! $order->note !!}</td>
                        </tr>
                    </table>
                @endif
                <div class="notification-text">
                    Thank you for your support of {{ $business->name }}! If you have any questions or need further assistance, please feel free to contact our customer service at email: reservation@balikamitour.com or phone: {{ $business->phone }}
                </div>
                
            </div>
        </div>
        {{-- ITINERARY --}}
        @if ($wedding_itineraries and count($wedding_itineraries)>0)
            <div class="card-box" style="page-break-after: always;">
                <div class="heading">
                    <table class="table">
                        <tr>
                            <td> 
                                <div class="heading-left">
                                    <div class="business-name">{{ $business->name }}</div>
                                    <div class="business-sub">{{ __('messages.'.$business->caption) }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="heading-right" style="text-align: right;">
                                    <div class="title">ITINERARY</div>
                                </div>
                            </td>
                        </tr>
                        <tr class="tb-heading" style="width: 100%">
                            <td>
                            </td>
                            <td>
                                <div class="order-date" style="text-align: right">{{ date('D, d M y',strtotime($order->updated_at)) }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="content">
                {{-- Bride's --}}
                <table class="table-order">
                    <tr>
                        <th colspan="2">Bride's Detail</th>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Groom
                        </td>
                        <td style="width: 70%">
                            Mr. {{ $bride->groom." ".$bride->groom_chinese }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Bride
                        </td>
                        <td style="width: 70%">
                            Mrs. {{ $bride->bride." ".$bride->bride_chinese }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Wedding Package
                        </td>
                        <td style="width: 70%">
                            {{ $order->servicename }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Event Date
                        </td>
                        <td style="width: 70%">
                            {{ dateFormat($order->checkin)." - ".dateFormat($order->checkout) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Wedding Date
                        </td>
                        <td style="width: 70%">
                            {{ dateTimeFormat($order->wedding_date) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Number of Invitations
                        </td>
                        <td style="width: 70%">
                            {{ $order_wedding->number_of_invitation }} guests
                        </td>
                    </tr>
                </table>
                <table class="table-order">
                    <tr>
                        <th colspan="3">Itinerary / Rundown</th>
                    </tr>
                    <tr>
                        <th style="width: 30%;">Date & Time</th>
                        <th style="width: 10%;">Duration</th>
                        <th style="width: 60%;">Service</th>
                    </tr>
                    @php
                        $w_duration = $order->duration;
                    @endphp
                    @for ($iti = 0; $iti <= $w_duration; $iti++)
                        @php
                            $wis = $wedding_itineraries->where('day',$iti+1);
                            $w_day = $iti+1;
                        @endphp
                        @if (count($wis) >0)
                            <tr>
                                <td colspan="3"><b>Day {{ $w_day.'-'.date('l, d M Y',strtotime($order->checkin)) }}</b></td>
                            </tr>
                            @foreach ($wis as $wedding_itinerary)
                                <tr>
                                    <td style="padding-left: 35px;">
                                        {{ date('H.i A',strtotime($wedding_itinerary->time)).' - '.date('H.i A',strtotime('+'.$wedding_itinerary->duration.' hours',strtotime($wedding_itinerary->time))) }}
                                    </td>
                                    <td>
                                        @if ($wedding_itinerary->duration > 1)
                                            {{ $wedding_itinerary->duration."hours" }}
                                        @else
                                            {{ $wedding_itinerary->duration."hour" }}
                                        @endif
                                    </td>
                                    <td>
                                        {!! $wedding_itinerary->itinerary !!}
                                    </td>
                                    @if ($order->status == "pending")
                                        <td>
                                            <div class="table-action">
                                                <a href="#" data-toggle="modal" data-target="#detail-itinerary-{{ $wedding_itinerary->id }}">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <form action="/fdelete-wedding-itinerary/{{ $wedding_itinerary->id }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('delete')
                                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="left" title="Delete Itinerary"><i class="icon-copy fa fa-remove" aria-hidden="true"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    @endfor
                </table>
                </div>
            </div>
        @endif
        {{-- INVOICE EN --}}
        @if (isset($invoice))
            <div class="card-box" style="page-break-after: always;">
                <div class="heading">
                    <table class="table">
                        <tr>
                            <td> 
                                <div class="heading-left">
                                    <div class="business-name">{{ $business->name }}</div>
                                    <div class="business-sub">{{ __('messages.'.$business->caption) }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="heading-right" style="text-align: right;">
                                    <div class="title">INVOICE</div>
                                </div>
                            </td>
                        </tr>
                        <tr class="tb-heading" style="width: 100%">
                            <td>
                            
                            </td>
                            <td>
                                <div class="order-date" style="text-align: right">{{ date('D, d M y',strtotime($order->updated_at)) }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="content">
                    <table class="table tb-head">
                        <tr>
                            <td style="width: 20%;">
                                Invoice No.
                            </td>
                            <td style="width: 30%;">
                                : {{ $invoice->inv_no }}
                            </td>
                            <td style="width: 20%;">
                                Invoice Date
                            </td>
                            <td style="width: 30%;">
                                : {{ dateFormat($invoice->inv_date) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;">
                                Reservation No.
                            </td>
                            <td style="width: 30%;">
                                : {{ $reservation->rsv_no }}
                            </td>
                            <td style="width: 20%;">
                                {{-- Reconfirm Date --}}
                                Payment Dateline
                            </td>
                            <td  style="width: 30%;">
                                : {{ dateFormat($invoice->due_date) }}
                            </td>
                            
                        </tr>
                        <tr>
                            <td style="width: 20%;">
                                Order No.
                            </td>
                            <td style="width: 30%;">
                                : {{  $order->orderno }}
                            </td>
                            <td style="width: 20%;">
                                Sales / Agent
                            </td>
                            <td  style="width: 30%;">
                                : {{  $agent->name }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;">
                                Customer Name
                            </td>
                            <td style="width: 30%;">
                                    @php
                                        $guestName = $bride->where('id',$order->pickup_name)->first();
                                    @endphp
                                @if (isset($guestName))
                                    : {{ $guestName->groom." & ".$guestName->bride }}
                                @else
                                    : - 
                                @endif
                            </td>
                            <td style="width: 20%;">
                                Company / Office
                            </td>
                            <td  style="width: 30%;">
                                : {{ $agent->office }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;">
                                Customer Phone
                                </td>
                                <td style="width: 30%;">
                                    @if (isset($guestName->groom_contact))
                                        : {{ $guestName->groom_contact }}
                                    @else
                                        : - 
                                    @endif
                                </td>
                            <td style="width: 20%;">
                                Contact Number
                            </td>
                            <td  style="width: 30%;">
                                : {{ $agent->phone }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;">
                                @if ($invoice->bank_id == 1)
                                    <b>Rate USD</b>
                                @elseif ($invoice->bank_id == 2)
                                    <b>Rate CNY</b>
                                @elseif ($invoice->bank_id == 3)
                                    <b>Rate TWD</b>
                                @endif
                            </td>
                            <td style="width: 30%;">
                                @if ($invoice->bank_id == 1)
                                    <b>{{ ': Rp ' . number_format($order->usd_rate, 0, ',', '.') }}</b>
                                @elseif ($invoice->bank_id == 2)
                                    <b>{{ ': Rp ' . number_format($order->cny_rate, 0, ',', '.') }}</b>
                                @elseif ($invoice->bank_id == 3)
                                    <b>{{ ': Rp ' . number_format($order->twd_rate, 0, ',', '.') }}</b>
                                @endif
                            </td>
                            <td style="width: 20%;">
                                
                            </td>
                            <td  style="width: 30%;">
                                
                            </td>
                        </tr>
                    </table>
                    
                    <table class="table-order" >
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 5%;">Date</th>
                            <th style="width: 65%;">Description</th>
                            
                            <th style="width: 10%; text-align: center;">Unit/Pax</th>
                            <th style="width: 15%; text-align: center;">Amount</th>
                        </tr>
                        
                        @php
                            $order_suite_and_villa_ids = json_decode($order_wedding->wedding_room_id);
                        @endphp
                        {{-- FIXED SERVICES ------------------------------------------------------------------------ --}}
                        @if ($w_fixed_services_id)
                            <tr>
                                <td><div class="table-service-name">{{ $i++ }}</div></td>
                                <td>
                                    <div class="table-service-name">
                                        {{ date("d M Y",strtotime($order_wedding->wedding_date)) }}
                                    </div>
                                </td>
                                <td><div class="table-service-name">Wedding Package / Include</div></td>
                                <td style="text-align: center;">
                                    1
                                </td>
                                <td style="text-align: right;">
                                    {{ '$ ' . number_format($order_wedding->fixed_service_price + $order_wedding->markup, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        {{-- SUITES AND VILLAS ------------------------------------------------------------------------ --}}
                        @if ($order_suite_and_villa_ids)
                            <tr>
                                <td><div class="table-service-name">{{ $i++ }}</div></td>
                                <td>
                                    <div class="table-service-name">
                                        {{ date("d M Y",strtotime($order->checkin)).' - '.date("d M Y",strtotime($order->checkout)) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="table-service-name">
                                        @foreach ($order_suite_and_villa_ids as $order_suite_and_villa_id)
                                            @php
                                                $wedding_site_and_villa = $rooms->where('id',$order_suite_and_villa_id)->first();
                                            @endphp
                                            @if (count($order_suite_and_villa_ids)>1)
                                                {{ "- ".$wedding_hotel->name.", ".date('F d',strtotime($order->checkin))."th - ".date('F d',strtotime($order->checkout)).'th, '.$order->duration." nights, ".$wedding_site_and_villa->rooms }}<br>
                                            @else
                                                {{ $wedding_hotel->name.", ".date('F d',strtotime($order->checkin))."th - ".date('F d',strtotime($order->checkout)).'th, '.$order->duration." nights, ".$wedding_site_and_villa->rooms }}
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                
                                <td style="text-align: center;">
                                    {{ $order->duration }}
                                </td>
                                <td style="text-align: right;">
                                    {{ '$ ' . number_format($order_wedding->room_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        {{-- WEDDING VENUE ------------------------------------------------------------------------ --}}
                        @if ($w_venues_id)
                            <tr>
                                <td><div class="table-service-name">{{ $i++ }}</div></td>
                                <td>
                                    <div class="table-service-name">
                                        {{ date("d M Y",strtotime($order->wedding_date)) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="table-service-name">
                                        @foreach ($w_venues_id as $w_venue_id)
                                            @php
                                                $wedding_venue = $weddingVenues->where('id',$w_venue_id)->first();
                                            @endphp
                                            @if (count($w_venues_id)>1)
                                                • {{ $wedding_venue->service.", capacity ".$wedding_venue->capacity." guests" }}<br>
                                            @else
                                                {{ $wedding_venue->service.", capacity ".$wedding_venue->capacity." guests" }}
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                
                                <td style="text-align: center;">
                                    {{ count($w_venues_id) }}
                                </td>
                                <td style="text-align: right;">
                                    {{ '$ ' . number_format($order_wedding->venue_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        {{-- MAKEUP ----------------------------------------------------------------------------}}
                        @if ($w_makeups_id)
                            <tr>
                                <td><div class="table-service-name">{{ $i++ }}</div></td>
                                <td>
                                    <div class="table-service-name">
                                        {{ date("d M Y",strtotime($order->wedding_date)) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="table-service-name">
                                        @if ($w_makeups_id)
                                            @foreach ($w_makeups_id as $w_makeup_id)
                                                @php
                                                    $wedding_makeup = $weddingMakeups->where('id',$w_makeup_id)->first();
                                                @endphp
                                                @if (count($w_makeups_id)>1)
                                                    • {{ $wedding_makeup->service." (".$wedding_makeup->duration." ".$wedding_makeup->time.")" }}<br>
                                                @else
                                                    {{ $wedding_makeup->service." (".$wedding_makeup->duration." ".$wedding_makeup->time.")" }}
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                
                                <td style="text-align: center;">
                                    {{ count($w_makeups_id) }}
                                </td>
                                <td style="text-align: right;">
                                    {{ '$ ' . number_format($order_wedding->makeup_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        {{-- DECORATION ----------------------------------------------------------------------------}}
                        @if ($w_decorations_id)
                            <tr>
                                <td><div class="table-service-name">{{ $i++ }}</div></td>
                                <td>
                                    <div class="table-service-name">
                                        {{ date("d M Y",strtotime($order->wedding_date)) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="table-service-name">
                                        @if ($w_decorations_id)
                                            @foreach ($w_decorations_id as $w_decoration_id)
                                                @php
                                                    $wedding_decoration = $weddingDecorations->where('id',$w_decoration_id)->first();
                                                @endphp
                                                @if (count($w_decorations_id)>1)
                                                    • {{ $wedding_decoration->service." (".$wedding_decoration->duration." ".$wedding_decoration->time.")" }}<br>
                                                @else
                                                    {{ $wedding_decoration->service." (".$wedding_decoration->duration." ".$wedding_decoration->time.")" }}
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                
                                <td style="text-align: center;">
                                    {{ count($w_decorations_id) }}
                                </td>
                                <td style="text-align: right;">
                                    {{ '$ ' . number_format($order_wedding->decoration_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        {{-- DINNER VENUE ----------------------------------------------------------------------------}}
                        @if ($w_dinner_venues_id)
                            <tr>
                                <td><div class="table-service-name">{{ $i++ }}</div></td>
                                <td>
                                    <div class="table-service-name">
                                        {{ date("d M Y",strtotime($order->wedding_date)) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="table-service-name">
                                        @if ($w_dinner_venues_id)
                                            @foreach ($w_dinner_venues_id as $w_dinner_venue_id)
                                                @php
                                                    $wedding_dinner_venue = $weddingDinnerVenues->where('id',$w_dinner_venue_id)->first();
                                                @endphp
                                                @if (count($w_dinner_venues_id)>1)
                                                    • {{ $wedding_dinner_venue->service." (".$wedding_dinner_venue->duration." ".$wedding_dinner_venue->time.")" }}<br>
                                                @else
                                                    {{ $wedding_dinner_venue->service." (".$wedding_dinner_venue->duration." ".$wedding_dinner_venue->time.")" }}
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    {{ count($w_dinner_venues_id) }}
                                </td>
                                <td style="text-align: right;">
                                    {{ '$ ' . number_format($order_wedding->dinner_venue_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        {{-- ENTERTAINMENT ----------------------------------------------------------------------------}}
                        @if ($w_entertainments_id)
                            <tr>
                                <td><div class="table-service-name">{{ $i++ }}</div></td>
                                <td>
                                    <div class="table-service-name">
                                        {{ date("d M Y",strtotime($order->wedding_date)) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="table-service-name">
                                        @if ($w_entertainments_id)
                                            @foreach ($w_entertainments_id as $w_entertainment_id)
                                                @php
                                                    $wedding_entertainment = $weddingEntertainments->where('id',$w_entertainment_id)->first();
                                                @endphp
                                                @if (count($w_entertainments_id)>1)
                                                    • {{ $wedding_entertainment->service." (".$wedding_entertainment->duration." ".$wedding_entertainment->time.")" }}<br>
                                                @else
                                                    {{ $wedding_entertainment->service." (".$wedding_entertainment->duration." ".$wedding_entertainment->time.")" }}
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    {{ count($w_entertainments_id) }}
                                </td>
                                <td style="text-align: right;">
                                    {{ '$ ' . number_format($order_wedding->entertainment_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        {{-- DOCUMENTATION ----------------------------------------------------------------------------}}
                        @if ($w_documentations_id)
                            <tr>
                                <td><div class="table-service-name">{{ $i++ }}</div></td>
                                <td>
                                    <div class="table-service-name">
                                        {{ date("d M Y",strtotime($order->wedding_date)) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="table-service-name">
                                        @if ($w_documentations_id)
                                            @foreach ($w_documentations_id as $w_documentation_id)
                                                @php
                                                    $wedding_documentation = $weddingDocumentations->where('id',$w_documentation_id)->first();
                                                @endphp
                                                @if (count($w_documentations_id)>1)
                                                    • {{ $wedding_documentation->service." (".$wedding_documentation->duration." ".$wedding_documentation->time.")" }}<br>
                                                @else
                                                    {{ $wedding_documentation->service." (".$wedding_documentation->duration." ".$wedding_documentation->time.")" }}
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    {{ count($w_documentations_id) }}
                                </td>
                                <td style="text-align: right;">
                                    {{ '$ ' . number_format($order_wedding->documentation_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        {{-- TRANSPORTATION ----------------------------------------------------------------------------}}
                        @if ($w_transportations_id)
                            <tr>
                                <td><div class="table-service-name">{{ $i++ }}</div></td>
                                <td>
                                    <div class="table-service-name">
                                        {{ date("d M Y",strtotime($order->checkin)) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="table-service-name">
                                        @if ($w_transportations_id)
                                            @foreach ($w_transportations_id as $w_transportation_id)
                                                @php
                                                    $wedding_transportation = $weddingTransportations->where('id',$w_transportation_id)->first();
                                                @endphp
                                                @if (count($w_transportations_id)>1)
                                                    • {{ $wedding_transportation->brand." ".$wedding_transportation->name." (Airport Shuttle)" }}<br>
                                                @else
                                                    {{ $wedding_transportation->brand." ".$wedding_transportation->name." (Airport Shuttle)" }}
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    {{ count($w_transportations_id) }}
                                </td>
                                <td style="text-align: right;">
                                    {{ '$ ' . number_format($order_wedding->transport_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        {{-- OTHER SERVICE ----------------------------------------------------------------------------}}
                        @if ($w_others_id)
                            <tr>
                                <td><div class="table-service-name">{{ $i++ }}</div></td>
                                <td>
                                    <div class="table-service-name">
                                        {{ date("d M Y",strtotime($order->wedding_date)) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="table-service-name">
                                        @if ($w_others_id)
                                            @foreach ($w_others_id as $w_other_id)
                                                @php
                                                    $wedding_other = $weddingOthers->where('id',$w_other_id)->first();
                                                @endphp
                                                @if (count($w_others_id)>1)
                                                    • {{ $wedding_other->service." (".$wedding_other->duration." ".$wedding_other->time.")" }}<br>
                                                @else
                                                    {{ $wedding_other->service." (".$wedding_other->duration." ".$wedding_other->time.")" }}
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    {{ count($w_others_id) }}
                                </td>
                                <td style="text-align: right;">
                                    {{ '$ ' . number_format($order_wedding->other_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        {{-- ADDITIONAL SERVICE --}}
                        @if (isset($order->additional_service))
                            @if ($order->additional_service != "null")
                                @php
                                    $adser = json_decode($order->additional_service);
                                    $adser_date = json_decode($order->additional_service_date);
                                    $adser_qty = json_decode($order->additional_service_qty);
                                    $adser_price = json_decode($order->additional_service_price);
                                    if (isset($adser)) {
                                        $cad = count($adser);
                                    }else{
                                        $cad = 0;
                                    }
                                    $adser_total_price = 0;
                                @endphp
                                @for ($v = 0; $v < $cad; $v++)
                                    @php
                                        $adser_tp = $adser_price[$v]*$adser_qty[$v];
                                        $adser_total_price = $adser_total_price + $adser_tp;
                                    @endphp
                                    <tr>
                                        <td><div class="table-service-name">{{ $i++ }}</div></td>
                                        <td>
                                            <div class="table-service-name">
                                                {{ date("d M Y",strtotime($adser_date[$v])) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-service-name">
                                                {{ date('l, m/d/Y ',strtotime($adser_date[$v])).", ".$adser[$v] }}
                                            </div>
                                        </td>
                                        
                                        <td style="text-align: center;">
                                            {{ $adser_qty[$v] }}
                                        </td>
                                        <td style="text-align: right;">
                                            {{ currencyFormatUsd($adser_price[$v]*$adser_qty[$v]) }}
                                        </td>
                                    </tr>
                                @endfor
                            @else
                                @php
                                    $adser_total_price = 0;
                                @endphp
                            @endif
                        @else
                            @php
                                $adser_total_price = 0;
                            @endphp
                        @endif
                        <tr>
                            <td colspan="4" style="text-align: center; font-size:1rem; font-weight:600;">Total</td>
                            <td style="text-align: right; font-size:1rem; font-weight:600;">
                                {{ currencyFormatUsd($order->final_price + $order->kick_back +$order->bookingcode_disc + $order->discounts + $promotion_disc) }}
                            </td>
                        </tr>
                    </table>
                    <table class="table-list m-t-18 m-b-18">
                        <tbody>
                            <tr style="text-align: right;">
                                <td colspan="2" style="width:40%;"></td>
                                <td style="width:20%;">Services</td>
                                <td style="width: 20%">USD</td>
                                <td style="width: 20%">{{ currencyFormatUsd($order->final_price + $order->kick_back +$order->bookingcode_disc + $order->discounts + $promotion_disc) }}</td>
                            </tr>
                            <tr style="text-align: right;">
                                <td colspan="2" style="width:40%;"></td>
                                <td style="width:20%;">Tax</td>
                                <td >USD</td>
                                <td style="width: 20%">$ 0</td>
                            </tr>
                            @if ($order->kick_back > 0)
                                <tr style="text-align: right;">
                                    <td colspan="2" style="width:40%;"></td>
                                    <td style="width:20%;">Kick Back</td>
                                    <td >USD</td>
                                    <td style="width: 20%">{{ "-$ ". number_format($order->kick_back) }}</td>
                                </tr>
                            @endif
                            @if ($order->bookingcode_disc > 0)
                                <tr style="text-align: right;">
                                    <td colspan="2" style="width:40%;"></td>
                                    <td style="width:20%;">Booking Code</td>
                                    <td>USD</td>
                                    <td style="width: 20%">{{ "-$ ". number_format($order->bookingcode_disc) }}</td>
                                </tr>
                            @endif
                            @if ($order->discounts > 0)
                                <tr style="text-align: right;">
                                    <td colspan="2" style="width:40%;"></td>
                                    <td style="width:20%;">Discounts</td>
                                    <td>USD</td>
                                    <td style="width: 20%">{{ "-$ ". number_format($order->discounts) }}</td>
                                </tr>
                            @endif
                            @if ($promotion_disc > 0)
                                <tr style="text-align: right;">
                                    <td colspan="2" style="width:40%;"></td>
                                    <td style="width:20%;">Promotions</td>
                                    <td>USD</td>
                                    <td style="width: 20%">{{ "-$ ". number_format($promotion_disc) }}</td>
                                </tr>
                            @endif
                            @if($invoice->kurs->name == "CNY")
                                <tr style="text-align: right; color:gray !important;">
                                    <td colspan="2" style="width:40%;"></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>Total</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>USD</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></td>
                                </tr>
                                <tr style="text-align: right;">
                                    <td colspan="2" style="width:40%;"></td>
                                    <td style="width:20%;" class="final-price"><b>Total</b></td>
                                    <td style="width:20%;" class="final-price"><b>CNY</b></td>
                                    <td style="width: 20%" class="final-price"><b>{{ "¥ ". number_format($invoice->total_cny) }}</b></td>
                                </tr>
                            @elseif($invoice->kurs->name == "TWD")
                                <tr style="text-align: right; color:gray !important;">
                                    <td colspan="2" style="width:40%;"></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>Total</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>USD</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></td>
                                </tr>
                                <tr style="text-align: right;">
                                    <td colspan="2" style="width:40%;"></td>
                                    <td style="width:20%;" class="final-price"><b>Total</b></td>
                                    <td style="width:20%;" class="final-price"><b>TWD</b></td>
                                    <td style="width: 20%" class="final-price"><b>{{ currencyFormatUsd($invoice->total_twd) }}</b></td>
                                </tr>
                            @elseif($invoice->kurs->name == "IDR")
                                <tr style="text-align: right; color:gray !important;">
                                    <td colspan="2" style="width:40%;"></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>Total</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>USD</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></td>
                                </tr>
                                <tr style="text-align: right;">
                                    <td colspan="2" style="width:40%;"></td>
                                    <td style="width:20%;" class="final-price"><b>Total</b></td>
                                    <td style="width:20%;" class="final-price"><b>IDR</b></td>
                                    <td style="width: 20%" class="final-price"><b>{{ currencyFormatIdr($invoice->total_idr) }}</b></td>
                                </tr>
                            @else
                                <tr style="text-align: right;">
                                    <td colspan="2" style="width:40%;"></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>Total</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>USD</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    @if ($bankAccount)
                        <div class="bank-account">
                            <div class="tb-container">
                                <table class="table tb-list">
                                    <tr>
                                        <td style="width: 15%;">
                                            Bank
                                        </td>
                                        <td style="width: 35%;">
                                            {{ $bankAccount->bank }}
                                        </td>
                                        <td style="width:15%;">
                                            Swift Code
                                        </td>
                                        <td style="width: 35%;">
                                            {{ $bankAccount->swift_code }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width:15%;">
                                            Name
                                        </td>
                                        <td style="width: 35%;">
                                            {{ $bankAccount->name }}
                                        </td>
                                        <td style="width:15%;">
                                            Telephone
                                        </td>
                                        <td style="width: 35%;">
                                            {{ $bankAccount->telephone }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width:15%;">
                                            Account IDR
                                        </td>
                                        <td style="width: 35%;">
                                            {{ $bankAccount->account_idr }}
                                        </td>
                                        <td style="width: 15%;">
                                            Address
                                        </td>
                                        <td style="width: 35%;">
                                            {{ $bankAccount->address }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width:15%;">
                                            Account USD
                                        </td>
                                        <td colspan="2" style="width: 35%;">
                                            {{ $bankAccount->account_usd }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <br>
                                        <i>* The transfer fees and administrative costs will be charged to the sender.</i>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @endif
                    <div class="additional-info">
                        <br>
                        <p>We would like to remind you to follow the following steps in the payment process:<br>
                        1. After receiving our confirmation letter, please review the details of the reservation in the invoice sent.<br>
                        2. If the invoice received is accurate, kindly proceed with the payment before the 'Payment Dateline' mentioned in the invoice.<br>
                        3. If the payment has been completed, please upload the 'proof of payment' in our system.<br>
                        Thank you for your attention and cooperation.
                        </p>
                    </div>
                </div>
            </div>
        @endif
        @include('emails.orderContractWeddingZh')
        <div class="btn-print hide-print">
            <button onclick="window.print();">Print</button>
        </div>
    </body>
</html>