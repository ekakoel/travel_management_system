<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('agent-registration.mail.receipt_subject') }}</title>
</head>
<body>
    <p>{{ __('agent-registration.mail.receipt_greeting', ['name' => $agent->contact_name ?: $agent->pic_name]) }}</p>
    <p>{{ __('agent-registration.mail.receipt_body') }}</p>
    <p>{{ __('agent-registration.mail.signature') }}</p>
</body>
</html>
