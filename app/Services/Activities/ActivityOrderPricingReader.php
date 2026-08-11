<?php

namespace App\Services\Activities;

use App\Exceptions\PricingException;
use App\Models\InvoiceAdmin;
use App\Models\Orders;
use App\Support\MoneyFormatter;
use App\Support\Pricing\FixedScale;
use App\ValueObjects\Money;

final class ActivityOrderPricingReader
{
    public function historicalValues(Orders $order, ?InvoiceAdmin $invoice = null): array
    {
        if ($order->service !== Orders::PUBLIC_ACTIVITY_SERVICE) {
            throw PricingException::unavailable('ACTIVITY_PRICING_ORDER_SERVICE_UNSUPPORTED');
        }

        $totalUsdMinor = $this->usdMinor($invoice?->total_usd ?: $order->final_price, 'final total');
        $grossTotalUsdMinor = $this->usdMinor($order->price_total ?: $order->normal_price, 'gross total');
        $unitPriceUsdMinor = $this->usdMinor($order->price_pax, 'unit price');
        $discountTotalUsdMinor = $grossTotalUsdMinor > $totalUsdMinor
            ? $grossTotalUsdMinor - $totalUsdMinor
            : 0;
        $rateValue = $invoice?->rate_usd ?: $invoice?->sell_usd ?: $order->usd_rate;
        $totalIdr = filled($invoice?->total_idr)
            ? (int) $invoice->total_idr
            : null;

        if ($totalIdr === null && filled($rateValue)) {
            $rateScaled = FixedScale::parseDecimal((string) $rateValue, FixedScale::FX_SCALE);
            $totalIdr = FixedScale::multiplyDivideUp(
                $totalUsdMinor,
                $rateScaled,
                100 * FixedScale::FX_SCALE
            );
        }

        return [
            'total_usd' => $this->usdDecimal($totalUsdMinor),
            'total_idr' => $totalIdr,
            'gross_total_usd' => $this->usdDecimal($grossTotalUsdMinor),
            'unit_price_usd' => $this->usdDecimal($unitPriceUsdMinor),
            'discount_total_usd' => $this->usdDecimal($discountTotalUsdMinor),
            'addon_total_usd' => '0.00',
            'rate_usd' => filled($rateValue) ? (string) $rateValue : null,
            'sell_usd' => filled($rateValue) ? (string) $rateValue : null,
            'source' => $invoice ? 'committed_invoice' : 'committed_order',
            'legacy_fallback' => true,
        ];
    }

    private function usdMinor(mixed $value, string $field): int
    {
        if (!filled($value)) {
            throw PricingException::unavailable(
                'ACTIVITY_PRICING_ORDER_VALUE_MISSING',
                "Stored Activity {$field} is unavailable."
            );
        }

        return FixedScale::parseDecimal((string) $value, 100);
    }

    private function usdDecimal(int $amount): string
    {
        return app(MoneyFormatter::class)->decimal(Money::usdCents($amount));
    }
}
