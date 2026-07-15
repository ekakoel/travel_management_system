@php
    use Carbon\Carbon;

    $businessName = $business->name ?? config('app.name', 'Bali Kami Tour');
    $businessCaption = isset($business) && isset($business->caption)
        ? __('messages.' . $business->caption)
        : 'Travel Services';
    $issueDate = $invoice?->inv_date ? Carbon::parse($invoice->inv_date) : null;
    $dueDate = $invoice?->due_date ? Carbon::parse($invoice->due_date) : null;
    $travelStart = $order->checkin ? Carbon::parse($order->checkin) : null;
    $travelEnd = $order->checkout ? Carbon::parse($order->checkout) : null;
    $currencyCode = optional($invoice?->currency)->name ?: 'USD';
    $amountDue = match ($currencyCode) {
        'CNY' => 'CNY ' . number_format((float) $invoice?->total_cny, 0),
        'TWD' => 'NT$ ' . number_format((float) $invoice?->total_twd, 0),
        'IDR' => 'Rp ' . number_format((float) $invoice?->total_idr, 0),
        default => currencyFormatUsd($invoice?->total_usd ?: $order->final_price),
    };
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoice->inv_no ?? $order->orderno }}</title>
    <style>
        @page {
            margin: 14px 22px;
        }
        body {
            margin: 0;
            color: #1f2937;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9.2px;
            line-height: 1.3;
            background: #ffffff;
        }
        .page {
            padding: 0 2px 6px;
        }
        .hero {
            padding: 0 0 10px;
            border-bottom: 1px solid #dbe7f3;
            background: transparent;
        }
        .table,
        .meta-table,
        .payment-inline {
            width: 100%;
            border-collapse: collapse;
        }
        .eyebrow {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 999px;
            background: #e8f1fb;
            color: #0f3d75;
            font-size: 7.8px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .invoice-title {
            margin: 6px 0 2px;
            font-size: 17px;
            font-weight: 800;
            color: #111827;
        }
        .brand {
            font-size: 14px;
            font-weight: 700;
            color: #0f3d75;
        }
        .muted {
            color: #6b7280;
        }
        .amount-box {
            padding: 0;
            text-align: right;
        }
        .amount-box span {
            display: block;
            color: #6b7280;
            font-size: 7.8px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .amount-box strong {
            display: block;
            margin-top: 4px;
            font-size: 18px;
            line-height: 1.1;
            color: #0f3d75;
        }
        .grid {
            margin-top: 8px;
        }
        .panel {
            padding: 0 0 8px;
            page-break-inside: avoid;
        }
        .panel-title {
            margin: 0 0 6px;
            color: #0f3d75;
            font-size: 8.2px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding-bottom: 4px;
            border-bottom: 1px solid #e5edf7;
        }
        .meta-table td {
            padding: 1px 0;
            vertical-align: top;
        }
        .label {
            width: 34%;
            color: #6b7280;
        }
        .value {
            color: #111827;
            font-weight: 600;
        }
        .summary-row td {
            padding-top: 8px;
            vertical-align: top;
        }
        .summary-row .table td {
            vertical-align: top;
        }
        .summary-block {
            box-sizing: border-box;
            display: block;
            min-height: 54px;
            padding: 0 6px 0 0;
            page-break-inside: avoid;
        }
        .summary-key {
            display: block;
            color: #6b7280;
            font-size: 7.8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .summary-value {
            display: block;
            margin-top: 3px;
            color: #111827;
            font-size: 9.6px;
            font-weight: 700;
            line-height: 1.28;
        }
        .payment-inline td {
            width: 25%;
            padding: 2px 10px 0 0;
            vertical-align: top;
        }
        .totals {
            margin-top: 8px;
            padding: 8px 0 0;
            border-top: 1px solid #dbe7f3;
            page-break-inside: avoid;
        }
        .totals-label {
            color: #6b7280;
            font-size: 7.8px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .totals-value {
            margin-top: 3px;
            color: #0f3d75;
            font-size: 15px;
            font-weight: 800;
        }
        .footer-note {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px dashed #cdd8e5;
            color: #6b7280;
            font-size: 7.8px;
            text-align: center;
            line-height: 1.25;
        }
        .footer-note strong {
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="hero">
            <table class="table">
                <tr>
                    <td style="width: 58%; vertical-align: top;">
                        <span class="eyebrow">Invoice</span>
                        <div class="invoice-title">{{ $invoice->inv_no ?? $order->orderno }}</div>
                        <div class="brand">{{ $businessName }}</div>
                        <div class="muted">{{ $businessCaption }}</div>
                    </td>
                    <td style="width: 42%; vertical-align: top;">
                        <div class="amount-box">
                            <span>Grand Total</span>
                            <strong>{{ $amountDue }}</strong>
                            <div class="muted" style="margin-top: 4px;">{{ $currencyCode }} payable before deadline</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="grid">
            <table class="table">
                <tr>
                    <td style="width: 50%; vertical-align: top; padding-right: 6px;">
                        <div class="panel">
                            <div class="panel-title">Invoice Details</div>
                            <table class="meta-table">
                                <tr><td class="label">Invoice No.</td><td class="value">{{ $invoice->inv_no ?? '-' }}</td></tr>
                                <tr><td class="label">Order Ref.</td><td class="value">{{ $order->orderno ?: '-' }}</td></tr>
                                <tr><td class="label">Issue Date</td><td class="value">{{ $issueDate ? dateTimeFormat($issueDate) : '-' }}</td></tr>
                                <tr><td class="label">Due Date</td><td class="value">{{ $dueDate ? dateTimeFormat($dueDate) : '-' }}</td></tr>
                            </table>
                        </div>
                    </td>
                    <td style="width: 50%; vertical-align: top; padding-left: 6px;">
                        <div class="panel">
                            <div class="panel-title">Billed To</div>
                            <table class="meta-table">
                                <tr><td class="label">Agent</td><td class="value">{{ $agent->name ?? $order->name ?? '-' }}</td></tr>
                                <tr><td class="label">Email</td><td class="value">{{ $order->email ?: '-' }}</td></tr>
                                <tr><td class="label">Contact</td><td class="value">{{ $pickup_people->name ?? $order->pickup_name ?? '-' }}</td></tr>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr class="summary-row">
                    <td colspan="2">
                        <table class="table">
                            <tr>
                                <td style="width: 28%; padding: 5px;">
                                    <div class="summary-block">
                                        <span class="summary-key">Service</span>
                                        <span class="summary-value">{{ $order->servicename ?: 'Tour Package' }}</span>
                                    </div>
                                </td>
                                <td style="width: 22%; padding: 5px;">
                                    <div class="summary-block">
                                        <span class="summary-key">Travel</span>
                                        <span class="summary-value">{{ $travelStart ? dateFormat($travelStart) : '-' }}{{ $travelEnd ? ' - ' . dateFormat($travelEnd) : '' }}</span>
                                    </div>
                                </td>
                                <td style="width: 12%; padding: 5px;">
                                    <div class="summary-block">
                                        <span class="summary-key">Guests</span>
                                        <span class="summary-value">{{ (int) $order->number_of_guests }} pax</span>
                                    </div>
                                </td>
                                <td style="width: 38%; padding: 5px;">
                                    <div class="summary-block">
                                        <span class="summary-key">Route</span>
                                        <span class="summary-value">{{ $order->pickup_location ?: '-' }}{{ $order->dropoff_location ? ' / ' . $order->dropoff_location : '' }}</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="summary-row">
                    <td colspan="2">
                        <div class="panel">
                            <div class="panel-title">Payment To</div>
                            <table class="payment-inline">
                                <tr>
                                    <td><span class="summary-key">Bank</span><span class="summary-value">{{ $bankAccount->bank ?? '-' }}</span></td>
                                    <td><span class="summary-key">Holder</span><span class="summary-value">{{ $bankAccount->name ?? '-' }}</span></td>
                                    <td><span class="summary-key">Account</span><span class="summary-value">{{ $bankAccount->account_usd ?? '-' }}</span></td>
                                    <td><span class="summary-key">Swift</span><span class="summary-value">{{ $bankAccount->swift_code ?? '-' }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="totals">
            <div class="totals-label">Amount Due</div>
            <div class="totals-value">{{ $amountDue }}</div>
        </div>

        <div class="footer-note">
            <strong>Payment Notice</strong><br>
            This invoice must be fully paid within 2 x 24 hours after approval or the order will be canceled automatically. After completing payment, please submit payment confirmation and upload the payment proof through the order page for verification.
        </div>
    </div>
</body>
</html>
