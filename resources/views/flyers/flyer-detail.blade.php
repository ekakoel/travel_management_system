<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $hotel->name }} Promotions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            width: 60%;
            margin: auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            position: relative;
            text-align: center;
            color: white;
        }
        .header img {
            width: 100%;
            height: auto;
            aspect-ratio: 9/6;
            object-fit: cover;
        }
        table{
            padding: 0 !important;
            margin: 0 !important;
        }
        table thead{
            /* font-weight: 600; */
            font-size: 0.8rem;
        }
        table thead tr th {
            background-color: #d5d5d5 !important;
        }
        table tbody tr td {
            font-size: 0.8rem;
        }
        .discount {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #0000008a;
            color: white;
            padding: 15px;
            border-radius: 5px;
            font-weight: bold;
            width: 27%;
        }
        .date-print {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #0000008a;
            color: white;
            padding: 4px 15px;
            border-radius: 5px;
            font-weight: bold;
        }
        .title {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            margin: 18px 0 0 0;
        }
        .room-title{
            font-weight: 600;
        }
        .stars {
            text-align: center;
            color: gold;
        }
        .about {
            text-align: center;
            margin: 0 0 20px 0;
        }
        .about i{
            font-size: 0.8rem;
            text-decoration-line: underline;
        }
        .promo-rooms {
            display: flex;
            flex-direction: column;
            gap: 18px;
            margin: 18px 0;
        }
        .promo-benefits{
            width: 100%;
            background-color: white;
            padding: 8px 18px;
            font-size: 0.8rem;
            margin-bottom: 18px;
            border-bottom: 1px solid #c3c3c3;
        }
        .room-container{
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            background: #f9f9f900;
            padding: 15px;
        }
        .room-item {
            padding-bottom: 15px;
            display: flex;
            border-radius: 10px;
        }
        .room-image {
            width: 26%;
            aspect-ratio: 9/6;
            overflow: hidden;
            margin-right: 15px;
        }
        .room-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .room-details {
            flex: 1;
        }
        .room-details span{
            font-size: 0.7rem;
            color: gray;
        }
        .room-details p {
            margin: 5px 0;
            color: #555;
        }
        .footer {
            display: block;
            text-align: center;
            background: #e3e3e3;
            color: #616161;
            padding: 10px;
            margin-top: 20px;
            justify-content: space-between;
        }
        .footer img{
            width: 250px;
        }
        
        .footer .btn-action{
            display: inline-flex;
            gap: 8px;
            margin: 8px 0;
        }
        .footer .btn-action a{
            height: fit-content;
            padding: 4px 18px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            margin: 0;
        }
        .footer .btn-action a i{
            padding: 0 8px 0 0;
        }
       
        .footer p{
            margin: 0;
        }
        
        .action{
            margin: 18px 0;
            display: flex;
            gap: 8px;
            place-items: anchor-center;
            width: 100%;
            justify-content: center;
        }
        .action .button{
            text-decoration: none;
            width: fit-content;
            display: block;
            padding: 7px 23px;
            color: #373737;
            font-weight: bold;
            border: none;
            cursor: pointer;
            border-radius: 39px;
            border: 2px solid #898989;
        }
        
        .note{
            padding: 8px 18px;
            border: 1px solid #c5c5c5;
            border-radius: 8px;
        }
        .note ul{
            margin: 0;
        }
        .note ul li{
            font-size: 0.8rem;
            font-style: italic;
            margin: 5px 0;
            line-height: 1.5;
            color: #555;
        }
        .bg-white{
            background: #ffffff;
            color: #373737;
        }
        .bg-red{
            background: #ff3535;
            color: white !important;
        }
        .text-left{
            text-align: left;
        }
        .about i{
            text-decoration: none
        }
        @media (max-width: 576px) {
            html, body{
                width: 100%;
                margin: 0;
                width: 100%;
                justify-self: center;
            }
            .container {
                width: 100%;
                margin: 0;
            }
            .room-item {
                display: grid;
                justify-items: anchor-center;
            }
            .room-image {
                height: 125px;
            }
            .footer{
                display: none;
            }
            .room-details {
               justify-items: center;
            }
        }
        @media (max-width: 768px) {
            html, body{
                width: 100%;
                margin: 0;
                width: 100%;
                justify-self: center;
            }
            .container {
                width: 100%;
                margin: 0;
            }
            .room-item {
                display: grid;
                justify-items: anchor-center;
            }
            .room-image {
                width: 100%;
            }
            .footer{
                display: none;
            }
            .room-details {
               justify-items: center;
            }
        }
        @media (max-width: 992px) {
            html, body{
                width: 100%;
                margin: 0;
                width: 100%;
                justify-self: center;
            }
            .container {
                width: 100%;
                margin: 0;
            }
            .room-item {
                display: grid;
                justify-items: anchor-center;
            }
            .room-image {
                width: 100%;
            }
            .footer{
                display: none;
            }
        }
        @media print {
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body, html {
                width: 100%;
                height: 100%;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: #ffffff; /* Pastikan warna tetap */
                margin: 0;
                padding: 0;
            }
            .header img {
                aspect-ratio: 15/6;
            }
            .room-image {
                width: 26%;
                aspect-ratio: 9 / 6;
                overflow: hidden;
                margin-right: 15px;
            }
            .room-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .container {
                width: 100vw;
                max-width: 100%;
                padding: 0;
                margin: 0;
            }
            .table {
                width: 100%;
            }
            
            .room-item {
                display: flex;
                page-break-inside: avoid;
                padding-bottom: 15px;
                border-radius: 0;
                box-shadow: none;
            }
            .room-details {
               justify-items: left;
            }
            .promo-benefits{
                page-break-after: auto;
            }
            .note{
                page-break-inside: avoid;
            }
            button {
                display: none;
            }
            .footer {
                page-break-inside: avoid;
                display: block;
                background-color: #ffffff !important;
                color: rgb(82, 82, 82) !important;
                box-shadow: none;
            }
           
            .action{
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="date-print">{{ date('F d, Y', strtotime($now)) }}</div>
            <img src="{{ asset('storage/hotels/hotels-cover/'.$hotel->cover) }}"  class="img-fluid rounded thumbnail-image" loading="lazy">
            <div class="discount">@lang('messages.Get exciting discounts during the promo period!')</div>
        </div>
        <div class="title">{{ $hotel->name }}</div>
        <div class="stars">★★★★★</div>
        <div class="about">
            <i>{{ $hotel->address }}</i><br>
            @if (app()->getLocale() == 'zh')
                {!! $hotel->description_traditional !!}
            @elseif (app()->getLocale() == 'zh-CN')
                {!! $hotel->description_simplified !!}
            @else
                {!! $hotel->description !!}
            @endif
        </div>
        @if ($rooms)
            <div class="promo-rooms">
                @foreach ($rooms as $room)
                    <div class="room-container">
                        <div class="room-item">
                            <div class="room-image">
                                <img src="{{ asset('storage/hotels/hotels-room/'.$room->cover) }}"  class="img-fluid rounded thumbnail-image" loading="lazy">
                            </div>
                            <div class="room-details">
                                <div class="room-title">{{ $room->rooms }}</div>
                                <span>
                                    @if ($room->capacity == 2)
                                        @php
                                            $adults = 2;
                                            $children = 0;
                                        @endphp
                                    @elseif ($room->capacity == 3)
                                        @php
                                            $adults = 2;
                                            $children = 1;
                                        @endphp
                                    @elseif ($room->capacity % 2 == 0) 
                                        @php
                                            $adults = $room->capacity;
                                            $children = $room->capacity / 2;
                                        @endphp
                                    @else
                                        @php
                                            $adults = $room->capacity - 1;
                                            $children = ($room->capacity - 1) / 2;
                                        @endphp
                                    @endif
                                    @for ($i = 0; $i < $adults; $i++)
                                        <i class="fa-solid fa-user"></i>
                                    @endfor
                                    @for ($i = 0; $i < $children; $i++)
                                        <i class="fa-solid fa-child"></i>
                                    @endfor
                                    ({{ $adults }} @lang('messages.adults'){{ $children > 0 ? " + ".$children." ".__('messages.child'):""; }})
                                </span>
                            </div>
                        </div>
                        <div class="promotion-card">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>@lang('messages.Promo')</th>
                                        <th>@lang('messages.Booking Period')</th>
                                        <th>@lang('messages.Stay Period')</th>
                                        <th>@lang('messages.Benefits')</th>
                                        <th>@lang('messages.Price/night')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($promos as $promo)
                                        @if ($promo->rooms_id == $room->id)
                                            <tr>
                                                <td>
                                                    <b>{{ $promo->name }}</b><br>
                                                    @lang('messages.Minimum Stay') {{ $promo->minimum_stay }} @lang('messages.nights')
                                                </td>
                                                <td>{{ date('F d, Y', strtotime($promo->book_periode_start)) }} - <br>{{ date('F d, Y', strtotime($promo->book_periode_end)) }}</td>
                                                <td>{{ date('F d, Y', strtotime($promo->periode_start)) }} - <br>{{ date('F d, Y', strtotime($promo->periode_end)) }}</td>
                                                @if (app()->getLocale() == 'zh')
                                                    @if ($promo->benefits)
                                                        <td>{!! $promo->benefits_traditional !!}</td>
                                                    @else
                                                        <td>-</td>
                                                    @endif
                                                @elseif (app()->getLocale() == 'zh-CN')
                                                    @if ($promo->benefits)
                                                        <td>{!! $promo->benefits_simplified !!}</td>
                                                    @else
                                                        <td>-</td>
                                                    @endif
                                                @else
                                                    @if ($promo->benefits)
                                                        <td>{!! $promo->benefits !!}</td>
                                                    @else
                                                        <td>-</td>
                                                    @endif
                                                @endif
                                                
                                                <td>{{ "$ " . number_format($promo->calculatePrice($usdrates, $tax)) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="note">
            <ul>
                <li>@lang('messages.Prices are subject to change based on daily exchange rates.')</li>
                <li>@lang('messages.Prices listed are exclusively valid for bookings made through our official website at online.balikamitour.com. Any reservations made through third party platforms, agents, or other channels may be subject to different rates and conditions. Bali Kami Tour reserves the right to deny price matching or adjustments for bookings not completed via our official site.')</li>
                <li>@lang('messages.The prices listed are not applicable for bookings made through our offline reservation agents, unless the bookings paid at the time of confirmations.')</li>
                <li>@lang('messages.Terms and conditions apply.')</li>
            </ul>
        </div>
        <div class="footer">
            <img src="{{ asset('storage/logo/logo-color-bali-kami.png') }}" alt="logo Bali Kami Tour">
            <p>
                {{ $business->address }}<br>
                {{ $business->phone }}<br>
                {{ $business->website }}<br>
                <a href="https://www.balikamitour.com">www.balikamitour.com</a> | 
                <a href="https://online.balikamitour.com">online.balikamitour.com</a><br>
            </p>
            <p>@lang('messages.You can book this promotional service through the available options below!')</p>
            <div class="btn-action">
                <!-- Facebook Booking -->
                <a href="https://online.balikamitour.com/hotel-{{ $hotel->code }}" target="_blank"
                    class="btn d-flex align-items-center"
                    style="background: #1877F2; color: white;">
                    <i class="fa fa-globe" aria-hidden="true"></i>@lang('messages.Book Here')
                </a>
                <!-- Email Booking -->
                <a href="mailto:reservation@balikamitour.com?subject=Room%20Booking%20Inquiry"
                    class="btn d-flex align-items-center"
                    style="background: #D44638; color: white;">
                    <i class="fas fa-envelope"></i>@lang('messages.Book Here')
                </a>
            </div>
        </div>
        <div class="action">
            <button class="button" onclick="window.print()"><i class="fa fa-print" aria-hidden="true"></i> @lang('messages.Print')</button>
            <a href="{{ route('index.flyers') }}" class="button"> <i class="fa fa-times" aria-hidden="true"></i> @lang('messages.Close')</a>
        </div>
    </div>
</body>

</html>
