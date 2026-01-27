<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exclusive Hotel Promo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: #ffffff;
            margin: 20px auto;
            padding: 20px;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container p {
            font-size: 0.8rem;
            color: grey;
            padding: 0 0 8px 0 !important;
            margin: 0 !important;
        }

        .header {
            background-color: #007BFF;
            padding: 10px 20px;
            color: #ffffff;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }

        .content {
            border-radius: 0 0 8px 8px;
            background-color: #ffffff;
            border: 1px solid #dbdbdb;
            padding: 20px;
            color: #333333;
        }

        .content h1 {
            font-size: 24px;
            color: #333333;
        }

        .content p {
            font-size: 16px;
            line-height: 1.5;
        }

        .content .promo-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        table{
            border-radius: 8px;
        }
        .promo-table thead tr, .promo-table thead th {
            border-radius: 8px 8px 0 0;
            border: none;
        }
        .promo-table th,
        .promo-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #dddddd;
            align-content: flex-start;
        }

        .promo-table th {
            background-color: #e1e1e1;
            color: #4f4f4f;
            text-transform: uppercase;
            text-align: center;
            font-size: 1.2rem;
        }

        .promo-table td {
            background-color: #f4f4f4;
        }

        .cta-button {
            display: inline-block;
            padding: 15px 25px;
            background-color: #007BFF;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            font-size: 18px;
            margin: 20px 0;
        }

        .unsubscribe {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777777;
        }

        .unsubscribe a {
            color: #007BFF;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <p>下方的优惠信息为简体中文</p>
        <div class="header">
            <h1>{!! $title_mandarin !!}</h1>
        </div>
        <div class="content">
            <h1>你好, {{ $user->name }}!</h1>
            <p>{!! $suggestion_mandarin !!}</p>
            <table class="promo-table">
                <thead>
                    <tr>
                        <th colspan="2">{{ $hotel->name }}</th>
                    </tr>
                </thead>
                <tr>
                    <td>优惠</td>
                    <td>{{ $promo->name }}</td>
                </tr>
                <tr>
                    <td>房间</td>
                    <td>{{ $promo->rooms->rooms }}</td>
                </tr>
                <tr>
                    <td>最少入住天数</td>
                    <td>{{ $promo->minimum_stay }} 晚</td>
                </tr>
                <tr>
                    <td>预订期间</td>
                    <td>{{ date('d M Y', strtotime($promo->book_periode_start)) }} -
                        {{ date('d M Y', strtotime($promo->book_periode_end)) }}</td>
                </tr>
                <tr>
                    <td>入住期间</td>
                    <td>{{ date('d M Y', strtotime($promo->periode_start)) }} -
                        {{ date('d M Y', strtotime($promo->periode_end)) }}</td>
                </tr>
                <tr>
                    <td>优惠内容</td>
                    <td>{!! $promo->benefits_simplified !!}</td>
                </tr>
            </table>
            <p>准备好预订您的住宿了吗？点击下方按钮，享受这些特别优惠吧！</p>
            <a href="{{ $link }}" class="cta-button">立即预订</a>
        </div>
    </div>
    <div class="container">
        <p>The promotion below is presented in English</p>
        <div class="header">
            <h1>{!! $title !!}</h1>
        </div>
        <div class="content">
            <h1>Hello, {{ $user->name }}!</h1>
            <p>{!! $suggestion !!}</p>
            <table class="promo-table">
                <thead>
                    <tr>
                        <th colspan="2">{{ $hotel->name }}</th>
                    </tr>
                </thead>
                <tr>
                    <td>Promo</td>
                    <td>{{ $promo->name }}</td>
                </tr>
                <tr>
                    <td>Room</td>
                    <td>{{ $promo->rooms->rooms }}</td>
                </tr>
                <tr>
                    <td>Minimum Stay</td>
                    <td>{{ $promo->minimum_stay }} Nights</td>
                </tr>
                <tr>
                    <td>Booking Period</td>
                    <td>{{ date('d M Y', strtotime($promo->book_periode_start)) }} -
                        {{ date('d M Y', strtotime($promo->book_periode_end)) }}</td>
                </tr>
                <tr>
                    <td>Stay Period</td>
                    <td>{{ date('d M Y', strtotime($promo->periode_start)) }} -
                        {{ date('d M Y', strtotime($promo->periode_end)) }}</td>
                </tr>
                <tr>
                    <td>Benefits</td>
                    <td>{!! $promo->benefits !!}</td>
                </tr>
            </table>
            <p>Ready to book your stay? Click the button below to take advantage of these special offers:</p>
            <a href="{{ $link }}" class="cta-button">Book Now</a>
        </div>
    </div>
    <div class="unsubscribe">
        <p>如果您不想再收到这些邮件，您可以随时<a href="{{ route('unsubscribe', ['email' => $user->email]) }}">取消订阅。</a><br>
            If you no longer wish to receive these emails, you can <a href="{{ route('unsubscribe', ['email' => $user->email]) }}">unsubscribe</a> at any time.</p>
        <p>感谢您选择 Bali Kami Tour & Wedding，我们期待不久后欢迎您的到来！<br>
            Thank you for choosing Bali Kami Tour & Wedding. We look forward to welcoming you soon!</p>
    </div>
</body>

</html>
