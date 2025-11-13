<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Back!</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 400px;
            text-align: center;
        }
        h1 {
            font-size: 26px;
            margin-bottom: 20px;
            color: #4CAF50;
        }
        p {
            font-size: 16px;
            margin-bottom: 30px;
            color: #555;
        }
        .cta-button {
            background-color: #4CAF50;
            color: #fff;
            padding: 12px 24px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .cta-button:hover {
            background-color: #45a049;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
        }
        .footer a {
            color: #4CAF50;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>@lang('messages.Welcome Back!')</h1>
        <p>@lang("messages.We're thrilled to have you back with us. Click the button below to subscribe again and never miss an update!")</p>
        
        <form action="{{ route('process_subscribe') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ $user->email }}">
            <button type="submit" class="cta-button">@lang('messages.Subscribe Again')</button>
        </form>

        <div class="footer">
            <p>@lang('messages.If you change your mind, you can always') <a href="{{ route('unsubscribe', ['email' => $user->email]) }}">@lang('messages.unsubscribe again')</a>.</p>
        </div>
    </div>
</body>
</html>