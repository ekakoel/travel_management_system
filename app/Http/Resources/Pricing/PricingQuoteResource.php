<?php

namespace App\Http\Resources\Pricing;

use App\Support\MoneyFormatter;
use App\ValueObjects\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PricingQuoteResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        $data = $this->resource->toArray();
        $formatter = app(MoneyFormatter::class);

        return [
            'price_available' => true,
            'quote' => $data,
            'display' => [
                'unit_price_usd' => $formatter->decimal(Money::usdCents($data['unit_price_usd_minor'])),
                'gross_total_usd' => $formatter->decimal(Money::usdCents($data['gross_total_usd_minor'])),
                'discount_total_usd' => $formatter->decimal(Money::usdCents($data['discount_total_usd_minor'])),
                'final_total_usd' => $formatter->decimal(Money::usdCents($data['final_total_usd_minor'])),
            ],
        ];
    }
}
