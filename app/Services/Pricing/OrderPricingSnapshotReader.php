<?php

namespace App\Services\Pricing;

use App\Exceptions\PricingException;
use App\Models\InvoiceAdmin;
use App\Models\OrderPricingSnapshot;
use App\Models\Orders;
use App\Support\MoneyFormatter;
use App\Support\Pricing\FixedScale;
use App\ValueObjects\Money;
use Illuminate\Support\Facades\Log;

final class OrderPricingSnapshotReader
{
    public function requireActive(Orders $order): OrderPricingSnapshot
    {
        if ($order->service !== Orders::PUBLIC_TOUR_SERVICE) {
            throw PricingException::unavailable('PRICING_ORDER_SERVICE_UNSUPPORTED');
        }

        $snapshot = $order->relationLoaded('activePricingSnapshot')
            ? $order->activePricingSnapshot
            : $order->activePricingSnapshot()->first();

        if ($snapshot === null) {
            throw PricingException::unavailable('PRICING_ORDER_SNAPSHOT_MISSING');
        }

        if ((int) $snapshot->order_id !== (int) $order->id
            || $snapshot->service !== Orders::PUBLIC_TOUR_SERVICE
            || (int) $snapshot->service_id !== (int) $order->service_id) {
            throw PricingException::unavailable('PRICING_ORDER_SNAPSHOT_SERVICE_MISMATCH');
        }

        return $snapshot;
    }

    public function historicalValues(Orders $order, ?InvoiceAdmin $invoice = null): array
    {
        if ($order->service !== Orders::PUBLIC_TOUR_SERVICE) {
            throw PricingException::unavailable('PRICING_ORDER_SERVICE_UNSUPPORTED');
        }

        $snapshot = $order->relationLoaded('activePricingSnapshot')
            ? $order->activePricingSnapshot
            : $order->activePricingSnapshot()->first();

        if ($snapshot === null) {
            return $this->legacyValues($order, $invoice);
        }

        $snapshot = $this->requireActive($order);
        $rate = FixedScale::formatDecimal(
            $snapshot->rate_value_scaled,
            $snapshot->rate_value_scale
        );

        return [
            'total_usd' => app(MoneyFormatter::class)
                ->decimal(Money::usdCents($snapshot->final_total_usd_minor)),
            'total_idr' => $snapshot->final_total_idr,
            'gross_total_usd' => app(MoneyFormatter::class)
                ->decimal(Money::usdCents($snapshot->gross_total_usd_minor)),
            'unit_price_usd' => app(MoneyFormatter::class)
                ->decimal(Money::usdCents($snapshot->unit_price_usd_minor)),
            'discount_total_usd' => app(MoneyFormatter::class)
                ->decimal(Money::usdCents($snapshot->discount_total_usd_minor)),
            'addon_total_usd' => app(MoneyFormatter::class)
                ->decimal(Money::usdCents($snapshot->addon_total_usd_minor)),
            'rate_usd' => $rate,
            'sell_usd' => $rate,
            'source' => 'snapshot',
            'legacy_fallback' => false,
        ];
    }

    public function invoiceValues(Orders $order, ?InvoiceAdmin $invoice = null): array
    {
        return $this->historicalValues($order, $invoice);
    }

    private function legacyValues(Orders $order, ?InvoiceAdmin $invoice): array
    {
        if ($order->pricing_version !== null
            || $order->pricing_snapshot_id !== null
            || $order->final_total_idr !== null
            || $order->final_total_usd_minor !== null
            || $order->pricing_calculated_at !== null) {
            throw PricingException::unavailable('PRICING_ORDER_SNAPSHOT_MISSING');
        }

        $totalUsd = $invoice?->total_usd ?: $order->final_price;
        $rateUsd = $invoice?->rate_usd ?: $order->usd_rate;

        if (!filled($totalUsd) || !filled($rateUsd)) {
            throw PricingException::unavailable('PRICING_LEGACY_ORDER_TOTAL_MISSING');
        }

        $totalUsdMinor = FixedScale::parseDecimal((string) $totalUsd, 100);
        $rateScaled = FixedScale::parseDecimal((string) $rateUsd, FixedScale::FX_SCALE);
        $totalIdr = filled($invoice?->total_idr)
            ? FixedScale::parseDecimal((string) $invoice->total_idr, 1)
            : FixedScale::multiplyDivideHalfUp(
                $totalUsdMinor,
                $rateScaled,
                100 * FixedScale::FX_SCALE
            );
        $formattedRate = FixedScale::formatDecimal($rateScaled, FixedScale::FX_SCALE);

        Log::warning('Legacy Tour Package pricing fallback used.', [
            'order_id' => $order->id,
            'invoice_id' => $invoice?->id,
            'service' => $order->service,
            'fallback_source' => $invoice ? 'committed_invoice' : 'stored_order_fields',
        ]);

        return [
            'total_usd' => app(MoneyFormatter::class)->decimal(Money::usdCents($totalUsdMinor)),
            'total_idr' => $totalIdr,
            'gross_total_usd' => (string) ($order->price_total ?: $totalUsd),
            'unit_price_usd' => (string) ($order->price_pax ?: $totalUsd),
            'discount_total_usd' => (string) (
                (int) ($order->discounts ?: 0)
                + (int) ($order->bookingcode_disc ?: 0)
            ),
            'addon_total_usd' => (string) ($order->additional_service_total_price ?: 0),
            'rate_usd' => $formattedRate,
            'sell_usd' => $formattedRate,
            'source' => $invoice ? 'legacy_committed_invoice' : 'legacy_order',
            'legacy_fallback' => true,
        ];
    }
}
