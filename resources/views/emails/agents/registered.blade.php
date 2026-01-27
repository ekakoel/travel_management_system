
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Agent Registration</title>
    <style>
        .text{
            font-size: 0.8rem;
        }
    </style>
</head>
    <body>
        <div class="container">
            <h1>A new travel agent has registered on Bali Kami Tour.</h1>
            <p>Company Name:{{ $agent->company_name }}</p>
            <p>Contact Person: {{ $agent->pic_name }}</p>
            <p>Email: {{ $agent->email }}</p>
            <p>Phone: {{ $agent->phone }}</p>
            <p>Country: {{ $agent->country }}</p>
            <p>Website: {{ $agent->website ?? '-' }}</p>
            <div style="height: 18px !important"></div>
            <hr>
            <p>Please review their submitted documents for approval.</p>
            <a href="{{ route('admin.agents.show',$agent->id) }}"> Verify Agent {{ $agent->company_name }}</a>
            <div style="height: 40px !important"></div>
            <p>
                <b>Thanks,</b><br>
                Bali Kami Tour System
            </p>
    </body>
</html>
