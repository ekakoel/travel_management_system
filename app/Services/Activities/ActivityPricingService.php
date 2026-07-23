<?php

namespace App\Services\Activities;

class ActivityPricingService
{
    public function contractRateUsd(float|int|null $contractRateIdr, object|null $usdRate): int
    {
        $rate = (float) ($usdRate->rate ?? 0);

        if ($rate <= 0) {
            return 0;
        }

        return (int) ceil(((float) $contractRateIdr) / $rate);
    }

    public function subtotalUsd(float|int|null $contractRateIdr, float|int|null $markup, object|null $usdRate): int
    {
        return $this->contractRateUsd($contractRateIdr, $usdRate) + (int) ceil((float) $markup);
    }

    public function taxAmount(float|int|null $contractRateIdr, float|int|null $markup, object|null $usdRate, object|null $tax): int
    {
        $subtotal = $this->subtotalUsd($contractRateIdr, $markup, $usdRate);
        $taxPercent = (float) ($tax->tax ?? 0);

        return (int) ceil($subtotal * ($taxPercent / 100));
    }

    public function publishedRate(float|int|null $contractRateIdr, float|int|null $markup, object|null $usdRate, object|null $tax): int
    {
        return $this->subtotalUsd($contractRateIdr, $markup, $usdRate)
            + $this->taxAmount($contractRateIdr, $markup, $usdRate, $tax);
    }
}
