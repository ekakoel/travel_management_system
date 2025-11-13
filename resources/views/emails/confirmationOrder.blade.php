<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation {{ $order->orderno }}</title>
	<meta charset="utf-8">
	<style>
        body{
            width: 100%;
            padding:0;
            margin: 0;
            font-family: sans-serif; 
        }
        .card-box{
            padding: 18px;
        }
        .heading{
            width: 100%;
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
            font-size: 2rem;
            font-family: sans-serif;
            font-weight: 700;
            text-transform: capitalize;
        }
        .card-box .confirm-box{
            display: flex;
            padding: 8px;
            background-color: red;
            width: max-content;
            align-items: center;
        }
        .card-box .confirm-by{
            max-width: fit-content;
            padding-right: 170px;
            font-size: 1rem;
            font-weight: 600;
        }
        .business-name{
            font-size: 1.8rem;
            font-weight: 600;
        }
        .business-sub{
            font-style: italic;
        }
        .table-container{
            display: flex;
        }
        table {
            margin: 18px 0;
            border-collapse: collapse;
            border: none;
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

        .table-order th {
            border:1PX solid black;
            background-color: #696969;
            text-align: left;
            padding: 8px 8px;
            color: white;
        }

        .table-order td {
            padding: 4px 8px;
            border: 1PX solid black;
            text-align: left;
        }

        .table-order tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .content{
            font-size: 0.8rem;
            padding: 8px 0;
        }
        .content .notification-text{
            font-style: italic;
        }
        .subtitle{
            font-size: 0.9rem;
            font-weight: 800;
            padding-bottom: 8px;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
    <div class="card-box">
        <div class="heading">
            <div class="title">CONFIRMATION ORDER</div>
            <div class="confirm-box">
                <div class="confirm-by">Confirm by {{ $admin->name }}</div>
                <div class="order-date" style="text-align: right">{{ date('D, d M y',strtotime($order->updated_at)) }}</div>
            </div>
        </div>
        <div class="content">
            <h2 style="padding: 0 !important; margin: 0 !important;">{{ 'Order no:  '.$order->orderno }}</h2>
            <table class="table-order tb-list">
                <tr>
                    <th colspan="2">Order</th>
                </tr>
                 <tr>
                    <td style="width: 30%">
                        Order Number
                    </td>
                    <td style="width: 70%">
                        {{ $order->orderno }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%">
                        Service
                    </td>
                    <td style="width: 70%">
                        {{ $order->service }}<br>
                    </td>
                </tr>
                @if (isset($invs))
                    @php
                        $due_date=date('Y-m-d', strtotime("-7 days", strtotime($order->checkin)));
                    @endphp
                    <tr>
                        <td style="width: 30%">
                            Reconfirm Date
                        </td>
                        <td style="width: 70%">
                            {{ date('D, d M y',strtotime($due_date)) }}<br>
                        </td>
                    </tr>
                @endif
                @if ($order->service == "Tour Package")
                    <tr>
                        <td style="width: 30%">
                            Tour Package
                        </td>
                        <td style="width: 70%">
                            {{ $order->servicename }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Number of Guests
                        </td>
                        <td style="width: 70%">
                            {{ $order->number_of_guests." guests" }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Guests Name
                        </td>
                        <td style="width: 70%">
                            {{ $order->guest_detail }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Travel Start
                        </td>
                        <td style="width: 70%">
                            {{ dateFormat($order->checkin) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Travel End
                        </td>
                        <td style="width: 70%">
                            {{ dateFormat($order->checkout) }}
                        </td>
                    </tr>
                @elseif ($order->service == "Private Villa")
                    <tr>
                        <td style="width: 30%">
                            Villa
                        </td>
                        <td style="width: 70%">
                            {{ $order->servicename }}
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="width: 30%">
                            Duration
                        </td>
                        <td style="width: 70%">
                            {{ $order->duration." Night" }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Check in
                        </td>
                        <td style="width: 70%">
                            {{ dateFormat($order->checkin) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Check out
                        </td>
                        <td style="width: 70%">
                            {{ dateFormat($order->checkout) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Number of Room
                        </td>
                        <td style="width: 70%">
                            {{ $order->number_of_room." Unit" }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Number of Guests
                        </td>
                        <td style="width: 70%">
                            {{ $order->number_of_guests }} {{ $order->number_of_guests > 1?" guests":" guest" }}
                        </td>
                    </tr>
                @elseif ($order->service == "Hotel")
                    <tr>
                        <td style="width: 30%">
                            Hotel Name
                        </td>
                        <td style="width: 70%">
                            {{ $order->servicename }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Suites & Villa
                        </td>
                        <td style="width: 70%">
                            {{ $order->subservice }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Duration
                        </td>
                        <td style="width: 70%">
                            {{ $order->duration." Night" }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Number of Room
                        </td>
                        <td style="width: 70%">
                            {{ $order->number_of_room." Unit" }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Check in
                        </td>
                        <td style="width: 70%">
                            {{ dateFormat($order->checkin) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Check out
                        </td>
                        <td style="width: 70%">
                            {{ dateFormat($order->checkout) }}
                        </td>
                    </tr>
                @elseif ($order->service == "Hotel Promo")
                    <tr>
                        <td style="width: 30%">
                            Hotel Name
                        </td>
                        <td style="width: 70%">
                            {{ $order->servicename }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Suites & Villa
                        </td>
                        <td style="width: 70%">
                            {{ $order->subservice }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Duration
                        </td>
                        <td style="width: 70%">
                            {{ $order->duration." Night" }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Number of Room
                        </td>
                        <td style="width: 70%">
                            {{ $order->number_of_room." Unit" }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Check in
                        </td>
                        <td style="width: 70%">
                            {{ dateFormat($order->checkin) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Check out
                        </td>
                        <td style="width: 70%">
                            {{ dateFormat($order->checkout) }}
                        </td>
                    </tr>
                @elseif ($order->service == "Hotel Package")
                    <tr>
                        <td style="width: 30%">
                            Hotel Name
                        </td>
                        <td style="width: 70%">
                            {{ $order->servicename }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Suites & Villa
                        </td>
                        <td style="width: 70%">
                            {{ $order->subservice }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Suites & Villa
                        </td>
                        <td style="width: 70%">
                            {{ $order->subservice }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Duration
                        </td>
                        <td style="width: 70%">
                            {{ $order->duration." Night" }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Number of Room
                        </td>
                        <td style="width: 70%">
                            {{ $order->number_of_room." Unit" }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Check in
                        </td>
                        <td style="width: 70%">
                            {{ dateFormat($order->checkin) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Check out
                        </td>
                        <td style="width: 70%">
                            {{ dateFormat($order->checkout) }}
                        </td>
                    </tr>
                @elseif ($order->service == "Activity")
                    <tr>
                        <td style="width: 30%">
                            Activity
                        </td>
                        <td style="width: 70%">
                            {{ $order->servicename }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Activity Date
                        </td>
                        <td style="width: 70%">
                            {{ dateFormat($order->checkin) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Number of Guests
                        </td>
                        <td style="width: 70%">
                            {{ $order->number_of_guests }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Guests Name
                        </td>
                        <td style="width: 70%">
                            {{ $order->guest_detail }}
                        </td>
                    </tr>
                @elseif ($order->service == "Transport")
                    <tr>
                        <td style="width: 30%">
                            Transport
                        </td>
                        <td style="width: 70%">
                            {{ $order->servicename }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            In
                        </td>
                        <td style="width: 70%">
                            {{ dateFormat($order->checkin) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Out
                        </td>
                        <td style="width: 70%">
                            {{ dateFormat($order->checkout) }}
                        </td>
                    </tr>
                @elseif ($order->service == "Wedding Package")
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
                            Bride's
                        </td>
                        <td style="width: 70%">
                            {{ $bride->groom." & ".$bride->bride }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Arrival
                        </td>
                        <td style="width: 70%">
                            {{ dateFormat($order->checkin) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Departure
                        </td>
                        <td style="width: 70%">
                            {{ dateFormat($order->checkout) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Wedding date
                        </td>
                        <td style="width: 70%">
                            {{ dateTimeFormat($order->wedding_date) }}
                        </td>
                    </tr>
                @else
                    Subservice: -<br>
                @endif
            </table>
            <div class="notification-text">
                Thank you for your order. We inform you that your order has been successfully received and confirmed.<br>
                For more information regarding your order and payment, you can find it in the attached file.<br>
                Please provide your approval for your order before the Reconfirm Date to prevent the release of your order, <br>
                By using the following link <a href="{{ $order_link }}">Detail Order</a><br>
            </div>
            <br>
            <br>
            Best Regards,<br>
            Reservation<br><br>
            {{ $admin->name }}<br>
        </div>
    </div>
    