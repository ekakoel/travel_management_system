<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
</head>
<body style="margin:0;background:#f3f6fa;color:#172033;font-family:Arial,Helvetica,sans-serif;">
@php
    $currencyCode = optional($invoice->currency)->name ?: 'USD';
    $amount = (float) optional($paymentConfirmation)->amount;
    $amountLabel = $currencyCode . ' ' . number_format($amount, in_array($currencyCode, ['USD'], true) ? 2 : 0, '.', ',');
@endphp
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f6fa;padding:32px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;background:#ffffff;border:1px solid #dce5ef;border-radius:16px;overflow:hidden;">
                <tr>
                    <td style="padding:26px 30px;background:#0f5fa8;color:#ffffff;">
                        <div style="font-size:12px;font-weight:700;letter-spacing:1.4px;text-transform:uppercase;">Bali Kami Tour</div>
                        <h1 style="margin:8px 0 0;font-size:25px;line-height:1.3;">Payment confirmation received</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:28px 30px;">
                        <p style="margin:0 0 18px;font-size:15px;line-height:1.65;">
                            {{ $agent?->name ?: 'An agent' }} submitted payment proof for verification. This submission is <strong>not a final payment approval</strong>; the finance team must match it to the invoice and bank transaction.
                        </p>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;border:1px solid #dce5ef;border-radius:10px;overflow:hidden;">
                            <tr><td style="padding:11px 14px;background:#f7f9fc;color:#5d6b82;width:42%;">Order number</td><td style="padding:11px 14px;font-weight:700;">{{ $order->orderno }}</td></tr>
                            <tr><td style="padding:11px 14px;background:#f7f9fc;color:#5d6b82;">Reservation number</td><td style="padding:11px 14px;font-weight:700;">{{ optional($order->reservations)->rsv_no ?: '-' }}</td></tr>
                            <tr><td style="padding:11px 14px;background:#f7f9fc;color:#5d6b82;">Invoice number</td><td style="padding:11px 14px;font-weight:700;">{{ $invoice->inv_no }}</td></tr>
                            <tr><td style="padding:11px 14px;background:#f7f9fc;color:#5d6b82;">Reported payment date</td><td style="padding:11px 14px;font-weight:700;">{{ optional($paymentConfirmation)->payment_date ? dateFormat($paymentConfirmation->payment_date) : '-' }}</td></tr>
                            <tr><td style="padding:11px 14px;background:#f7f9fc;color:#5d6b82;">Reported amount</td><td style="padding:11px 14px;font-weight:700;">{{ $amountLabel }}</td></tr>
                            <tr><td style="padding:11px 14px;background:#f7f9fc;color:#5d6b82;">Review status</td><td style="padding:11px 14px;font-weight:700;color:#9a6700;">Pending verification</td></tr>
                        </table>

                        <p style="margin:22px 0 8px;font-size:14px;line-height:1.6;color:#5d6b82;">
                            Verify the beneficiary account, amount, currency, value date, and transaction identity before marking this payment as valid.
                        </p>
                        <a href="{{ $order_link }}" style="display:inline-block;margin-top:8px;padding:13px 22px;border-radius:999px;background:#0f5fa8;color:#ffffff;font-size:14px;font-weight:700;text-decoration:none;">Review order and payment</a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:18px 30px;border-top:1px solid #e5ebf2;color:#748197;font-size:12px;line-height:1.55;">
                        Automated operational notification from {{ config('app.name') }}. Please do not reply to this message.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
