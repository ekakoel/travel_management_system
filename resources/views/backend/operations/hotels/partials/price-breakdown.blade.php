@php
    $pricing = $pricing ?? [];
    $showKickBack = (int) ($pricing['kick_back_usd'] ?? 0) > 0;
@endphp

<div class="hotel-price-calculation">
    <div>
        <span>Contract</span>
        <strong>{{ currencyFormatIdr($pricing['effective_contract_rate_idr'] ?? 0) }}</strong>
        <small>
            {{ currencyFormatUsd($pricing['contract_rate_usd'] ?? 0) }}
            @if (($pricing['multiplier'] ?? 1) > 1)
                · contract × {{ $pricing['multiplier'] }} nights
            @endif
        </small>
    </div>
    <div>
        <span>Markup</span>
        <strong>{{ currencyFormatUsd($pricing['markup_usd'] ?? 0) }}</strong>
        <small>Applied once per published rate</small>
    </div>
    <div>
        <span>Tax</span>
        <strong>{{ currencyFormatUsd($pricing['tax_usd'] ?? 0) }}</strong>
        <small>{{ number_format((float) ($pricing['tax_percent'] ?? 0), 2) }}% of USD subtotal</small>
    </div>
    <div>
        <span>Formula</span>
        <strong>
            {{ currencyFormatUsd($pricing['contract_rate_usd'] ?? 0) }}
            + {{ currencyFormatUsd($pricing['markup_usd'] ?? 0) }}
            + {{ currencyFormatUsd($pricing['tax_usd'] ?? 0) }}
        </strong>
        <small>Contract USD + markup + tax</small>
    </div>
    @if ($showKickBack)
        <div>
            <span>Kickback adjustment</span>
            <strong>- {{ currencyFormatUsd($pricing['kick_back_usd']) }}</strong>
            <small>Applied after published rate</small>
        </div>
    @endif
</div>

@if (!($pricing['exchange_rate_valid'] ?? false))
    <div class="backend-alert backend-alert--danger hotel-price-calculation__warning">
        USD exchange rate is missing or invalid. Published price is not ready for sale.
    </div>
@endif
