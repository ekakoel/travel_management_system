<?php

namespace App\Models;

use LogicException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPricingSnapshot extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'order_id',
        'snapshot_sequence',
        'pricing_version',
        'service',
        'service_id',
        'price_id',
        'base_currency',
        'display_currency',
        'quantity',
        'contract_rate_idr',
        'markup_amount_minor',
        'markup_currency',
        'markup_idr',
        'subtotal_idr',
        'tax_policy_id',
        'tax_percentage_scaled',
        'tax_percentage_scale',
        'tax_amount_idr',
        'rate_id',
        'rate_pair',
        'rate_side',
        'rate_value_scaled',
        'rate_value_scale',
        'rate_source',
        'rate_retrieved_at',
        'rate_max_age_seconds',
        'unit_price_idr',
        'unit_price_usd_minor',
        'gross_total_idr',
        'gross_total_usd_minor',
        'discount_total_idr',
        'discount_total_usd_minor',
        'addon_total_idr',
        'addon_total_usd_minor',
        'final_total_idr',
        'final_total_usd_minor',
        'rounding_policy',
        'calculated_at',
        'calculated_by',
        'reason',
        'input_fingerprint',
        'snapshot_checksum',
        'breakdown',
    ];

    protected $casts = [
        'snapshot_sequence' => 'integer',
        'quantity' => 'integer',
        'contract_rate_idr' => 'integer',
        'markup_amount_minor' => 'integer',
        'markup_idr' => 'integer',
        'subtotal_idr' => 'integer',
        'tax_percentage_scaled' => 'integer',
        'tax_percentage_scale' => 'integer',
        'tax_amount_idr' => 'integer',
        'rate_value_scaled' => 'integer',
        'rate_value_scale' => 'integer',
        'rate_max_age_seconds' => 'integer',
        'unit_price_idr' => 'integer',
        'unit_price_usd_minor' => 'integer',
        'gross_total_idr' => 'integer',
        'gross_total_usd_minor' => 'integer',
        'discount_total_idr' => 'integer',
        'discount_total_usd_minor' => 'integer',
        'addon_total_idr' => 'integer',
        'addon_total_usd_minor' => 'integer',
        'final_total_idr' => 'integer',
        'final_total_usd_minor' => 'integer',
        'rate_retrieved_at' => 'immutable_datetime',
        'calculated_at' => 'immutable_datetime',
        'breakdown' => 'array',
    ];

    protected static function booted(): void
    {
        static::updating(static function (): void {
            throw new LogicException('Committed pricing snapshots are immutable.');
        });

        static::deleting(static function (): void {
            throw new LogicException('Committed pricing snapshots cannot be deleted.');
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function taxPolicy(): BelongsTo
    {
        return $this->belongsTo(TaxPolicy::class, 'tax_policy_id');
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(UsdRates::class, 'rate_id');
    }
}
