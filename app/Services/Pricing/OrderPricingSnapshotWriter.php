<?php

namespace App\Services\Pricing;

use App\Data\Pricing\PricingQuote;
use App\Data\Pricing\PricingSnapshot;
use App\Models\OrderPricingSnapshot;
use App\Models\Orders;

final class OrderPricingSnapshotWriter
{
    public function commit(
        Orders $order,
        PricingQuote $quote,
        ?int $actorId,
        string $reason = 'initial_order_pricing',
    ): OrderPricingSnapshot {
        $sequence = ((int) $order->pricingSnapshots()->max('snapshot_sequence')) + 1;
        $snapshot = PricingSnapshot::fromQuote($quote, $sequence, $actorId, $reason);
        $data = $snapshot->toArray();

        $record = OrderPricingSnapshot::create([
            'order_id' => $order->id,
            'snapshot_sequence' => $data['snapshot_sequence'],
            'pricing_version' => $data['pricing_version'],
            'service' => $data['service'],
            'service_id' => $data['service_id'],
            'price_id' => $data['price_id'],
            'base_currency' => $data['base_currency'],
            'display_currency' => $data['display_currency'],
            'quantity' => $data['quantity'],
            'contract_rate_idr' => $data['contract_rate_idr'],
            'markup_amount_minor' => $data['markup_amount_minor'],
            'markup_currency' => $data['markup_currency'],
            'markup_idr' => $data['markup_idr'],
            'subtotal_idr' => $data['subtotal_idr'],
            'tax_policy_id' => $data['tax_policy_id'],
            'tax_percentage_scaled' => $data['tax_percentage_scaled'],
            'tax_percentage_scale' => $data['tax_percentage_scale'],
            'tax_amount_idr' => $data['tax_amount_idr'],
            'rate_id' => $data['rate_id'],
            'rate_pair' => $data['rate_pair'],
            'rate_side' => $data['rate_side'],
            'rate_value_scaled' => $data['rate_value_scaled'],
            'rate_value_scale' => $data['rate_value_scale'],
            'rate_source' => $data['rate_source'],
            'rate_retrieved_at' => $data['rate_retrieved_at'],
            'rate_max_age_seconds' => $data['rate_max_age_seconds'],
            'unit_price_idr' => $data['unit_price_idr'],
            'unit_price_usd_minor' => $data['unit_price_usd_minor'],
            'gross_total_idr' => $data['gross_total_idr'],
            'gross_total_usd_minor' => $data['gross_total_usd_minor'],
            'discount_total_idr' => $data['discount_total_idr'],
            'discount_total_usd_minor' => $data['discount_total_usd_minor'],
            'addon_total_idr' => $data['addon_total_idr'],
            'addon_total_usd_minor' => $data['addon_total_usd_minor'],
            'final_total_idr' => $data['final_total_idr'],
            'final_total_usd_minor' => $data['final_total_usd_minor'],
            'rounding_policy' => $data['rounding_policy'],
            'calculated_at' => $data['calculated_at'],
            'calculated_by' => $data['calculated_by'],
            'reason' => $data['reason'],
            'input_fingerprint' => $data['input_fingerprint'],
            'snapshot_checksum' => $data['snapshot_checksum'],
            'breakdown' => $quote->toArray(),
        ]);

        $order->forceFill([
            'pricing_version' => $data['pricing_version'],
            'pricing_snapshot_id' => $record->id,
            'base_currency' => $data['base_currency'],
            'display_currency' => $data['display_currency'],
            'final_total_idr' => $data['final_total_idr'],
            'final_total_usd_minor' => $data['final_total_usd_minor'],
            'pricing_calculated_at' => $data['calculated_at'],
        ])->save();

        return $record;
    }
}
