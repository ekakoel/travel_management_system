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
            <div class="title">PAYMENT CONFIRMATION</div>
            <div class="confirm-box">
                <div class="confirm-by">{{ $agent->name." (".$agent->code.")" }}</div>
                <div class="order-date" style="text-align: right">{{ date('D, d M y',strtotime($now)) }}</div>
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
                        Payment Dateline
                    </td>
                    <td style="width: 70%">
                        {{ dateFormat($order->reservations->invoice->due_date) }}<br>
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
                @if ($order->service == "Tour Package")
                    <tr>
                        <td style="width: 30%">
                            Tour Package
                        </td>
                        <td style="width: 70%">
                            {{ $order->subservice }}
                        </td>
                    </tr>
                @elseif($order->service == "Hotel")
                    <tr>
                        <td style="width: 30%">
                           Hotel
                        </td>
                        <td style="width: 70%">
                            {{ $order->servicename }}
                        </td>
                    </tr>
                @elseif($order->service == "Activity")
                    <tr>
                        <td style="width: 30%">
                           Hotel
                        </td>
                        <td style="width: 70%">
                            {{ $order->subservice }}
                        </td>
                    </tr>
                @elseif($order->service == "Transport")
                    <tr>
                        <td style="width: 30%">
                           Transport
                        </td>
                        <td style="width: 70%">
                            {{ $order->servicename }}
                        </td>
                    </tr>
                @endif
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
                        Amount
                    </td>
                    <td style="width: 70%">
                        {{ currencyFormatUsd($order->final_price) }}
                    </td>
                </tr>
            </table>
            <div class="notification-text">
                Agent {{ $agent->name }} has confirmed the payment for their order.<br>
                Carefully inspect the payment proof and match it with the placed order before finalizing the transaction.<br>
                Please proceed with the payment validation process by using the following link! <a href="{{ $order_link }}">Detail Order</a><br>
                This email is sent automatically by the online system at https://online.balikamitour.com. Please do not reply to this email.
            </div>
        </div>
    </div>
    