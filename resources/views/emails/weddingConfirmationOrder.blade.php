<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation {{ $orderWedding->orderno }}</title>
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
                <div class="order-date" style="text-align: right">{{ date('D, d M y',strtotime($orderWedding->updated_at)) }}</div>
            </div>
        </div>
        <div class="content">
            <h2 style="padding: 0 !important; margin: 0 !important;">{{ 'Order no:  '.$orderWedding->orderno }}</h2>
            <table class="table-order tb-list">
                <tr>
                    <th colspan="2">Wedding Order</th>
                </tr>
                @if (isset($invs))
                    @php
                        $due_date=date('Y-m-d', strtotime("-7 days", strtotime($orderWedding->checkin)));
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
                <tr>
                    <td style="width: 30%">
                        Service
                    </td>
                    <td style="width: 70%">
                        {{ $orderWedding->service }}<br>
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%">
                        Wedding Venue
                    </td>
                    <td style="width: 70%">
                        {{ $orderWedding->hotel->name }}<br>
                    </td>
                </tr>
                @if ($orderWedding->service == "Wedding Package")
                    <tr>
                        <td style="width: 30%">
                            Wedding Package
                        </td>
                        <td style="width: 70%">
                            {{ $orderWedding->wedding->name }}
                        </td>
                    </tr>
                @endif
                <tr>
                    <td style="width: 30%">
                        Bride's
                    </td>
                    <td style="width: 70%">
                        {{ $bride->groom }} {{ $bride->groom_chinese?" (".$bride->groom_chinese.")":""; }} & {{ $bride->bride }} {{ $bride->bride_chinese?" (".$bride->bride_chinese.")":""; }}
                    </td>
                </tr>
                @if ($orderWedding->service == "Wedding Package")
                    <tr>
                        <td style="width: 30%">
                            Wedding Date
                        </td>
                        <td style="width: 70%">
                            {{ date('l, d M Y', strtotime($orderWedding->wedding_date)) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Slot
                        </td>
                        <td style="width: 70%">
                            {{ date('h:i a', strtotime($orderWedding->slot)) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Reservation Date
                        </td>
                        <td style="width: 70%">
                            {{ date('l, d M Y (h:i a)', strtotime($orderWedding->reception_date_start)) }}
                        </td>
                    </tr>
                @elseif($orderWedding->service == "Ceremony Venue")
                    <tr>
                        <td style="width: 30%">
                            Wedding Date
                        </td>
                        <td style="width: 70%">
                            {{ date('l, d M Y', strtotime($orderWedding->wedding_date)) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            Slot
                        </td>
                        <td style="width: 70%">
                            {{ date('h:i a', strtotime($orderWedding->slot)) }}
                        </td>
                    </tr>
                @elseif($orderWedding->service == "Ceremony Venue")
                    <tr>
                        <td style="width: 30%">
                            Reception Date
                        </td>
                        <td style="width: 70%">
                            {{ date('l, d M Y (h:i a)', strtotime($orderWedding->reception_date_start)) }}
                        </td>
                    </tr>
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