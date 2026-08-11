<?php

namespace App\Services\Tours;

use App\Exceptions\PricingException;
use App\Models\TourPrices;
use App\Support\Pricing\FixedScale;
use App\ValueObjects\Money;

final class TourMarkupResolver
{
    public function resolve(TourPrices $price): Money
    {
        return match ($price->resolvedMarkupType()) {
            TourPrices::MARKUP_TYPE_USD => Money::usdCents(
                FixedScale::parseDecimal((string) $price->markup_amount, 100)
            ),
            TourPrices::MARKUP_TYPE_IDR => Money::idr(
                FixedScale::parseDecimal((string) $price->markup_amount, 1)
            ),
            TourPrices::MARKUP_TYPE_PERCENTAGE => Money::idr(
                FixedScale::multiplyDivideHalfUp(
                    (int) $price->contract_rate_idr,
                    FixedScale::parseDecimal(
                        (string) $price->markup_amount,
                        FixedScale::PERCENTAGE_SCALE
                    ),
                    FixedScale::PERCENTAGE_DENOMINATOR
                )
            ),
            default => throw PricingException::unavailable('PRICING_MARKUP_TYPE_INVALID'),
        };
    }
}
