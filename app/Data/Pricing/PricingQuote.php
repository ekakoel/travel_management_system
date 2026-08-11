<?php

namespace App\Data\Pricing;

use App\Exceptions\PricingException;
use JsonSerializable;

final class PricingQuote implements JsonSerializable
{
    public function __construct(
        public readonly array $data,
    ) {
        foreach ([
            'pricing_version',
            'service',
            'service_id',
            'price_id',
            'base_currency',
            'display_currency',
            'final_total_idr',
            'final_total_usd_minor',
            'calculated_at',
        ] as $requiredKey) {
            if (!array_key_exists($requiredKey, $data)) {
                throw PricingException::arithmetic("Pricing quote is missing {$requiredKey}.");
            }
        }
    }

    public function finalTotalIdr(): int
    {
        return $this->data['final_total_idr'];
    }

    public function finalTotalUsdMinor(): int
    {
        return $this->data['final_total_usd_minor'];
    }

    public function unitPriceIdr(): int
    {
        return $this->data['unit_price_idr'];
    }

    public function unitPriceUsdMinor(): int
    {
        return $this->data['unit_price_usd_minor'];
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
