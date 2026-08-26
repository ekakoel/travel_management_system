<?php

namespace App\Services\Transports;

use App\Models\TransportPrice;

class TransportPricingService
{
    public function createPrice(array $validated, int $authorId): TransportPrice
    {
        return TransportPrice::create($this->payload($validated, $authorId));
    }

    public function updatePrice(int $priceId, array $validated, int $authorId): TransportPrice
    {
        $price = TransportPrice::findOrFail($priceId);
        $price->update($this->payload($validated, $authorId));

        return $price;
    }

    public function deletePrice(int $priceId): TransportPrice
    {
        $price = TransportPrice::findOrFail($priceId);
        $price->delete();

        return $price;
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

    private function payload(array $validated, int $authorId): array
    {
        return [
            'transports_id' => $validated['transports_id'],
            'type' => $validated['type'],
            'src' => $validated['src'] ?? null,
            'dst' => $validated['dst'] ?? null,
            'duration' => $validated['duration'],
            'contract_rate' => $validated['contract_rate'],
            'markup' => $validated['markup'],
            'extra_time' => $validated['extra_time'],
            'additional_info' => $validated['additional_info'] ?? null,
            'author_id' => $authorId,
        ];
    }
}
