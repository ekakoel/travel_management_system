<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('agent-registration.mail.admin_subject') }}</title>
</head>
<body>
    <p>{{ __('agent-registration.mail.admin_body', ['company' => $agent->company_name]) }}</p>
    <p>{{ __('agent-registration.fields.contact_name') }}: {{ $agent->contact_name ?: $agent->pic_name }}</p>
    <p>{{ __('agent-registration.fields.contact_email') }}: {{ $agent->contact_email ?: $agent->email }}</p>
    <p>{{ __('agent-registration.fields.phone') }}: {{ $agent->phone }}</p>
    <p><a href="{{ route('admin.agents.show', $agent) }}">{{ __('agent-registration.mail.review_application') }}</a></p>
    <p>{{ __('agent-registration.mail.signature') }}</p>
</body>
</html>
