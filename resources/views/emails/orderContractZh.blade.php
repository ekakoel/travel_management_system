@php
    $r=1;
    $i=1;
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation {{ $order->orderno }}</title>
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
        .tb-head tr{
            line-height: 0.9;
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
            font-size: 0.7rem !important;
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
            font-size: 0.7rem;
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
        .table-title{
            font-size: 1rem;
            font-weight: 700;
            border-bottom: 1px solid grey !important;
            padding: 4px 18px !important;
            background-color: #696969;
            color: white;
        }
        .final-price{
            font-family: "notosans" !important;
            font-size: 0.9rem;
        }
        .additional-info p{
            line-height: 1.3;
        }
        .guest-container{
            display: flex;
            gap: 27px;
            flex-wrap: wrap;
        }

        .guest-list span{
            padding-right: 20px;
        }
        
        @media print {
            .hide-print {
                display: none;
            }
        }
        @media (max-width: 579px) {
            .guest-container{
                gap: 8px
            }
        }
    </style>
</head>
    <body>
        {{-- CONTRACT ZH --}}
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
                                <div class="title">合約</div>
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
                            預約編號.
                        </td>
                        <td style="width: 30%;">
                            {{ $reservation->rsv_no }}
                        </td>
                        @if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package")
                            <td style="width: 20%;">
                                抵達航班
                            </td>
                            <td style="width: 30%;">
                                @if (isset($order->arrival_flight))
                                    {{ $order->arrival_flight }}
                                @else
                                    ..........................
                                @endif
                            </td>
                        @else
                            <td style="width: 20%;">
                                取車日期
                            </td>
                            <td style="width: 30%;">
                                @if (isset($order->pickup_date))
                                {{ dateTimeFormat($order->pickup_date) }}
                                @else
                                    ..........................
                                @endif
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            預約日期
                        </td>
                        <td style="width: 30%;">
                            {{ dateFormat($reservation->created_at) }}
                        </td>
                        @if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package")
                            <td style="width: 20%;">
                                抵達時間
                            </td>
                            <td style="width: 30%;">
                                @if ($order->arrival_time)
                                    {{ dateFormat($order->arrival_time) }}
                                @else
                                    ..........................
                                @endif
                            </td>
                        @else
                            <td style="width: 20%;">
                                取車地點
                            </td>
                            <td style="width: 30%;">
                                @if (isset($order->pickup_location))
                                    {{ $order->pickup_location }}
                                @else
                                    ..........................
                                @endif
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            取車姓名
                        </td>
                        <td style="width: 30%;">
                            @if (isset($pickup_people))
                                {{ $pickup_people->name }}
                            @else
                                ..........................
                            @endif
                        </td>
                        @if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package")
                            <td style="width: 20%;">
                                出發航班
                            </td>
                            <td style="width: 30%;">
                                @if (isset($order->departure_flight))
                                    {{ $order->departure_flight }}
                                @else
                                    ..........................
                                @endif
                            </td>
                        @else
                            <td style="width: 20%;">
                                交車日期
                            </td>
                            <td style="width: 30%;">
                                @if (isset($order->dropoff_date))
                                {{ dateTimeFormat($order->dropoff_date) }}
                                @else
                                    ..........................
                                @endif
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            取車電話
                        </td>
                        <td style="width: 30%;">
                            @if (isset($pickup_people))
                                {{ $pickup_people->phone }}
                            @else
                                ..........................
                            @endif
                        </td>
                        @if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package")
                            <td style="width: 20%;">
                                出發時間
                            </td>
                            <td style="width: 30%;">
                                @if ($order->departure_time)
                                    {{ dateFormat($order->departure_time) }}
                                @else
                                    ..........................
                                @endif
                            </td>
                        @else
                            <td style="width: 20%;">
                                交車地點
                            </td>
                            <td style="width: 30%;">
                                @if (isset($order->dropoff_location))
                                    {{ $order->dropoff_location }}
                                @else
                                    ..........................
                                @endif
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            銷售代理
                        </td>
                        <td style="width: 30%;">
                            {{ $agent->name }}
                        </td>
                        @if (isset($guide->name))
                            <td style="width: 20%;">
                                導遊姓名
                            </td>
                            <td style="width: 30%;">
                                {{ $guide->name }}
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            辦公室
                        </td>
                        <td style="width: 30%;">
                            {{ $agent->office }}
                        </td>
                        @if (isset($guide->phone))
                            <td style="width: 20%;">
                                導遊電話
                            </td>
                            <td style="width: 30%;">
                                {{ $guide->phone }}
                            </td>
                        @endif
                    </tr>
                    @if (isset($driver->name))
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="width: 20%;">
                                司機姓名
                            </td>
                            <td style="width: 30%;">
                                {{ $driver->name }}
                            </td>
                        </tr>
                    @endif
                    @if (isset($driver->phone))
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="width: 20%;">
                                司機電話
                            </td>
                            <td style="width: 30%;">
                                {{ $driver->phone }}
                            </td>
                        </tr>
                    @endif
                </table>
                <hr class="hr-line">
                {{-- GUEST --}}
                <div class="subtitle"><b>客戶詳情</b></div>
                @if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package")
                    <div class="guest-container">
                        @if ($guest_name)
                            @php
                                $x = count($guest_name);
                            @endphp
                            <div class="guest-list">
                                @for ($k = 0; $k < $x; $k++)
                                    @php
                                        $nr = $k + 1;
                                    @endphp
                                    <span>{!! $nr.". ". $guest_name[$k]->name." ". $guest_name[$k]->name_mandarin !!} </span>
                                @endfor
                            </div>
                        @else
                            @php
                                $guest_detail = json_decode($order->guest_detail);
                                if (isset($guest_detail)) {
                                    $cgdt = count($guest_detail);
                                    $ng = 1;
                                }else{
                                    $cgdt = 0;
                                    $ng = 1;
                                }
                            @endphp
                            @if ($cgdt>0)
                                <div class="guest-list">
                                    @for ($g = 0; $g < $cgdt; $g++)
                                        <span> {!! $ng++.". ". $guest_detail[$g] !!}</span>
                                    @endfor
                                </div>
                            @endif
                        @endif
                    </div>
                @else
                    <div class="normal-text">{!! $order->guest_detail !!}</div>
                @endif
                {{-- ORDER DETAIL --}}
                <table class="table-order m-t-18">
                    <tr>
                        @if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package")
                            <th colspan="2">住宿</th>
                        @elseif($order->service == "Tour Package")
                            <th colspan="2">旅遊套裝</th>
                        @elseif($order->service == "Activity")
                            <th colspan="2">活動</th>
                        @elseif($order->service == "Transport")
                            <th colspan="2">交通</th>
                        @else
                            <th colspan="2">服務</th>
                        @endif
                    </tr>
                    @if ($order->service == "Tour Package")
                        <tr>
                            <td style="width: 30%">
                                訂單編號
                            </td>
                            <td style="width: 70%">
                                <b>{{ $order->orderno }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                確認編號
                            </td>
                            <td style="width: 70%">
                                @if (isset($order->confirmation_order))
                                    <b>{{ $order->confirmation_order }}</b>
                                @else
                                    <b>-</b>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                旅遊套裝
                            </td>
                            <td style="width: 70%">
                                {{ $order->servicename }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                客人數量
                            </td>
                            <td style="width: 70%">
                                {{ $order->number_of_guests." guests" }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                旅遊開始
                            </td>
                            <td style="width: 70%">
                                {{ dateFormat($order->checkin) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                旅遊結束
                            </td>
                            <td style="width: 70%">
                                {{ dateFormat($order->checkout) }}
                            </td>
                        </tr>
                    @elseif ($order->service == "Hotel")
                        <tr>
                            <td style="width: 30%">
                                訂單編號
                            </td>
                            <td style="width: 70%">
                                <b>{{ $order->orderno }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                確認編號
                            </td>
                            <td style="width: 70%">
                                @if (isset($order->confirmation_order))
                                    <b>{{ $order->confirmation_order }}</b>
                                @else
                                    <b>-</b>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                飯店名稱
                            </td>
                            <td style="width: 70%">
                                {{ $order->servicename }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                套房和別墅
                            </td>
                            <td style="width: 70%">
                                {{ $order->subservice }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                入住
                            </td>
                            <td style="width: 70%">
                                {{ dateFormat($order->checkin) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                退房
                            </td>
                            <td style="width: 70%">
                                {{ dateFormat($order->checkout) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                持續時間
                            </td>
                            <td style="width: 70%">
                                {{ $order->duration." Night" }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                客人數量
                            </td>
                            <td style="width: 70%">
                                {{ $order->number_of_guests." Guests" }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                房間數量
                            </td>
                            <td style="width: 70%">
                                {{ $order->number_of_room." Unit" }}
                            </td>
                        </tr>
                        @if ($jml_extra_bed > 0)
                            <tr>
                                <td style="width: 30%">
                                    加床
                                </td>
                                <td style="width: 70%">
                                    {{ $jml_extra_bed." Unit" }}
                                </td>
                            </tr>
                        @endif
                    @elseif ($order->service == "Hotel Promo")
                        <tr>
                            <td style="width: 30%">
                                訂單編號
                            </td>
                            <td style="width: 70%">
                                <b>{{ $order->orderno }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                確認編號
                            </td>
                            <td style="width: 70%">
                                @if (isset($order->confirmation_order))
                                    <b>{{ $order->confirmation_order }}</b>
                                @else
                                    <b>-</b>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                服務
                            </td>
                            <td style="width: 70%">
                                {{ $order->service }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                飯店名稱
                            </td>
                            <td style="width: 70%">
                                {{ $order->servicename }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                套房和別墅
                            </td>
                            <td style="width: 70%">
                                {{ $order->subservice }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                辦理登記
                            </td>
                            <td style="width: 70%">
                                {{ dateFormat($order->checkin) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                結帳離開
                            </td>
                            <td style="width: 70%">
                                {{ dateFormat($order->checkout) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                持續時間
                            </td>
                            <td style="width: 70%">
                                {{ $order->duration." Night" }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                客人數量
                            </td>
                            <td style="width: 70%">
                                {{ $order->number_of_guests." Guests" }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                房間數量
                            </td>
                            <td style="width: 70%">
                                {{ $order->number_of_room." Unit" }}
                            </td>
                        </tr>
                        @if ($jml_extra_bed > 0)
                            <tr>
                                <td style="width: 30%">
                                    加床
                                </td>
                                <td style="width: 70%">
                                    {{ $jml_extra_bed." Unit" }}
                                </td>
                            </tr>
                        @endif
                    @elseif ($order->service == "Hotel Package")
                        <tr>
                            <td style="width: 30%">
                                訂單編號
                            </td>
                            <td style="width: 70%">
                                <b>{{ $order->orderno }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                            確認編號
                            </td>
                            <td style="width: 70%">
                                @if (isset($order->confirmation_order))
                                    <b>{{ $order->confirmation_order }}</b>
                                @else
                                    <b>-</b>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                服務
                            </td>
                            <td style="width: 70%">
                                {{ $order->service }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                飯店名稱
                            </td>
                            <td style="width: 70%">
                                {{ $order->servicename }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                套房和別墅
                            </td>
                            <td style="width: 70%">
                                {{ $order->subservice }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                辦理登記
                            </td>
                            <td style="width: 70%">
                                {{ dateFormat($order->checkin) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                退房
                            </td>
                            <td style="width: 70%">
                                {{ dateFormat($order->checkout) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                持續時間
                            </td>
                            <td style="width: 70%">
                                {{ $order->duration." Night" }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                客人數量
                            </td>
                            <td style="width: 70%">
                                {{ $order->number_of_guests." Guests" }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                房間數量
                            </td>
                            <td style="width: 70%">
                                {{ $order->number_of_room." Unit" }}
                            </td>
                        </tr>
                        @if ($jml_extra_bed > 0)
                            <tr>
                                <td style="width: 30%">
                                    加床
                                </td>
                                <td style="width: 70%">
                                    {{ $jml_extra_bed." Unit" }}
                                </td>
                            </tr>
                        @endif
                    @elseif ($order->service == "Activity")
                        <tr>
                            <td style="width: 30%">
                                訂單編號
                            </td>
                            <td style="width: 70%">
                                <b>{{ $order->orderno }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                            確認編號
                            </td>
                            <td style="width: 70%">
                                @if (isset($order->confirmation_order))
                                    <b>{{ $order->confirmation_order }}</b>
                                @else
                                    <b>-</b>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                活動
                            </td>
                            <td style="width: 70%">
                                {{ $order->servicename }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                活動日期
                            </td>
                            <td style="width: 70%">
                                {{ dateFormat($order->checkin) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                客人數量
                            </td>
                            <td style="width: 70%">
                                {{ $order->number_of_guests." Guests" }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                客人姓名
                            </td>
                            <td style="width: 70%">
                                {{ $order->guest_detail }}
                            </td>
                        </tr>
                    @elseif ($order->service == "Transport")
                        <tr>
                            <td style="width: 30%">
                                訂單編號
                            </td>
                            <td style="width: 70%">
                                <b>{{ $order->orderno }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                            確認編號
                            </td>
                            <td style="width: 70%">
                                @if (isset($order->confirmation_order))
                                    <b>{{ $order->confirmation_order }}</b>
                                @else
                                    <b>-</b>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                交通
                            </td>
                            <td style="width: 70%">
                                {{ $order->servicename }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                容量
                            </td>
                            <td style="width: 70%">
                                {{ $order->capacity." Seat" }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                客人數量
                            </td>
                            <td style="width: 70%">
                                {{ $order->number_of_guests." Guests" }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                進入
                            </td>
                            <td style="width: 70%">
                                {{ dateFormat($order->checkin) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                出
                            </td>
                            <td style="width: 70%">
                                {{ dateFormat($order->checkout) }}
                            </td>
                        </tr>
                    @elseif ($order->service == "Wedding Package")
                        <tr>
                            <td style="width: 30%">
                                婚禮套餐
                            </td>
                            <td style="width: 70%">
                                {{ $order->servicename }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                抵達
                            </td>
                            <td style="width: 70%">
                                {{ dateFormat($order->checkin) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                出發
                            </td>
                            <td style="width: 70%">
                                {{ dateFormat($order->checkout) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%">
                                婚禮日期
                            </td>
                            <td style="width: 70%">
                                {{ dateFormat($order->travel_date) }}
                            </td>
                        </tr>
                    @else
                        子服務 -<br>
                    @endif
                </table>
                {{-- Additional 服務 --}}
                @if(isset($optional_rate_orders) or isset($order->additional_service))
                    <table class="table-order">
                        <tr>
                            <th colspan="4">額外費用</th>
                        </tr>
                        @foreach ($optional_rate_orders as $optional_rate_order)
                            @if ($optional_rate_order && $optional_rate_order->optional_rate)
                                <tr>
                                    <td style="width:20%">{{ date('d M Y', strtotime($optional_rate_order->service_date)) }}</td>
                                    <td style="width:40%">{!! $optional_rate_order->optional_rate->name !!}</td>
                                    <td style="width:20%">{!! $optional_rate_order->number_of_guest . " pax" !!}</td>
                                    @if ($order->service == "Private Villa")
                                        <td style="width:20%">{!! $villa->name !!}</td>
                                    @else
                                        <td style="width:20%">{!! $hotel->name !!}</td>
                                    @endif
                                </tr>
                            @endif
                        @endforeach
                    </table>
                    <table class="table-order">
                        @if(isset($order->additional_service))
                            @if ($order->additional_service != "null")
                                @php
                                    $additional_service = json_decode($order->additional_service);
                                    $additional_service_date = json_decode($order->additional_service_date);
                                    $additional_service_qty = json_decode($order->additional_service_qty);
                                    $additional_service_price = json_decode($order->additional_service_price);
                                    if (isset($additional_service)) {
                                        $c_adser = count($additional_service);
                                    }else{
                                        $c_adser = 0;
                                    }
                                @endphp
                                <tr style="margin-top: 8px">
                                    <th colspan="4">額外服務</th>
                                </tr>
                                @for ($adser = 0; $adser < $c_adser; $adser++)
                                    <tr>
                                        <td style="width:20%">{{ date('d M Y',strtotime($additional_service_date[$adser])) }}</td>
                                        <td style="width:40%">{!! $additional_service[$adser] !!}</td>
                                        <td style="width:20%">{!! $additional_service_qty[$adser]." pax" !!}</td>
                                        <td style="width:20%">-</td>
                                    </tr>
                                @endfor
                            @endif
                        @endif
                    </table>
                @endif
                @if (isset($airport_shuttles))
                    @if(count($airport_shuttles)>0)
                        <table class="table-order">
                            <tr>
                                <th colspan="4">交通</th>
                            </tr>
                            @foreach ($airport_shuttles as $airport_shuttle)
                                <tr>
                                    <td style="width:20%">{{ date('d M Y',strtotime($airport_shuttle->date)) }}</td>
                                    <td style="width:30%">@lang('messages.Airport Shuttle'), {{ $airport_shuttle->transport?->brand." ".$airport_shuttle->transport?->name.", ".$airport_shuttle->duration." hours, ".$airport_shuttle->distance."KM" }}</td>
                                    <td style="width:20%">1 Unit</td>
                                    <td style="width:30%">{{ $airport_shuttle->src." - ".$airport_shuttle->dst }}</td>
                                </tr>
                            @endforeach
                        </table>
                    @endif
                @endif
                {{-- ADDITIONAL INFORMATION --}}
                <table class="table-order">
                    <tr>
                        <th colspan="2">附加資訊</th>
                    </tr>
                    @if (isset($order->benefits))
                        <tr>
                            <td style="width: 20%">好處</td>
                            <td style="width: 80%">{!! $order->benefits !!}</td>
                        </tr>
                    @endif
                    @if (isset($order->destinations))
                        <tr>
                            <td style="width: 20%">目的地</td>
                            <td style="width: 80%">{!! $order->destinations !!}</td>
                        </tr>
                    @endif
                    @if (isset($order->itinerary))
                        <tr>
                            <td style="width: 20%">行程</td>
                            <td style="width: 80%">{!! $order->itinerary !!}</td>
                        </tr>
                    @endif
                    @if (isset($order->include))
                        <tr>
                            <td style="width: 20%">包括</td>
                            <td style="width: 80%">{!! $order->include !!}</td>
                        </tr>
                    @endif
                    @if (isset($order->additional_info))
                        <tr>
                            <td style="width: 20%">資訊</td>
                            <td style="width: 80%">{!! $order->additional_info !!}</td>
                        </tr>
                    @endif
                </table>
                @if (isset($order->note))
                    <table class="table-order">
                        <tr>
                            <th style="max-width: 100%">備註</th>
                        </tr>
                        <tr>
                            <td>{!! $order->note !!}</td>
                        </tr>
                    </table>
                @endif
                <div class="notification-text">
                    感謝您支持 {{ $business->name }}。如果您有任何問題或需要進一步協助， 請隨時聯繫我們的客戶服務， 電子郵件： reservation@balikamitour.com， 電話：{{ $business->phone }}。
                    {{-- Thank you for your support of {{ $business->name }}! If you have any questions or need further assistance, please feel free to contact our customer service at email: reservation@balikamitour.com or phone: {{ $business->phone }} --}}
                </div>
            </div>
        </div>
        {{-- INVOICE ZH --}}
        @if (isset($invoice))
            <div class="card-box">
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
                                    <div class="title">發票</div>
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
                                發票編號
                            </td>
                            <td style="width: 30%;">
                                {{ $invoice->inv_no }}
                            </td>
                            <td style="width: 20%;">
                                顧客姓名
                            </td>
                            <td style="width: 30%;">
                                    @php
                                        $guestName = $guest_name->where('id',$order->pickup_name)->first();
                                    @endphp
                                @if (isset($guestName))
                                    {{ $guestName->name }}
                                @else
                                    - 
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;">
                                預約編號
                            </td>
                            <td style="width: 30%;">
                                {{  $reservation->rsv_no }}
                            </td>
                            <td style="width: 20%;">
                                顧客電話
                            </td>
                            <td style="width: 30%;">
                                @if (isset($guestName->phone))
                                    {{ $guestName->phone }}
                                @else
                                    - 
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;">
                                訂單編號
                            </td>
                            <td style="width: 30%;">
                                {{  $order->orderno }}
                            </td>
                            <td style="width: 20%;">
                                銷售代理
                            </td>
                            <td  style="width: 30%;">
                                {{  $agent->name }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;">
                                發票日期
                            </td>
                            <td style="width: 30%;">
                                {{ dateFormat($invoice->inv_date) }}
                            </td>
                            <td style="width: 20%;">
                                公司
                            </td>
                            <td  style="width: 30%;">
                                {{ $agent->office }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;">
                                付款截止日期
                            </td>
                            <td  style="width: 30%;">
                                {{ dateFormat($invoice->due_date) }}
                            </td>
                            <td style="width: 20%;">
                                電話號碼
                            </td>
                            <td  style="width: 30%;">
                                {{ $agent->phone }}
                            </td>
                        </tr>
                    </table>
                    
                    <table class="table-order" >
                            <tr>
                                <th style="width: 5%;">數字</th>
                                <th style="width: 40%;">描述</th>
                                <th style="width: 15%;">速度 USD</th>
                                <th style="width: 10%;">單位 / 包裹</th>
                                <th style="width: 10%;">夜晚 / 時代</th>
                                <th style="width: 20%;">數量</th>
                            </tr>
                            <tr>
                                <td><div class="table-service-name">{{ $i++ }}</div></td>
                                <td>
                                    <div class="table-service-name">
                                        @if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package" )
                                            {{ "Date ". date('m/d',strtotime($order->checkin))." - ".date('m/d',strtotime($order->checkout)).", ".$order->servicename }}
                                        @elseif($order->service == "Transport")
                                            {{ "Date ". date('m/d',strtotime($order->checkin))." - ".date('m/d',strtotime($order->checkout)).", ".$order->servicename."(".$order->capacity." seat), ".$order->service_type.", ".$order->dst." to ".$order->src }}
                                        @else
                                            {{ "Date ". date('m/d',strtotime($order->checkin))." - ".date('m/d',strtotime($order->checkout)).", ".$order->servicename }}
                                        @endif
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    {{ currencyFormatUsd($order->price_pax) }}
                                </td>
                                <td style="text-align: right;">
                                    @if ($order->service == "Tour Package")
                                        {{ $order->number_of_guests }}
                                    @elseif($order->service == "Activity")
                                        {{ $order->number_of_guests }}
                                    @elseif($order->service == "Transport")
                                        1
                                    @else
                                        {{ $order->number_of_room }}
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @if ($order->service == "Hotel" || $order->service == "Private Villa" || $order->service == "Hotel Promo" || $order->service == "Hotel Package" || $order->service == "Tour Package" )
                                        {{ $order_duration }}
                                    @else
                                        1
                                    @endif
                                    
                                </td>
                                <td style="text-align: right;">
                                    @if ($order->service == "Hotel" || $order->service == "Private Villa" || $order->service == "Hotel Promo" || $order->service == "Hotel Package" || $order->service == "Tour Package" )
                                        {{ currencyFormatUsd($order->normal_price) }}
                                    @else
                                        {{ currencyFormatUsd($order->price_total) }}
                                    @endif
                                </td>
                            </tr>
                            {{-- EXTRA BED --}}
                            @if ($jml_extra_bed > 0)
                                <tr>
                                    <td><div class="table-service-name">{{ $i++ }}</div></td>
                                    <td>
                                        <div class="table-service-name">
                                            {{"Date ". date('m/d',strtotime($order->checkin))." - ".date('m/d',strtotime($order->checkout)).", Extra Bed" }}
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        {{ currencyFormatUsd(((($extrabed_price/$jml_extra_bed)/$jml_extra_bed)/$order->duration)) }}
                                    </td>
                                    <td style="text-align: right;">
                                        {{ $jml_extra_bed }}
                                    </td>
                                    <td style="text-align: right;">
                                        {{ $order->duration }}
                                    </td>
                                    <td style="text-align: right;">
                                        {{ currencyFormatUsd($extrabed_price/$jml_extra_bed) }}
                                    </td>
                                </tr>
                            @endif
                            {{-- ADDITIONAL CHARGE --}}
                            @if ($optional_rate_orders)
                                @foreach ($optional_rate_orders as $optional_rate_order)
                                    <tr>
                                        <td><div class="table-service-name">{{ $i++ }}</div></td>
                                        <td>
                                            <div class="table-service-name">
                                                {{"Date ". date('m/d',strtotime($optional_rate_order->service_date)) }}, {{ $optional_rate_order->optional_rate->name }}
                                            </div>
                                        </td>
                                        <td style="text-align: right;">
                                            {{ currencyFormatUsd($optional_rate_order->price_pax) }}
                                        </td>
                                        <td style="text-align: right;">
                                            {{ $optional_rate_order->number_of_guest }}
                                        </td>
                                        <td style="text-align: right;">
                                            1
                                        </td>
                                        <td style="text-align: right;">
                                            {{ currencyFormatUsd($optional_rate_order->price_total) }}
                                        </td>
                                    </tr>
                                @endforeach
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
                                                    {{"Date ". date('m/d',strtotime($adser_date[$v])).", ".$adser[$v] }}
                                                </div>
                                            </td>
                                            <td style="text-align: right;">
                                                {{ currencyFormatUsd($adser_price[$v]) }}
                                            </td>
                                            <td style="text-align: right;">
                                                {{ $adser_qty[$v] }}
                                            </td>
                                            <td style="text-align: right;">
                                                1
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
                            
                            {{-- AIRPORT SHUTTLE --}}
                            @if (isset($airport_shuttles))
                                @foreach ($airport_shuttles as $inv_airport_shuttle)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ "Date ".date('m/d',strtotime($inv_airport_shuttle->date)). ", Airport Shuttle, ".$inv_airport_shuttle->src." - ".$inv_airport_shuttle->dst.", ". $inv_airport_shuttle->transport?->brand." ".$inv_airport_shuttle->transport?->name }}</td>
                                        <td style="text-align: right;">
                                            {{ currencyFormatUsd($inv_airport_shuttle->price) }}
                                        </td>
                                        <td style="text-align: right;">
                                            1
                                        </td>
                                        <td style="text-align: right;">
                                            1
                                        </td>
                                        <td style="text-align: right;">
                                            {{ currencyFormatUsd($inv_airport_shuttle->price) }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                    </table>
                    {{-- {{ $invoice->bank_id }} --}}
                    <table class="table-list m-t-18 m-b-18">
                        <tbody>
                            @uiEnabled('doku-payment')
                                <tr>
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;"></td>
                                    <td colspan="3" class="table-title">
                                        自助付款
                                    </td>
                                </tr>
                            @endUiEnabled
                            <tr style="text-align: right;">
                                <td style="width:20%;"></td>
                                <td style="width:20%;"></td>
                                <td style="width:20%;">服務</td>
                                <td style="width: 20%">USD</td>
                                <td style="width: 20%">{{ currencyFormatUsd($invoice->total_usd + $order->kick_back + $order->discounts + $order->bookingcode_disc + $promotion_disc) }}</td>
                            </tr>
                            <tr style="text-align: right;">
                                <td style="width:20%;"></td>
                                <td style="width:20%;"></td>
                                <td style="width:20%;">稅</td>
                                <td >USD</td>
                                <td style="width: 20%">$ 0</td>
                            </tr>
                            @if ($order->kick_back > 0)
                                <tr style="text-align: right;">
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;">反沖</td>
                                    <td >USD</td>
                                    <td style="width: 20%; color:#bb0000">{{ "- ".currencyFormatUsd($order->kick_back) }}</td>
                                </tr>
                            @endif
                            @if ($order->bookingcode_disc > 0)
                                <tr style="text-align: right;">
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;">預訂代碼</td>
                                    <td>USD</td>
                                    <td style="width: 20%; color:#bb0000">{{ "- ".currencyFormatUsd($order->bookingcode_disc) }}</td>
                                </tr>
                            @endif
                            @if ($order->discounts > 0)
                                <tr style="text-align: right;">
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;">折扣</td>
                                    <td>USD</td>
                                    <td style="width: 20%; color:#bb0000">{{ "- ".currencyFormatUsd($order->discounts) }}</td>
                                </tr>
                            @endif
                            @if ($promotion_disc > 0)
                                <tr style="text-align: right;">
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;">促銷活動</td>
                                    <td>USD</td>
                                    <td style="width: 20%; color:#bb0000">{{ "- ".currencyFormatUsd($promotion_disc) }}</td>
                                </tr>
                            @endif
                            @if($invoice->currency->name == "CNY")
                                <tr style="text-align: right;">
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;"></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>總</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>USD</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></td>
                                </tr>
                                <tr style="text-align: right;">
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;" class="final-price"><b>總</b></td>
                                    <td style="width:20%;" class="final-price"><b>CNY</b></td>
                                    <td style="width: 20%" class="final-price"><b>{{ "¥ ". number_format($invoice->total_cny) }}</b></td>
                                </tr>
                            @elseif($invoice->currency->name == "TWD")
                                <tr style="text-align: right;">
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;"></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>總</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>USD</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></td>
                                </tr>
                                <tr style="text-align: right;">
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;" class="final-price"><b>總</b></td>
                                    <td style="width:20%;" class="final-price"><b>TWD</b></td>
                                    <td style="width: 20%" class="final-price"><b>{{ currencyFormatTwd($invoice->total_twd) }}</b></td>
                                </tr>
                            @elseif($invoice->currency->name == "IDR")
                                <tr style="text-align: right;">
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;"></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>總</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>USD</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></td>
                                </tr>
                                <tr style="text-align: right;">
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;" class="final-price"><b>總</b></td>
                                    <td style="width:20%;" class="final-price"><b>IDR</b></td>
                                    <td style="width: 20%" class="final-price"><b>{{ currencyFormatIdr($invoice->total_idr) }}</b></td>
                                </tr>
                            @else
                                <tr style="text-align: right;">
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;"></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>總</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>USD</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>{{ currencyFormatUsd($invoice->total_usd) }}</b></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    @uiEnabled('doku-payment')
                        <table class="table-list m-t-18 m-b-18">
                            <tbody>
                                <tr>
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;"></td>
                                    <td colspan="3" class="table-title">
                                        DOKU 付款
                                    </td>
                                </tr>
                                <tr style="text-align: right;">
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;">服務</td>
                                    <td style="width: 20%">IDR</td>
                                    <td style="width: 20%">{{ currencyFormatIdr($order->final_price * $usdrates->rate) }}</td>
                                </tr>
                                <tr style="text-align: right;">
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;">稅 ({{ $tax_doku->tax_rate }})</td>
                                    <td >IDR</td>
                                    <td style="width: 20%">{{ currencyFormatIdr(($order->final_price * $usdrates->rate)*$tax_doku->tax_rate) }}</td>
                                </tr>
                                <tr style="text-align: right;">
                                    <td style="width:20%;"></td>
                                    <td style="width:20%;"></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>總</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>IDR</b></td>
                                    <td style="width:20%; border-top: 1px solid grey;" class="final-price"><b>{{ currencyFormatIdr(($order->final_price * $usdrates->rate)+(($order->final_price * $usdrates->rate)*$tax_doku->tax_rate)) }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    @endUiEnabled
                    <div class="bank-account">
                        <div class="tb-container">
                            <table class="table tb-list">
                                <tr>
                                    <td style="width: 15%;">
                                        銀行
                                    </td>
                                    <td style="width: 35%;">
                                        {{ $bankAccount->bank }}
                                    </td>
                                    <td style="width:15%;">
                                        SWIFT 代碼
                                    </td>
                                    <td style="width: 35%;">
                                        {{ $bankAccount->swift_code }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:15%;">
                                        姓名
                                    </td>
                                    <td style="width: 35%;">
                                        {{ $bankAccount->name }}
                                    </td>
                                    <td style="width:15%;">
                                        電話
                                    </td>
                                    <td style="width: 35%;">
                                        {{ $bankAccount->telephone }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:15%;">
                                        帳戶IDR
                                    </td>
                                    <td style="width: 35%;">
                                        {{ $bankAccount->account_idr }}
                                    </td>
                                    <td style="width: 15%;">
                                        地址
                                    </td>
                                    <td style="width: 35%;">
                                        {{ $bankAccount->address }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:15%;">
                                        帳戶USD
                                    </td>
                                    <td colspan="2" style="width: 35%;">
                                        {{ $bankAccount->account_usd }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <br>
                                       <i>* 转账费用和行政费用将由付款方承担。</i>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="additional-info">
                        <br>
                        <p>
                        我們謹提醒您在付款過程中遵循以下步驟：<br>
                        1. 收到我們的確認函後，請查閱發送的發票中的預訂詳情。<br>
                        2. 如果收到的發票正確無誤，請在發票中提及的“付款截止日期”前完成支付。<br>
                        3. 如果付款已完成，請將「付款證明」上傳至我們的系統。<br>
                        @uiEnabled('doku-payment')
                            4. 使用 DOKU Payment 付款只能以印尼盾 (IDR) 进行。<br>
                            5. 付款可以自行支付或通过 DOKU Payment 进行。<br>
                            6. 通过 DOKU Payment 进行的每笔付款将收取 3% 或 0.03 的税费。<br>
                        @endUiEnabled
                        感謝您的關注與合作。
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </body>
</html>