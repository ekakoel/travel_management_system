<!-- resources/views/unsubscribe.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We're Sorry to See You Go</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 500px;
            text-align: center;
        }
        h1 {
            color: #333333;
            font-size: 24px;
            margin-bottom: 10px;
        }
        p {
            color: #666666;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .reasons {
            text-align: left;
            margin-bottom: 20px;
        }
        .reasons input {
            margin-right: 10px;
        }
        .reasons label {
            color: #333333;
            font-size: 14px;
            display: block;
            margin-bottom: 10px;
        }
        textarea {
            width: 100%;
            height: 80px;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #cccccc;
            font-size: 14px;
            margin-bottom: 20px;
            resize: none;
        }
        button {
            background-color: #ff6b6b;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #ff5252;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #999999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>@lang("messages.We're Sorry to See You Go")</h1>
        <p>@lang("messages.We'd love to know why you're unsubscribing. Your feedback helps us improve our service.")</p>
        
        <form action="{{ route('process_unsubscribe') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ $user->email }}">

            <div class="reasons">
                <label><input type="radio" name="reason" value="Too many emails"> @lang("messages.I'm receiving too many emails")</label>
                <label><input type="radio" name="reason" value="Not relevant"> @lang('messages.The content is not relevant to me')</label>
                <label><input type="radio" name="reason" value="Prefer another service"> @lang('messages.I prefer another service')</label>
                <label><input type="radio" name="reason" value="Technical issues"> @lang('messages.I had technical issues')</label>
                <label><input type="radio" name="reason" value="Other"> @lang('messages.Other') (@lang('messages.please specify below'))</label>
            </div>

            <textarea name="reason_other" id="reason_other" placeholder="@lang("messages.If you chose 'Other', please tell us the reason")..."></textarea>

            <button type="submit">@lang('messages.Unsubscribe')</button>
        </form>

        <div class="footer">
            <p>@lang('messages.If you change your mind, you can always') <a href="{{ route('subscribe', ['email' => $user->email]) }}">@lang('messages.subscribe again')</a>!</p>
        </div>
    </div>
</body>
</html>
