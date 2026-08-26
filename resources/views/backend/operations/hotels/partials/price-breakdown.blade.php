@php
    $pricing = $pricing ?? [];
    $showKickBack = (int) ($pricing['kick_back_usd'] ?? 0) > 0;
    $isPackagePerNight = ($pricing['display_mode'] ?? null) === 'package_per_night';
@endphp

<div class="hotel-price-calculation-summary {{ $isPackagePerNight ? 'hotel-price-calculation-summary--package' : '' }}" aria-label="Agent rate summary">
    <div class="hotel-price-calculation-summary__item hotel-price-calculation-summary__item--agent">
        <span>{{ $isPackagePerNight ? 'Agent Rate Per Night' : 'Agent Rate' }}</span>
        <strong>{{ currencyFormatUsd($pricing['net_rate'] ?? ($pricing['published_rate'] ?? 0)) }}</strong>
        <small>{{ currencyFormatIdr($pricing['net_rate_idr'] ?? ($pricing['published_rate_idr'] ?? 0)) }}</small>
    </div>

    @if ($isPackagePerNight)
        <div class="hotel-price-calculation-summary__item">
            <span>Package Total</span>
            <strong>{{ currencyFormatUsd($pricing['package_total_net_rate'] ?? ($pricing['package_total_published_rate'] ?? 0)) }}</strong>
            <small>
                {{ currencyFormatIdr($pricing['package_total_net_rate_idr'] ?? ($pricing['package_total_published_rate_idr'] ?? 0)) }}
                - {{ $pricing['package_duration'] ?? 1 }} nights
            </small>
        </div>
    @endif
</div>

<div class="hotel-price-calculation" aria-label="Rate calculation breakdown">
    <div>
        <span>Contract</span>
        <strong>{{ currencyFormatIdr($pricing['effective_contract_rate_idr'] ?? 0) }}</strong>
        <small>
            {{ currencyFormatUsd($pricing['contract_rate_usd'] ?? 0) }}
            @if ($isPackagePerNight)
                - allocated per night from {{ $pricing['package_duration'] ?? 1 }} nights
            @elseif (($pricing['multiplier'] ?? 1) > 1)
                - contract x {{ $pricing['multiplier'] }} nights
            @endif
        </small>
    </div>
    <div>
        <span>Markup</span>
        <strong>{{ currencyFormatUsd($pricing['markup_usd'] ?? 0) }}</strong>
        <small>{{ currencyFormatIdr($pricing['markup_idr'] ?? 0) }} {{ $isPackagePerNight ? 'allocated per night' : 'applied once per rate' }}</small>
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
