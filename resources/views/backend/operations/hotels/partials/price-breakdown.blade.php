@php
    $pricing = $pricing ?? [];
    $showKickBack = (int) ($pricing['kick_back_usd'] ?? 0) > 0;
@endphp

<div class="hotel-price-calculation-summary" aria-label="Agent rate summary">
    <div class="hotel-price-calculation-summary__item hotel-price-calculation-summary__item--agent">
        <span>Agent Rate</span>
        <strong>{{ currencyFormatUsd($pricing['net_rate'] ?? ($pricing['published_rate'] ?? 0)) }}</strong>
        <small>{{ currencyFormatIdr($pricing['net_rate_idr'] ?? ($pricing['published_rate_idr'] ?? 0)) }}</small>
    </div>
</div>

<div class="hotel-price-calculation" aria-label="Rate calculation breakdown">
    <div>
        <span>Contract</span>
        <strong>{{ currencyFormatIdr($pricing['effective_contract_rate_idr'] ?? 0) }}</strong>
        <small>
            {{ currencyFormatUsd($pricing['contract_rate_usd'] ?? 0) }} - contract x {{ $pricing['multiplier'] }} nights
        </small>
    </div>
    <div>
        <span>Markup</span>
        <strong>{{ currencyFormatUsd($pricing['markup_usd'] ?? 0) }}</strong>
        <small>{{ currencyFormatIdr($pricing['markup_idr'] ?? 0) }}</small>
    </div>
    <div>
        <span>Tax</span>
        <strong>{{ currencyFormatUsd($pricing['tax_usd'] ?? 0) }}</strong>
        <small>{{ currencyFormatIdr($pricing['tax_idr'] ?? 0) }} - {{ number_format((float) ($pricing['tax_percent'] ?? 0), 2) }}% of USD subtotal</small>
    </div>
    <div>
        <span>Formula</span>
        <strong>
            {{ currencyFormatUsd($pricing['contract_rate_usd'] ?? 0) }}
            + {{ currencyFormatUsd($pricing['markup_usd'] ?? 0) }}
            + {{ currencyFormatUsd($pricing['tax_usd'] ?? 0) }}
        </strong>
        <small>
            {{ currencyFormatIdr($pricing['effective_contract_rate_idr'] ?? 0) }}
            + {{ currencyFormatIdr($pricing['markup_idr'] ?? 0) }}
            + {{ currencyFormatIdr($pricing['tax_idr'] ?? 0) }}
        </small>
    </div>
    @if ($showKickBack)
        <div>
            <span>Kickback adjustment</span>
            <strong>- {{ currencyFormatUsd($pricing['kick_back_usd']) }}</strong>
            <small>{{ currencyFormatIdr($pricing['kick_back_idr'] ?? 0) }} applied after published rate</small>
        </div>
    @endif
</div>

@if (!($pricing['exchange_rate_valid'] ?? false))
    <div class="backend-alert backend-alert--danger hotel-price-calculation__warning">
        USD exchange rate is missing or invalid. Published price is not ready for sale.
    </div>
@endif
