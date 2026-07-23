<?php

namespace App\Services\Hotels;

class HotelPricingService
{
    public function contractRateUsd(float|int|null $contractRateIdr, object|null $usdRate): int
    {
        $rate = (float) ($usdRate->rate ?? 0);

        if ($rate <= 0) {
            return 0;
        }

        return (int) ceil(((float) $contractRateIdr) / $rate);
    }

    public function subtotalUsd(float|int|null $contractRateIdr, float|int|null $markup, object|null $usdRate, int $multiplier = 1): int
    {
        $contractRate = ((float) $contractRateIdr) * max($multiplier, 1);

        return $this->contractRateUsd($contractRate, $usdRate) + (int) ceil((float) $markup);
    }

    public function taxAmount(float|int|null $contractRateIdr, float|int|null $markup, object|null $usdRate, object|null $tax, int $multiplier = 1): int
    {
        $subtotal = $this->subtotalUsd($contractRateIdr, $markup, $usdRate, $multiplier);
        $taxPercent = (float) ($tax->tax ?? 0);

        return (int) ceil($subtotal * ($taxPercent / 100));
    }

    public function publishedRate(float|int|null $contractRateIdr, float|int|null $markup, object|null $usdRate, object|null $tax, int $multiplier = 1): int
    {
        $subtotal = $this->subtotalUsd($contractRateIdr, $markup, $usdRate, $multiplier);

        return $subtotal + $this->taxAmount($contractRateIdr, $markup, $usdRate, $tax, $multiplier);
    }

    public function normalPricePublishedRate(object $price, object|null $usdRate, object|null $tax): int
    {
        return max(
            $this->publishedRate($price->contract_rate, $price->markup, $usdRate, $tax) - (int) ($price->kick_back ?? 0),
            0
        );
    }

    public function packagePublishedRate(object $package, object|null $usdRate, object|null $tax): int
    {
        return $this->publishedRate(
            $package->contract_rate,
            $package->markup,
            $usdRate,
            $tax,
            max((int) ($package->duration ?? 1), 1)
        );
    }
}
