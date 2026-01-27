@php
    $r=1;
    $i=1;
    $admin_reservation = Auth::user()->where('id',$invoice->created_by)->first();
    $imgData = base64_encode(file_get_contents('storage/logo/logo-color-bali-kami.png'));
    $imgSrc = 'data:image/png;base64,' . $imgData;
    $stmpelData = base64_encode(file_get_contents('storage/logo/stempel_bali_kami_group.png'));
    $stmpelSrc = 'data:image/png;base64,' . $stmpelData;
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>{{ $order_wedding->orderno }}</title>
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
            font-size: 0.9rem !important;
        }
        .m-b-18{
            margin-bottom: 18px;
        }
        .m-t-18{
            margin-top: 18px;
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
        .stampel{
            max-width: 100px !important;
            rotate: -3deg;
        }
        .table-container{
            display: flex;
        }
        .text-left{
            text-align: left !important;
        }
        .text-center{
            text-align: center !important;
        }
        .text-right{
            text-align: right !important;
        }
        .tabel-heading{
            font-size: 1rem;
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
            background-color: red;
            font-size: 1rem;
            font-weight: 600;
            padding: 8px 18px;
            color: white;
        }
        .tb-heading td{
            padding: 8px 18px;
            word-wrap: break-word;
        }
        .tb-head tr{
            line-height: 0.9;
        }
        
        .table-list td{
            padding: 1px 8px;
            border: none;
            vertical-align: text-top;
            word-wrap: break-word;
        }
        .table-order{
            width: 100% !important;
        }
        .table-order th {
            border: 1px solid #121212;
            background-color: #ababab;
            text-align: left;
            padding: 8px;
            color: rgb(41, 41, 41);
            font-size: 0.8rem;
        }
        .table-order p{
            margin: 0 !important;
            font-size: 0.8rem !important;
        }
        .table-order td {
            padding: 4px 8px;
            border: 1px solid #575757;
            text-align: left;
            vertical-align: text-top;
            word-wrap: break-word;
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
        .logo-contract img{
            max-width: 250px;
        }
        .m-b-18{
            margin-bottom: 18px !important;
        }
        .p-b-18{
            padding-bottom: 18px !important;
        }
        .p-b-8{
            padding-bottom: 8px !important;
        }
        .border-bottom{
            border-bottom: 1px solid #c7c7c7;
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
            border: none;
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
                            <div class="logo-contract">
                                <img src="{{ $imgSrc }}" alt="Logo Bali Kami Group">
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
                            <div class="order-date" style="text-align: right">{{ date('D, d F y',strtotime($order_wedding->updated_at)) }}</div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="content">
                <table class="table tb-head border-bottom m-b-18">
                    <tr>
                        <td>
                            <div class="heading-left">
                                <div class="business-name">{{ $business->name }}</div>
                                <div class="business-sub p-b-8">{{ __('messages.'.$business->caption) }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
                <table class="table tb-head">
                    
                    <tr>
                        <td style="width: 20%;">
                            預訂號碼
                        </td>
                        <td style="width: 30%;">
                            : {{ $reservation->rsv_no }}
                        </td>
                       
                        <td style="width: 20%;">
                            抵達航班
                        </td>
                        <td style="width: 30%;">
                            @if (isset($order_wedding->arrival_flight))
                                : {{ $order_wedding->arrival_flight }}
                            @else
                                : ..........................
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            預訂日期
                        </td>
                        <td style="width: 30%;">
                            : {{ date('D, m/d/Y',strtotime($reservation->created_at)) }}
                        </td>
                        
                        <td style="width: 20%;">
                            抵達時間
                        </td>
                        <td style="width: 30%;">
                            @if ($order_wedding->arrival_time)
                                : {{ dateTimeFormat($order_wedding->arrival_time) }}
                            @else
                                : ..........................
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            接送人姓名
                        </td>
                        <td style="width: 30%;">
                            @if (isset($bride))
                                : Mr.{{ $bride->groom." ".$bride->bride_chinese }}
                            @else
                                : ..........................
                            @endif
                        </td>
                        <td style="width: 20%;">
                            出發航班
                        </td>
                        <td style="width: 30%;">
                            @if (isset($order_wedding->departure_flight))
                                : {{ $order_wedding->departure_flight }}
                            @else
                                : ..........................
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            接送人電話
                        </td>
                        <td style="width: 30%;">
                            @if (isset($bride))
                                : {{ $bride->bride_contact }}
                            @else
                                : ..........................
                            @endif
                        </td>
                        <td style="width: 20%;">
                            離境航班
                        </td>
                        <td style="width: 30%;">
                            @if ($order_wedding->departure_time)
                                : {{ dateTimeFormat($order_wedding->departure_time) }}
                            @else
                                : ..........................
                            @endif
                        </td>
                        
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            銷售 / 代理人
                        </td>
                        <td style="width: 30%;">
                            : {{ $agent->name }}
                        </td>
                        @if (isset($guide->name))
                            <td style="width: 20%;">
                                導遊姓名
                            </td>
                            <td style="width: 30%;">
                                : {{ $guide->name }}
                            </td>
                        @endif
                        
                    </tr>
                    <tr>
                        <td style="width: 20%;">
                            辦公室
                        </td>
                        <td style="width: 30%;">
                            : {{ $agent->office }}
                        </td>
                        @if (isset($guide->phone))
                            <td style="width: 20%;">
                                導遊電話
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
                                司機姓名
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
                                司機電話
                            </td>
                            <td style="width: 30%;">
                                : {{ $driver->phone }}
                            </td>
                        </tr>
                    @endif
                </table>
                {{-- WEDDING DETAIL --}}
                <table class="table-order m-t-18">
                    <tr>
                        <th colspan="2">婚禮細節</th>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            新郎
                        </td>
                        <td style="width: 70%">
                            Mr. {{ $bride->groom." ".$bride->groom_chinese }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            新娘
                        </td>
                        <td style="width: 70%">
                            Mrs. {{ $bride->bride." ".$bride->bride_chinese }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            婚禮場地
                        </td>
                        <td style="width: 70%">
                            {{ $order_wedding->hotel->name }}
                        </td>
                    </tr>
                    @if ($order_wedding->ceremony_venue_id)
                        <tr>
                            <td style="width: 30%">
                                婚禮日期
                            </td>
                            <td style="width: 70%">
                                {{ dateFormat($order_wedding->wedding_date) }} ({{ date('H.i',strtotime($order_wedding->slot)) }})
                            </td>
                        </tr>
                    @endif
                    @if ($order_wedding->reception_venue_id)
                        <tr>
                            <td style="width: 30%">
                                接待日期
                            </td>
                            <td style="width: 70%">
                                {{ dateTimeFormat($order_wedding->reception_date_start) }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="width: 30%">
                            邀請函數量
                        </td>
                        <td style="width: 70%">
                            {{ $order_wedding->number_of_invitation }} 邀請函
                        </td>
                    </tr>
                </table>
                {{-- FLIGHT --}}
                @if (count($flights)>0)
                    <table class="table-order">
                        <tr>
                            <th colspan="4">航班時間表</th>
                        </tr>
                        <tr>
                            <th style="max-width: 20%">日期</th>
                            <th  style="max-width: 20%">航班號碼</th>
                            <th  style="max-width: 20%">類型</th>
                            <th  style="max-width: 40%">客人數量</th>
                        </tr>
                        @foreach ($flights as $flight)
                            <tr>
                                <td>{{ dateTimeFormat($flight->time) }}</td>
                                <td>{{ $flight->flight }}</td>
                                <td>{{ $flight->type }}</td>
                                <td>{{ $flight->number_of_guests }}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif
                {{-- SERVICES INCLUDE --}}
                <table class="table-order">
                    <tr>
                        <th colspan="4">服務 (包括)</th>
                    </tr>
                    <tr>
                        <th style="max-width: 20%">日期</th>
                        <th  style="max-width: 20%">服務</th>
                        <th  style="max-width: 40%">描述</th>
                        <th  style="max-width: 20%">邀請函</th>
                    </tr>
                    @if ($order_wedding->room_bride_id)
                        <tr>
                            <td>
                                {{ dateFormat($order_wedding->checkin)." ".date('(h.i a)',strtotime($order_wedding->hotel->checkin)) }}
                                -
                                {{ dateFormat($order_wedding->checkout)." ".date('(h.i a)',strtotime($order_wedding->hotel->checkout)) }} ({{ $order_wedding->duration."N" }})
                            </td>
                            <td>
                                <p>套房 / 別墅(新娘)</p>
                            </td>
                            <td>
                                {{ $order_wedding->suite_villa->rooms }}
                            </td>
                            <td style="text-align: center">2</td>
                        </tr>
                    @endif
                    @if ($order_wedding->ceremony_venue)
                        <tr>
                            <td>{{ dateTimeFormat($order_wedding->wedding_date) }}</td>
                            <td>
                                <p>儀式場地</p>
                            </td>
                            <td>
                                {{ $order_wedding->ceremony_venue->name }}
                            </td>
                            <td style="text-align: center">{{ $order_wedding->number_of_invitation }}</td>
                        </tr>
                    @endif
                    @if ($order_wedding->ceremony_venue_decoration_id)
                        <tr>
                            <td>{{ dateTimeFormat($order_wedding->wedding_date) }}</td>
                            <td>
                                <p>儀式場地裝飾</p>
                            </td>
                            <td>
                                {{ $order_wedding->ceremony_venue_decoration->service }}
                            </td>
                            <td style="text-align: center">{{ $order_wedding->number_of_invitation }}</td>
                        </tr>
                    @endif
                    @if ($order_wedding->reception_venue)
                        <tr>
                            <td>{{ dateTimeFormat($order_wedding->reception_date_start) }}</td>
                            <td>
                                <p>接待場地</p>
                            </td>
                            <td>
                                {{ $order_wedding->reception_venue->name }}
                            </td>
                            <td style="text-align: center">{{ $order_wedding->reception_venue_invitations }}</td>
                        </tr>
                    @endif
                    @if ($order_wedding->lunch_venue)
                        <tr>
                            <td>
                                {{ dateTimeFormat($order_wedding->lunch_venue_date) }}
                            </td>
                            <td>
                                <p>午餐場地</p>
                            </td>
                            <td>
                                {{ $order_wedding->lunch_venue->name }}
                            </td>
                            <td style="text-align: center">{{ $order_wedding->number_of_invitation }}</td>
                        </tr>
                    @endif
                    @if ($order_wedding->dinner_venue)
                        <tr>
                            <td>
                                {{ dateTimeFormat($order_wedding->dinner_venue_date) }}
                            </td>
                            <td>
                                <p>晚宴場地</p>
                            </td>
                            <td>
                                {{ $order_wedding->dinner_venue->name }}
                            </td>
                            <td style="text-align: center">{{ $order_wedding->number_of_invitation }}</td>
                        </tr>
                    @endif
                    @if ($transport_id)
                        @php
                            $wedding_transport = $transports->where('id',$transport_id)->first();
                        @endphp
                        @if ($wedding_transport)
                            <tr>
                                <td>
                                    {{ dateTimeFormat($order_wedding->checkin) }}
                                </td>
                                <td>
                                    <p>交通 <i>(機場接駁車 - 抵達)</i></p>
                                </td>
                                <td>
                                    {{ $wedding_transport->brand }}, {{ $wedding_transport->name }} 
                                </td>
                                <td style="text-align: center">2</td>
                            </tr>
                            <tr>
                                <td>
                                    {{ dateTimeFormat($order_wedding->checkin) }}
                                </td>
                                <td>
                                    <p>交通 <i>(機場接駁車 - 離開)</i></p>
                                </td>
                                <td>
                                    {{ $wedding_transport->brand }}, {{ $wedding_transport->name }} 
                                </td>
                                <td style="text-align: center">2</td>
                            </tr>
                        @endif
                    @endif
                    @if ($other_service_ids)
                        @php
                            $cosid = count($other_service_ids);
                        @endphp
                        @for ($j = 0; $j < $cosid; $j++)
                            @php
                                $other_service = $other_services->where('id',$other_service_ids[$j])->first();
                            @endphp
                            @if ($other_service)
                                <tr>
                                    <td>
                                        @if ($other_service->venue == "Ceremony Venue")
                                            {{ dateTimeFormat($order_wedding->wedding_date) }}
                                        @elseif ($other_service->venue == "Reception Venue")
                                            {{ dateTimeFormat($order_wedding->reception_date_start) }}
                                        @else
                                            {{ dateTimeFormat($order_wedding->checkin)." - ".dateTimeFormat($order_wedding->checkout) }}
                                        @endif
                                    </td>
                                    <td>
                                        額外的 服務 (<i>包括</i>)
                                    </td>
                                    <td>
                                        {{ $other_service->service }}
                                    </td>
                                    <td style="text-align: center">-</td>
                                </tr>
                            @endif
                        @endfor
                    @endif
                    
                </table>
                {{-- ADDITIONAL SERVICE --}}
                @if (count($wedding_accommodations)>0 or count($additional_transports)>0)
                    <table class="table-order">
                        <tr>
                            <th colspan="4">額外的 服務 (收費)</th>
                        </tr>
                        <tr>
                            <th style="max-width: 20%">日期</th>
                            <th style="max-width: 20%">服務</th>
                            <th style="max-width: 40%">描述</th>
                            <th style="max-width: 20%">邀請函</th>
                        </tr>
                        @if (count($wedding_accommodations)>0)
                            @foreach ($wedding_accommodations as $wedding_accommodation)
                                <tr>
                                    <td>
                                        {{ dateFormat($wedding_accommodation->checkin)." ".date('(h.i a)',strtotime($wedding_accommodation->hotel->checkin)) }}
                                        -
                                        {{ dateFormat($wedding_accommodation->checkout)." ".date('(h.i a)',strtotime($wedding_accommodation->hotel->checkout)) }}
                                    </td>
                                    <td>
                                        <p>套房 / 別墅</p>
                                    </td>
                                    <td>
                                        {{ $wedding_accommodation->room->rooms }}{{ $wedding_accommodation->extra_bed?" + 加床":"" }}
                                    </td>
                                    <td style="text-align: center">{{ $wedding_accommodation->number_of_guests }}</td>
                                </tr>
                            @endforeach
                        @endif
                        @if ($additional_transports)
                            @foreach ($additional_transports as $additional_transport)
                                <tr>
                                    <td>
                                        {{ dateTimeFormat($additional_transport->date) }}
                                    </td>
                                    <td>
                                        @if ($additional_transport->desc_type == "In")
                                            <p>交通 <i>(機場接駁車 - 抵達)</i></p>
                                        @else
                                            <p>交通 <i>(機場接駁車 - 離開)</i></p>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $additional_transport->transport->brand }}, {{ $additional_transport->transport->name }}
                                    </td>
                                    <td style="text-align: center">{{ $additional_transport->number_of_guests }}</td>
                                </tr>
                            @endforeach
                        @endif
                        @if ($additional_charges)
                            @foreach ($additional_charges as $additional_charge)
                                <tr>
                                    <td>
                                        {{ dateTimeFormat($additional_charge->date) }}
                                    </td>
                                    <td>
                                        <p>{{ $additional_charge->service }}</p>
                                    </td>
                                    <td>
                                        @if ($additional_charge->note)
                                            {!! $additional_charge->note !!}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td style="text-align: center">{{ $additional_charge->quantity }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                @endif
                {{-- ADDITIONAL INFORMATION --}}
                @if ($order_wedding->service == "Wedding Package")
                    <table class="table-order">
                        <tr>
                            <th colspan="2">接待場地</th>
                        </tr>
                        @if ($wedding->include)
                            <tr>
                                <td style="width: 20%">包括</td>
                                <td style="width: 80%">{!! $wedding->include !!}</td>
                            </tr>
                        @endif
                        @if ($wedding->description)
                            <tr>
                                <td style="width: 20%">描述</td>
                                <td style="width: 80%">{!! $wedding->description !!}</td>
                            </tr>
                        @endif
                        @if ($wedding->additional_info)
                            <tr>
                                <td style="width: 20%">接待場地</td>
                                <td style="width: 80%">{!! $wedding->additional_info !!}</td>
                            </tr>
                        @endif
                        @if ($wedding->cancellation_policy)
                            <tr>
                                <td style="width: 20%">取消政策</td>
                                <td style="width: 80%">{!! $wedding->cancellation_policy !!}</td>
                            </tr>
                        @endif
                        @if ($wedding->payment_process)
                            <tr>
                                <td style="width: 20%">付款流程</td>
                                <td style="width: 80%">{!! $wedding->payment_process !!}</td>
                            </tr>
                        @endif
                    </table>
                @endif
                @if ($order_wedding->remark)
                    <table class="table-order">
                        <tr>
                            <th style="max-width: 100%">備註</th>
                        </tr>
                        <tr>
                            <td>{!! $order_wedding->remark !!}</td>
                        </tr>
                    </table>
                @endif
                <table class="table-order" style="margin-bottom: -8px !important;">
                    <tr>
                        <th style="width: 50%">備註</th>
                        <th style="width: 50%">公司</th>
                    </tr>
                    <tr>
                        <td rowspan="2"></td>
                        <td style="padding: 8px !important;">
                            <b>{{ $business->name }}</b>
                            <p>許可證 : {{ $business->license }}</p>
                            <p>地址 : {{ $business->address }}</p>
                            <p>TAX number : {{ $business->tax_id }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px !important;">
                            @if ($bank_account)
                                <b>BANK ACCOUNT ({{ $bank_account->currency }})</b>
                                <p>Code : {{ $bank_account->bank_code }}</p>
                                <p>地址 : {{ $bank_account->address }}</p>
                                <p>姓名 : {{ $bank_account->name }}</p>
                                <p>號碼  : {{ $bank_account->number }}</p>
                                <p>電話  : {{ $bank_account->telephone }}</p>
                            @endif
                        </td>
                    </tr>
                    
                    <tr>
                        <th style="max-width: 50%">公司印章</th>
                        <th style="max-width: 50%">公司印章</th>
                    </tr>
                    <tr>
                        <td style="padding:18px 18px 18px 28px !important;"></td>
                        <td style="padding:18px 18px 18px 28px !important;">
                            <img class="stampel" src="{{ $stmpelSrc }}" alt="Stampel Bali Kami Group">
                        </td>
                </table>
                <table class="table-order">
                    <tr>
                        <th style="max-width: 33%">巴厘島聯絡人</th>
                        <th style="max-width: 33%">星期一 1-5 公司電話</th>
                        <th style="max-width: 33%">緊急聯絡人</th>
                    </tr>
                    <tr>
                        <td style="padding: 8px !important;">
                            {{ config('app.bali_contact_person_name_i') }} : {{ config('app.bali_contact_person_phone_i') }}<br>
                            {{ config('app.bali_contact_person_name_ii') }} : {{ config('app.bali_contact_person_phone_ii') }}
                        </td>
                        <td style="padding: 8px !important;">
                            {{ config('app.company_phone_number_i') }}<br>
                            {{ config('app.company_phone_number_ii') }}<br>
                            {{ config('app.company_phone_number_iii') }}<br>
                        </td>
                        <td style="padding: 8px !important;">
                            {{ config('app.emergency_contact_name_i') }} : {{ config('app.emergency_contact_phone_i') }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        {{-- INVOICE EN --}}
        @if ($invoice)
            <div class="card-box">
                <div class="heading">
                    <table class="table">
                        <tr>
                            <td> 
                                <div class="logo-contract">
                                    <img src="{{ $imgSrc }}" alt="Logo Bali Kami Group">
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
                                <div class="order-date" style="text-align: right">{{ date('D, d F y',strtotime($order_wedding->updated_at)) }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="content">
                    <table class="table tb-head border-bottom m-b-18">
                        <tr>
                            <td>
                                <div class="heading-left">
                                    <div class="business-name">{{ $business->name }}</div>
                                    <div class="business-sub p-b-8">{{ __('messages.'.$business->caption) }}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <table class="table tb-head">
                        <tr>
                            <td style="width: 20%;">
                                發票代碼
                            </td>
                            <td style="width: 30%;">
                                : {{ $invoice->inv_no }}
                            </td>
                            <td style="width: 20%;">
                                發票日期
                            </td>
                            <td style="width: 30%;">
                                : {{ dateFormat($invoice->inv_date) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;">
                                預訂代碼
                            </td>
                            <td style="width: 30%;">
                                : {{ $reservation->rsv_no }}
                            </td>
                            <td style="width: 20%;">
                                付款截止日期
                            </td>
                            <td  style="width: 30%;">
                                : {{ dateFormat($invoice->due_date) }}
                            </td>
                            
                        </tr>
                        <tr>
                            <td style="width: 20%;">
                                訂單代碼
                            </td>
                            <td style="width: 30%;">
                                : {{  $order_wedding->orderno }}
                            </td>
                            <td style="width: 20%;">
                                銷售 / 代理人
                            </td>
                            <td  style="width: 30%;">
                                : {{  $agent->name }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;">
                                新娘
                            </td>
                            <td style="width: 30%;">
                                @if ($bride)
                                    : {{ $bride->groom."".$bride->groom_chinese." & ".$bride->bride." ".$bride->groom_chinese }}
                                @else
                                    : - 
                                @endif
                            </td>
                            <td style="width: 20%;">
                                公司 / 辦公室
                            </td>
                            <td  style="width: 30%;">
                                : {{ $agent->office }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;">
                                聯絡
                            </td>
                            <td style="width: 30%;">
                                @if ($bride->groom_contact)
                                    : {{ $bride->groom_contact }} / {{ $bride->bride_contact }}
                                @else
                                    : - 
                                @endif
                            </td>
                            <td style="width: 20%;">
                                聯絡號碼
                            </td>
                            <td  style="width: 30%;">
                                : {{ $agent->phone }}
                            </td>
                        </tr>
                        <tr>
                            <td>發票識別碼</td>
                            <td colspan="3">: {{ $invoice->admin_code }}</td>
                        </tr>
                        <tr>
                            <td>處理人</td>
                            <td colspan="3">: {{ $admin_reservation->name }}</td>
                            
                        </tr>
                    </table>
                    @php
                        $no_service = 0;
                    @endphp
                    <table class="table-order">
                        <tr>
                            <th style="width: 5%;">數位</th>
                            <th style="width: 30%;">日期 & 時間</th>
                            <th style="width: 45%;">描述</th>
                            <th style="width: 20%; text-align: center;">數量</th>
                        </tr>
                        {{-- WEDDING PACKAGE --}}
                        @if ($order_wedding->service == "Wedding Package")
                            <tr>
                                <td><div class="table-service-name">{{ ++$no_service }}</div></td>
                                <td>
                                    <div class="table-service-name">
                                        {{ date("d M Y",strtotime($order_wedding->checkin)) }} ({{ date('h.i a',strtotime($order_wedding->slot)) }}) - 
                                        {{ date("d M Y",strtotime($order_wedding->checkout)) }} ({{ date('h.i a',strtotime($order_wedding->checkout)) }}) 
                                    </div>
                                </td>
                                <td><div class="table-service-name">婚禮套餐 | {{ $order_wedding->wedding->name }}</div></td>
                                <td style="text-align: right;">
                                    {{ '$ ' . number_format($order_wedding->package_price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                        {{-- SUITES AND VILLAS INVITATION --}}
                        @if ($wedding_accommodations)
                            @foreach ($wedding_accommodations as $inv_room)
                                @php
                                    if ($inv_room->extra_bed_order) {
                                        $extra_bed_price = $inv_room->extra_bed_order->total_price;
                                    }else{
                                        $extra_bed_price = 0;
                                    }
                                @endphp
                                <tr>
                                    <td><div class="table-service-name">{{ ++$no_service }}</div></td>
                                    <td>
                                        <div class="table-service-name">
                                            {{ date("d M Y",strtotime($order_wedding->checkin)) }} ({{ date('h.i a',strtotime($order_wedding->hotel->checkin)) }}) - 
                                            {{ date("d M Y",strtotime($order_wedding->checkout)) }} ({{ date('h.i a',strtotime($order_wedding->hotel->checkout)) }}) 
                                        </div>
                                    </td>
                                    <td><div class="table-service-name">套房或別墅, {{ $inv_room->room->rooms }} {{ $inv_room->extra_bed_order?" + 加床":"" }}, {{ $inv_room->guest_detail }} </div></td>
                                    <td style="text-align: right;">
                                        {{ '$ ' . number_format($inv_room->public_rate + $extra_bed_price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        @if ($order_wedding->service != "Wedding Package")
                            {{-- CEREMONY VENUE --}}
                            @if ($order_wedding->ceremony_venue_id)
                                <tr>
                                    <td><div class="table-service-name">{{ ++$no_service }}</div></td>
                                    <td>
                                        <div class="table-service-name">
                                            {{ date("d M Y",strtotime($order_wedding->wedding_date)) }} ({{ date('h.i a',strtotime($order_wedding->slot)) }})
                                        </div>
                                    </td>
                                    <td><div class="table-service-name">儀式場地, {{ $order_wedding->ceremony_venue->name }}</div></td>
                                    <td style="text-align: right;">
                                        {{ '$ ' . number_format($order_wedding->ceremony_venue_price, 0, ',', '.') }}
                                    </td>
                                </tr>
                                {{-- CEREMONY VENUE DECORATION --}}
                                @if ($order_wedding->ceremony_venue_decoration_id)
                                    <tr>
                                        <td><div class="table-service-name">{{ ++$no_service }}</div></td>
                                        <td>
                                            <div class="table-service-name">
                                                {{ date("d M Y",strtotime($order_wedding->wedding_date)) }} ({{ date('h.i a',strtotime($order_wedding->slot)) }})
                                            </div>
                                        </td>
                                        <td><div class="table-service-name">儀式場地裝飾, {{ $order_wedding->ceremony_venue_decoration->name }}</div></td>
                                        <td style="text-align: right;">
                                            {{ '$ ' . number_format($order_wedding->ceremony_venue_decoration_price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td><div class="table-service-name">{{ ++$no_service }}</div></td>
                                        <td>
                                            <div class="table-service-name">
                                                {{ date("d M Y",strtotime($order_wedding->wedding_date)) }} ({{ date('h.i a',strtotime($order_wedding->slot)) }})
                                            </div>
                                        </td>
                                        <td><div class="table-service-name">儀式場地裝飾, 標準裝飾</div></td>
                                        <td style="text-align: right;">
                                            $ 0
                                        </td>
                                    </tr>
                                @endif
                            @endif
                            {{-- RECEPTION VENUE --}}
                            @if ($order_wedding->reception_venue_id)
                                <tr>
                                    <td><div class="table-service-name">{{ ++$no_service }}</div></td>
                                    <td>
                                        <div class="table-service-name">
                                            {{ dateTimeFormat($order_wedding->reception_date_start) }}
                                        </div>
                                    </td>
                                    <td><div class="table-service-name">接待場地, {{ $order_wedding->reception_venue->name }}</div></td>
                                    <td style="text-align: right;">
                                        {{ '$ ' . number_format($order_wedding->reception_venue_price, 0, ',', '.') }}
                                    </td>
                                </tr>
                                {{-- RECEPTION VENUE DECORATION--}}
                                @if ($order_wedding->reception_venue_decoration_id)
                                    <tr>
                                        <td><div class="table-service-name">{{ ++$no_service }}</div></td>
                                        <td>
                                            <div class="table-service-name">
                                                {{ dateTimeFormat($order_wedding->reception_date_start) }}
                                            </div>
                                        </td>
                                        <td><div class="table-service-name">接待場地裝飾, {{ $order_wedding->reception_venue_decoration->service }}</div></td>
                                        <td style="text-align: right;">
                                            {{ '$ ' . number_format($order_wedding->reception_venue_decoration_price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td><div class="table-service-name">{{ ++$no_service }}</div></td>
                                        <td>
                                            <div class="table-service-name">
                                                {{ dateTimeFormat($order_wedding->reception_date_start) }}
                                            </div>
                                        </td>
                                        <td><div class="table-service-name">接待場地裝飾, 標準裝飾</div></td>
                                        <td style="text-align: right;">
                                            $ 0
                                        </td>
                                    </tr>
                                @endif
                            @endif
                        @endif
                        {{-- TRANSPORT INVITATIONS --}}
                        @if ($additional_transports)
                            @foreach ($additional_transports as $transport_invitation)
                                @php
                                    $transport_inv = $transports->where('id',$transport_invitation->transport_id)->first();
                                    $flight_invs = $flights->where('type',"Arrival")->where('group',"Brids")->first();
                                @endphp
                                @if ($transport_inv)
                                    <tr>
                                        <td><div class="table-service-name">{{ ++$no_service }}</div></td>
                                        <td>
                                            <div class="table-service-name">
                                                {{ dateTimeFormat($transport_invitation->date) }}
                                            </div>
                                        </td>
                                        <td><div class="table-service-name">機場接駁車, {{ $transport_inv->capacity }} 邀請函, {{ $transport_inv->brand }} {{ $transport_inv->name }} - {{ $transport_inv->desc_type="In"?"抵達":"離開" }}</div></td>
                                        <td style="text-align: right;">
                                            {{ '$ ' . number_format($transport_invitation->price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                        {{-- ADDITIONAL SERVICES --}}
                        @if ($order_wedding->service != "Wedding Package")
                            @if ($other_service_ids)
                                @foreach ($other_service_ids as $other_service_id)
                                    @php
                                        $additionalService = $other_services->where('id',$other_service_id)->first();
                                    @endphp
                                    @if ($additionalService)
                                        <tr>
                                            <td><div class="table-service-name">{{ ++$no_service }}</div></td>
                                            <td>
                                                <div class="table-service-name">
                                                    @if ($additionalService->venue == "Ceremony Venue")
                                                        {{ date("d M Y",strtotime($order_wedding->checkin)) }} ({{ date('h.i a',strtotime($order_wedding->slot)) }})
                                                    @elseif($additionalService->venue == "Reception Venue")
                                                        {{ date("d M Y",strtotime($order_wedding->reception_date_start)) }} ({{ date('h.i a',strtotime($order_wedding->reception_date_start)) }})
                                                    @else
                                                        {{ date("d M Y",strtotime($order_wedding->checkin)) }} - {{ date("d M Y",strtotime($order_wedding->checkout)) }}
                                                    @endif
                                                    
                                                </div>
                                            </td>
                                            <td><div class="table-service-name">額外的服務, {{ $additionalService->service }} </div></td>
                                            <td style="text-align: right;">
                                                {{ '$ ' . number_format($additionalService->publish_rate, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        @endif
                        {{-- ADDITIONAL CHARGE --}}
                        @if ($additional_charges)
                            @foreach ($additional_charges as $additionalCharge)
                                <tr>
                                    <td><div class="table-service-name">{{ ++$no_service }}</div></td>
                                    <td>
                                        <div class="table-service-name">
                                            
                                            {{ dateTimeFormat($additionalCharge->date) }}
                                            
                                        </div>
                                    </td>
                                    <td><div class="table-service-name">額外的收費, {{ $additionalCharge->service }} </div></td>
                                    <td style="text-align: right;">
                                        {{ '$ ' . number_format($additionalCharge->price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        @if ($invoice->currency->name != "USD")
                        {{-- TOTAL USD --}}
                        <tr style="background-color: #ffffce">
                            <td colspan="3" class="text-center">全部的 USD</td>
                            <td class="text-right">
                                <div class="tabel-heading">{{ '$ ' . number_format($invoice->total_usd, 0, ',', '.') }}</div>
                                
                            </td>
                        </tr>
                        @endif
                        @if ($invoice->currency->name == "USD")
                            {{-- TOTAL USD --}}
                            <tr style="background-color:  #ffffce">
                                <td colspan="3" class="text-center"><div class="tabel-heading"><b>全部的 USD</b></div></td>
                                <td class="text-right">
                                    <div class="tabel-heading"><b>{{ '$ ' . number_format($invoice->total_usd, 0, ',', '.') }}</b></div>
                                    
                                </td>
                            </tr>
                        @elseif ($invoice->currency->name == "CNY")
                            {{-- TOTAL CNY --}}
                            <tr style="background-color:  #ffffce">
                                <td colspan="3" class="text-center"><div class="tabel-heading"><b>全部的 CNY</b></div></td>
                                <td class="text-right">
                                    <div class="tabel-heading"><b>{{ '¥ ' . number_format($invoice->total_cny, 0, ',', '.') }}</b></div>
                                    
                                </td>
                            </tr>
                        @elseif ($invoice->currency->name == "TWD")
                            {{-- TOTAL TWD --}}
                            <tr style="background-color:  #ffffce">
                                <td colspan="3" class="text-center"><div class="tabel-heading"><b>全部的 TWD</b></div></td>
                                <td class="text-right">
                                    <div class="tabel-heading"><b>{{ '$ ' . number_format($invoice->total_twd, 0, ',', '.') }}</b></div>
                                    
                                </td>
                            </tr>
                        @elseif ($invoice->currency->name == "IDR")
                            {{-- TOTAL IDR --}}
                            <tr style="background-color:  #ffffce">
                                <td colspan="3" class="text-center"><div class="tabel-heading"><b>全部的 IDR</b></div></td>
                                <td class="text-right">
                                    <div class="tabel-heading"><b>{{ 'Rp ' . number_format($invoice->total_idr, 0, ',', '.') }}</b></div>
                                    
                                </td>
                            </tr>
                        @endif
                        {{-- TRANSACTIONS --}}
                        @if ($invoice->currency->name == "USD")
                            @foreach ($transactions_usd as $transaction)
                                <tr>
                                    <td colspan="3" class="text-right"><p>{{ date("Y/m/d",strtotime($transaction->transaction_date)) }} {{ $transaction->type == "Debit"?"RECEIVED":""; }} {{ $transaction->kurs_name }} {{ '$ ' . number_format($transaction->amount, 0, ',', '.') }}</p></td>
                                    <td class="text-right">
                                        <p>{{ '$ ' . number_format($transaction->amount, 0, ',', '.') }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        @elseif ($invoice->currency->name == "CNY")
                            @foreach ($transactions_cny as $transaction)
                                <tr>
                                    <td colspan="3" class="text-right"><p>{{ date("Y/m/d",strtotime($transaction->transaction_date)) }} {{ $transaction->type == "Debit"?"RECEIVED":""; }} {{ $transaction->kurs_name }} {{ '¥ ' . number_format($transaction->amount, 0, ',', '.') }}</p></td>
                                    <td class="text-right">
                                        <p>{{ '¥ ' . number_format($transaction->amount, 0, ',', '.') }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        @elseif ($invoice->currency->name == "TWD")
                            @foreach ($transactions_twd as $transaction)
                                <tr>
                                    <td colspan="3" class="text-right"><p>{{ date("Y/m/d",strtotime($transaction->transaction_date)) }} {{ $transaction->type == "Debit"?"RECEIVED":""; }} {{ $transaction->kurs_name }} {{ '$ ' . number_format($transaction->amount, 0, ',', '.') }}</p></td>
                                    <td class="text-right">
                                        <p>{{ '$ ' . number_format($transaction->amount, 0, ',', '.') }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        @elseif ($invoice->currency->name == "IDR")
                            @foreach ($transactions_idr as $transaction)
                                <tr>
                                    <td colspan="3" class="text-right"><p>{{ date("Y/m/d",strtotime($transaction->transaction_date)) }} {{ $transaction->type == "Debit"?"RECEIVED":""; }} {{ $transaction->kurs_name }} {{ 'Rp ' . number_format($transaction->amount, 0, ',', '.') }}</p></td>
                                    <td class="text-right">
                                        <p>{{ 'Rp ' . number_format($transaction->amount, 0, ',', '.') }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </table>
                    @if ($bank_account)
                        <div class="bank-account">
                            <div class="tb-container">
                                <table class="table tb-list">
                                    <tr>
                                        <td style="width: 15%;">
                                            銀行
                                        </td>
                                        <td style="width: 35%;">
                                            {{ $bank_account->bank }}
                                        </td>
                                        <td style="width:15%;">
                                            SWIFT 代碼
                                        </td>
                                        <td style="width: 35%;">
                                            {{ $bank_account->swift_code }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width:15%;">
                                            姓名
                                        </td>
                                        <td style="width: 35%;">
                                            {{ $bank_account->name }}
                                        </td>
                                        <td style="width:15%;">
                                            電話
                                        </td>
                                        <td style="width: 35%;">
                                            {{ $bank_account->telephone }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width:15%;">
                                            帳戶 IDR
                                        </td>
                                        <td style="width: 35%;">
                                            {{ $bank_account->account_idr }}
                                        </td>
                                        <td style="width: 15%;">
                                            地址
                                        </td>
                                        <td style="width: 35%;">
                                            {{ $bank_account->address }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width:15%;">
                                            帳戶 USD
                                        </td>
                                        <td colspan="2" style="width: 35%;">
                                            {{ $bank_account->account_usd }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <br>
                                        <i>* 轉帳費用和管理費用將由寄件者承擔.</i>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @endif
                    <div class="additional-info">
                        <br>
                        <p>我們謹提醒您在付款過程中遵循以下步驟:<br>
                        1. 收到我們的確認信後，請查看發送的發票中的預訂詳細資訊.<br>
                        2. 如果收到的發票準確，請在發票中提及的「付款日期」之前進行付款.<br>
                        3. 如果付款已完成，請在我們的系統中上傳“付款證明”.<br>
                        感謝您的關注與合作.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </body>
</html>