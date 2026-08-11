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
    $totalPriceUsd = currencyFormatUsd($tourPricing['total_usd']);
    $amountDue = match ($currencyCode) {
        'CNY' => 'CNY ' . number_format((float) $invoice?->total_cny, 0),
        'TWD' => 'NT$ ' . number_format((float) $invoice?->total_twd, 0),
        'IDR' => 'Rp ' . number_format((float) $invoice?->total_idr, 0),
        default => $totalPriceUsd,
    };
    $invoiceLocale = ($invoiceLocale ?? 'zh') === 'zh-CN' ? 'zh-CN' : 'zh';
    $copy = $invoiceLocale === 'zh-CN'
        ? [
            'invoice' => '发票',
            'grand_total' => '总金额',
            'pay_before_deadline' => '请于付款期限前完成付款',
            'invoice_details' => '发票信息',
            'invoice_number' => '发票号码',
            'order_reference' => '订单编号',
            'issue_date' => '开具日期',
            'due_date' => '付款期限',
            'billed_to' => '付款对象',
            'agent' => '代理商',
            'email' => '电子邮箱',
            'contact' => '联系人',
            'service' => '服务',
            'travel' => '出行日期',
            'guests' => '旅客',
            'route' => '路线',
            'payment_to' => '收款账户',
            'bank' => '银行',
            'holder' => '户名',
            'account' => '账号',
            'amount_due' => '应付金额',
            'total_price_usd' => '总价（USD）',
            'payment_notice' => '付款通知',
            'notice' => '此发票须在订单批准后 2 x 24 小时内全额支付，否则订单将自动取消。付款完成后，请通过订单页面提交付款确认并上传付款凭证以便核对。',
        ]
        : [
            'invoice' => '發票',
            'grand_total' => '總金額',
            'pay_before_deadline' => '請於付款期限前完成付款',
            'invoice_details' => '發票資訊',
            'invoice_number' => '發票號碼',
            'order_reference' => '訂單編號',
            'issue_date' => '開立日期',
            'due_date' => '付款期限',
            'billed_to' => '付款對象',
            'agent' => '代理商',
            'email' => '電子郵件',
            'contact' => '聯絡人',
            'service' => '服務',
            'travel' => '旅遊日期',
            'guests' => '旅客',
            'route' => '路線',
            'payment_to' => '收款帳戶',
            'bank' => '銀行',
            'holder' => '戶名',
            'account' => '帳號',
            'amount_due' => '應付金額',
            'total_price_usd' => '總價（USD）',
            'payment_notice' => '付款通知',
            'notice' => '此發票須於訂單核准後 2 x 24 小時內全額付款，否則訂單將自動取消。完成付款後，請透過訂單頁面提交付款確認並上傳付款憑證以便核對。',
        ];
@endphp
<!DOCTYPE html>
<html lang="{{ $invoiceLocale }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $copy['invoice'] }} {{ $invoice->inv_no ?? $order->orderno }}</title>
    <style>
        @page {
            margin: 14px 22px;
        }
        body {
            margin: 0;
            color: #1f2937;
            font-family: "notosans", sans-serif;
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
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            width: 50%;
            padding: 0;
            vertical-align: top;
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
                        <span class="eyebrow">{{ $copy['invoice'] }}</span>
                        <div class="invoice-title">{{ $invoice->inv_no ?? $order->orderno }}</div>
                        <div class="brand">{{ $businessName }}</div>
                        <div class="muted">{{ $businessCaption }}</div>
                    </td>
                    <td style="width: 42%; vertical-align: top;">
                        <div class="amount-box">
                            <span>{{ $copy['grand_total'] }}</span>
                            <strong>{{ $amountDue }}</strong>
                            <div class="muted" style="margin-top: 4px;">{{ $currencyCode }} {{ $copy['pay_before_deadline'] }}</div>
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
                            <div class="panel-title">{{ $copy['invoice_details'] }}</div>
                            <table class="meta-table">
                                <tr><td class="label">{{ $copy['invoice_number'] }}</td><td class="value">{{ $invoice->inv_no ?? '-' }}</td></tr>
                                <tr><td class="label">{{ $copy['order_reference'] }}</td><td class="value">{{ $order->orderno ?: '-' }}</td></tr>
                                <tr><td class="label">{{ $copy['issue_date'] }}</td><td class="value">{{ $issueDate ? dateTimeFormat($issueDate) : '-' }}</td></tr>
                                <tr><td class="label">{{ $copy['due_date'] }}</td><td class="value">{{ $dueDate ? dateTimeFormat($dueDate) : '-' }}</td></tr>
                            </table>
                        </div>
                    </td>
                    <td style="width: 50%; vertical-align: top; padding-left: 6px;">
                        <div class="panel">
                            <div class="panel-title">{{ $copy['billed_to'] }}</div>
                            <table class="meta-table">
                                <tr><td class="label">{{ $copy['agent'] }}</td><td class="value">{{ $agent->name ?? $order->name ?? '-' }}</td></tr>
                                <tr><td class="label">{{ $copy['email'] }}</td><td class="value">{{ $order->email ?: '-' }}</td></tr>
                                <tr><td class="label">{{ $copy['contact'] }}</td><td class="value">{{ $pickup_people->name ?? $order->pickup_name ?? '-' }}</td></tr>
                            </table>
                        </div>
                    </td>
                </tr>
                <tr class="summary-row">
                    <td colspan="2">
                        <table class="table">
                            <tr>
                                <td style="width: 28%; padding-right: 5px;">
                                    <div class="summary-block">
                                        <span class="summary-key">{{ $copy['service'] }}</span>
                                        <span class="summary-value">{{ $order->servicename ?: 'Tour Package' }}</span>
                                    </div>
                                </td>
                                <td style="width: 22%; padding: 0 5px;">
                                    <div class="summary-block">
                                        <span class="summary-key">{{ $copy['travel'] }}</span>
                                        <span class="summary-value">{{ $travelStart ? dateFormat($travelStart) : '-' }}{{ $travelEnd ? ' - ' . dateFormat($travelEnd) : '' }}</span>
                                    </div>
                                </td>
                                <td style="width: 12%; padding: 0 5px;">
                                    <div class="summary-block">
                                        <span class="summary-key">{{ $copy['guests'] }}</span>
                                        <span class="summary-value">{{ (int) $order->number_of_guests }} pax</span>
                                    </div>
                                </td>
                                <td style="width: 38%; padding-left: 5px;">
                                    <div class="summary-block">
                                        <span class="summary-key">{{ $copy['route'] }}</span>
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
                            <div class="panel-title">{{ $copy['payment_to'] }}</div>
                            <table class="payment-inline">
                                <tr>
                                    <td><span class="summary-key">{{ $copy['bank'] }}</span><span class="summary-value">{{ $bankAccount->bank ?? '-' }}</span></td>
                                    <td><span class="summary-key">{{ $copy['holder'] }}</span><span class="summary-value">{{ $bankAccount->name ?? '-' }}</span></td>
                                    <td><span class="summary-key">{{ $copy['account'] }}</span><span class="summary-value">{{ $bankAccount->account_usd ?? '-' }}</span></td>
                                    <td><span class="summary-key">SWIFT</span><span class="summary-value">{{ $bankAccount->swift_code ?? '-' }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="totals">
            <table class="totals-table">
                <tr>
                    <td>
                        <div class="totals-label">{{ $copy['total_price_usd'] }}</div>
                        <div class="totals-value">{{ $totalPriceUsd }}</div>
                    </td>
                    <td style="text-align: right;">
                        <div class="totals-label">{{ $copy['amount_due'] }} ({{ $currencyCode }})</div>
                        <div class="totals-value">{{ $amountDue }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer-note">
            <strong>{{ $copy['payment_notice'] }}</strong><br>
            {{ $copy['notice'] }}
        </div>
    </div>
</body>
</html>
