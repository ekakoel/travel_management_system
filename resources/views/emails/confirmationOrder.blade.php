<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>Order Confirmation {{ $confirmation['order']['reference'] }}</title>
    <style>
        @media only screen and (max-width: 640px) {
            .email-shell { width: 100% !important; }
            .email-padding { padding-left: 20px !important; padding-right: 20px !important; }
            .stack-cell { display: block !important; width: 100% !important; box-sizing: border-box !important; }
            .stack-cell + .stack-cell { padding-top: 12px !important; }
            .mobile-align-left { text-align: left !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background-color:#f3f6fa; color:#172033; font-family:Arial, Helvetica, sans-serif; -webkit-text-size-adjust:100%;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent;">
        {{ $confirmation['preheader'] }}
    </div>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; background-color:#f3f6fa; border-collapse:collapse;">
        <tr>
            <td align="center" style="padding:28px 12px;">
                <table role="presentation" width="640" cellspacing="0" cellpadding="0" border="0" class="email-shell" style="width:640px; max-width:640px; background-color:#ffffff; border-collapse:collapse; border:1px solid #e2e8f0;">
                    <tr>
                        <td class="email-padding" style="padding:24px 32px; background-color:#0f3d75;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;">
                                <tr>
                                    <td class="stack-cell" style="vertical-align:middle;">
                                        <div style="font-size:20px; line-height:26px; font-weight:700; color:#ffffff; letter-spacing:.2px;">
                                            {{ $confirmation['brand']['name'] }}
                                        </div>
                                        <div style="margin-top:3px; font-size:12px; line-height:18px; color:#cbdcf1;">
                                            {{ $confirmation['brand']['tagline'] }}
                                        </div>
                                    </td>
                                    <td class="stack-cell mobile-align-left" align="right" style="vertical-align:middle; text-align:right;">
                                        <span style="display:inline-block; padding:7px 12px; border-radius:999px; background-color:#dff7ec; color:#087857; font-size:12px; line-height:16px; font-weight:700; text-transform:uppercase; letter-spacing:.6px;">
                                            {{ $confirmation['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="email-padding" style="padding:32px 32px 22px;">
                            <div style="font-size:12px; line-height:18px; color:#0f6f61; font-weight:700; text-transform:uppercase; letter-spacing:1px;">
                                Order confirmation
                            </div>
                            <h1 style="margin:8px 0 0; font-size:27px; line-height:35px; color:#172033; font-weight:700;">
                                Your order is confirmed
                            </h1>
                            <p style="margin:16px 0 0; font-size:15px; line-height:24px; color:#4b5870;">
                                Dear {{ $confirmation['recipient_name'] }},<br>
                                Thank you for booking with {{ $confirmation['brand']['name'] }}. Your reservation has been confirmed by our team. Please review the summary below and keep this email for your records.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td class="email-padding" style="padding:0 32px 24px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; border-collapse:collapse; background-color:#eef5fc; border:1px solid #d8e7f7;">
                                <tr>
                                    <td class="stack-cell" style="width:50%; padding:16px; vertical-align:top;">
                                        <div style="font-size:11px; line-height:16px; color:#64748b; font-weight:700; text-transform:uppercase; letter-spacing:.6px;">Order reference</div>
                                        <div style="margin-top:4px; font-size:17px; line-height:23px; color:#0f3d75; font-weight:700;">{{ $confirmation['order']['reference'] }}</div>
                                    </td>
                                    <td class="stack-cell" style="width:50%; padding:16px; vertical-align:top; border-left:1px solid #d8e7f7;">
                                        <div style="font-size:11px; line-height:16px; color:#64748b; font-weight:700; text-transform:uppercase; letter-spacing:.6px;">Reservation reference</div>
                                        <div style="margin-top:4px; font-size:17px; line-height:23px; color:#0f3d75; font-weight:700;">{{ $confirmation['order']['reservation_reference'] }}</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="email-padding" style="padding:0 32px 24px;">
                            <h2 style="margin:0 0 12px; font-size:16px; line-height:22px; color:#172033;">Order summary</h2>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; border-collapse:collapse; border:1px solid #e2e8f0;">
                                @foreach([
                                    'Service' => $confirmation['order']['service'],
                                    'Product' => $confirmation['order']['product'],
                                    'Travel date' => $confirmation['order']['travel_start'] === $confirmation['order']['travel_end']
                                        ? $confirmation['order']['travel_start']
                                        : $confirmation['order']['travel_start'].' – '.$confirmation['order']['travel_end'],
                                    'Guests' => $confirmation['order']['guests'],
                                    'Pickup location' => $confirmation['order']['pickup'],
                                    'Drop-off location' => $confirmation['order']['dropoff'],
                                ] as $label => $value)
                                    <tr>
                                        <td style="width:36%; padding:10px 14px; border-bottom:1px solid #e8edf3; color:#64748b; font-size:13px; line-height:19px; vertical-align:top;">{{ $label }}</td>
                                        <td style="padding:10px 14px; border-bottom:1px solid #e8edf3; color:#172033; font-size:13px; line-height:19px; font-weight:600; vertical-align:top;">{{ $value }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="email-padding" style="padding:0 32px 24px;">
                            <h2 style="margin:0 0 12px; font-size:16px; line-height:22px; color:#172033;">Invoice &amp; payment</h2>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; border-collapse:collapse; background-color:#f8fafc; border:1px solid #e2e8f0;">
                                <tr>
                                    <td class="stack-cell" style="width:33.33%; padding:15px; vertical-align:top;">
                                        <div style="font-size:11px; line-height:16px; color:#64748b; font-weight:700; text-transform:uppercase;">Invoice</div>
                                        <div style="margin-top:4px; font-size:14px; line-height:20px; color:#172033; font-weight:700;">{{ $confirmation['billing']['invoice_number'] }}</div>
                                    </td>
                                    <td class="stack-cell" style="width:33.33%; padding:15px; vertical-align:top;">
                                        <div style="font-size:11px; line-height:16px; color:#64748b; font-weight:700; text-transform:uppercase;">Total price (USD)</div>
                                        <div style="margin-top:4px; font-size:14px; line-height:20px; color:#172033; font-weight:700;">{{ $confirmation['billing']['total_usd'] }}</div>
                                    </td>
                                    <td class="stack-cell" style="width:33.33%; padding:15px; vertical-align:top;">
                                        <div style="font-size:11px; line-height:16px; color:#64748b; font-weight:700; text-transform:uppercase;">Amount due ({{ $confirmation['billing']['currency'] }})</div>
                                        <div style="margin-top:4px; font-size:14px; line-height:20px; color:#087857; font-weight:700;">{{ $confirmation['billing']['amount_due'] }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="padding:0 15px 15px; color:#64748b; font-size:12px; line-height:18px;">
                                        Payment due date: <strong style="color:#334155;">{{ $confirmation['billing']['due_date'] }}</strong>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:10px 0 0; color:#64748b; font-size:12px; line-height:19px;">{{ $confirmation['attachments'] }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td class="email-padding" align="center" style="padding:2px 32px 30px; text-align:center;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:0 auto; border-collapse:collapse;">
                                <tr>
                                    <td align="center" style="background-color:#0f6f61; border-radius:5px;">
                                        <a href="{{ $confirmation['action_url'] }}" style="display:inline-block; padding:13px 24px; color:#ffffff; font-size:14px; line-height:20px; font-weight:700; text-decoration:none;">View order details</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:14px 0 0; color:#64748b; font-size:11px; line-height:17px;">
                                If the button does not work, copy and paste this secure link into your browser:<br>
                                <a href="{{ $confirmation['action_url'] }}" style="color:#0f3d75; text-decoration:underline; word-break:break-all;">{{ $confirmation['action_url'] }}</a>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td class="email-padding" style="padding:20px 32px; background-color:#f8fafc; border-top:1px solid #e2e8f0;">
                            <p style="margin:0; color:#475569; font-size:12px; line-height:19px;">
                                Confirmed on {{ $confirmation['confirmed_at'] }} by {{ $confirmation['confirmed_by'] }}.<br>
                                Need assistance? Contact
                                <a href="mailto:{{ $confirmation['brand']['email'] }}" style="color:#0f3d75; text-decoration:underline;">{{ $confirmation['brand']['email'] }}</a>
                                @if($confirmation['brand']['phone'])
                                    or {{ $confirmation['brand']['phone'] }}
                                @endif
                                .
                            </p>
                            <p style="margin:10px 0 0; color:#94a3b8; font-size:10px; line-height:16px;">
                                This is a transactional email concerning order {{ $confirmation['order']['reference'] }}. Please do not share its links or attachments publicly.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
