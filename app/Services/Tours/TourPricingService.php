<?php

namespace App\Services\Tours;

use App\Models\TourPrices;

class TourPricingService
{
    public function createPrice(int $tourId, array $validated): TourPrices
    {
        return TourPrices::create([
            'tour_id' => $tourId,
            'min_qty' => $validated['min_qty'],
            'max_qty' => $validated['max_qty'],
            'contract_rate' => $validated['contract_rate'],
            'markup' => $validated['markup'],
            'expired_date' => date('Y-m-d', strtotime($validated['expired_date'])),
            'status' => 'Draft',
        ]);
    }

    public function updatePrice(int $priceId, array $validated): TourPrices
    {
        $tourPrice = TourPrices::findOrFail($priceId);
        $tourPrice->update([
            'min_qty' => $validated['min_qty'],
            'max_qty' => $validated['max_qty'],
            'contract_rate' => $validated['contract_rate'],
            'markup' => $validated['markup'],
            'expired_date' => $validated['expired_date'],
            'status' => $validated['status'],
        ]);

        return $tourPrice;
    }

    public function deletePrice(int $priceId): TourPrices
    {
        $tourPrice = TourPrices::findOrFail($priceId);
        $tourPrice->delete();

        return $tourPrice;
    }

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
