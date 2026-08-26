@php
    $paymentCurrencyCode = optional($invoice->currency)->name ?: 'USD';
    $paymentBalance = (float) $invoice->balance;
    $paymentDecimals = $paymentCurrencyCode === 'USD' ? 2 : 0;
@endphp

<input type="hidden" name="payment_standard_version" value="1">

<div class="order-detail-grid">
    <div class="order-detail-upload">
        <label for="payment_date_{{ $order->id }}" class="form-label">
            @lang('messages.Payment Date') <span class="text-danger" aria-hidden="true">*</span>
        </label>
        <input
            type="date"
            name="payment_date"
            id="payment_date_{{ $order->id }}"
            value="{{ old('payment_date', now()->toDateString()) }}"
            max="{{ now()->toDateString() }}"
            class="form-control @error('payment_date') is-invalid @enderror"
            required
        >
        @error('payment_date')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="order-detail-upload">
        <label for="amount_paid_{{ $order->id }}" class="form-label">
            @lang('messages.Amount Paid') ({{ $paymentCurrencyCode }}) <span class="text-danger" aria-hidden="true">*</span>
        </label>
        <input
            type="number"
            name="amount_paid"
            id="amount_paid_{{ $order->id }}"
            value="{{ old('amount_paid', number_format($paymentBalance, $paymentDecimals, '.', '')) }}"
            min="0"
            max="{{ number_format($paymentBalance, $paymentDecimals, '.', '') }}"
            step="{{ $paymentCurrencyCode === 'USD' ? '0.01' : '1' }}"
            inputmode="decimal"
            class="form-control @error('amount_paid') is-invalid @enderror"
            required
        >
        <small class="form-text text-muted">
            @lang('messages.Outstanding balance'): {{ $paymentCurrencyCode }} {{ number_format($paymentBalance, $paymentDecimals, '.', ',') }}
        </small>
        @error('amount_paid')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="order-detail-upload mt-3">
    <label for="receipt_file_{{ $order->id }}" class="form-label">
        @lang('messages.Payment Proof') <span class="text-danger" aria-hidden="true">*</span>
    </label>
    <input
        type="file"
        name="receipt_file"
        id="receipt_file_{{ $order->id }}"
        accept="image/jpeg,image/png,application/pdf,.jpg,.jpeg,.png,.pdf"
        class="form-control @error('receipt_file') is-invalid @enderror"
        data-receipt-input="#payment-receipt-preview-{{ $order->id }}"
        data-receipt-empty="@lang('messages.No preview available')"
        required
    >
    <small class="form-text text-muted">@lang('messages.JPG, PNG, or PDF up to 5 MB.')</small>
    @error('receipt_file')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="order-detail-payment-preview mt-3" id="payment-receipt-preview-{{ $order->id }}">
    <span>@lang('messages.No preview available')</span>
</div>
