<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation {{ $orderWedding->orderno }}</title>
	<meta charset="utf-8">
</head>
<body>
    <div class="container">
    <div class="card-box">
        <div class="content">
            <h2 style="padding: 0 !important; margin: 0 !important;">{{ 'Invoice:  '.$orderWedding->orderno }}</h2>
            {!! $content !!}
            <br>
            Best Regards,<br>
            Reservation<br><br>
            {{ $admin->name }}<br>
        </div>
    </div>