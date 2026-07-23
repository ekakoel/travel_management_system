<?php

namespace App\ViewModels\Transports;

use App\Models\Transports;
use App\Services\Transports\TransportPricingService;
use Illuminate\Support\Collection;

class TransportDetailViewModel
{
    public function __construct(
        public readonly Transports $transport,
        public readonly Collection $prices,
        public readonly object|null $usdRate,
        public readonly object|null $tax,
        private readonly TransportPricingService $pricingService,
    ) {
    }

    public function status(): string
    {
        return $this->transport->status ?: 'Unknown';
    }

    public function statusTone(): string
    {
        return match (strtolower($this->status())) {
            'active' => 'active',
            'archived' => 'muted',
            'rejected', 'invalid', 'removed' => 'danger',
            'waiting' => 'warning',
            default => 'draft',
        };
    }

    public function capacity(): string
    {
        return $this->transport->capacity . ' Seats';
    }

    public function stats(): array
    {
        return [
            ['label' => 'Status', 'value' => $this->status(), 'meta' => 'Current publication state', 'icon' => 'dw dw-bus', 'tone' => $this->statusTone()],
            ['label' => 'Capacity', 'value' => number_format((int) $this->transport->capacity), 'meta' => 'Seats available', 'icon' => 'fa fa-users', 'tone' => 'blue'],
            ['label' => 'Price Rows', 'value' => number_format($this->prices->count()), 'meta' => 'Configured pricing options', 'icon' => 'fa fa-tags', 'tone' => 'green'],
            ['label' => 'USD Rate', 'value' => number_format($this->usdRate->rate ?? 0), 'meta' => 'Pricing conversion reference', 'icon' => 'fa fa-dollar', 'tone' => 'amber'],
        ];
    }

    public function contentBlocks(): array
    {
        return [
            'Description' => $this->transport->description,
            'Include' => $this->transport->include,
            'Additional Information' => $this->transport->additional_info,
            'Cancellation Policy' => $this->transport->cancellation_policy,
        ];
    }

    public function priceRows(): Collection
    {
        return $this->prices->map(function ($price) {
            return [
                'model' => $price,
                'contract_rate_usd' => $this->pricingService->contractRateUsd($price->contract_rate, $this->usdRate),
                'tax_amount_usd' => $this->pricingService->taxAmount($price->contract_rate, $price->markup, $this->usdRate, $this->tax),
                'published_rate_usd' => $this->pricingService->publishedRate($price->contract_rate, $price->markup, $this->usdRate, $this->tax),
                'duration_label' => $price->duration ? $price->duration . ' Hours' : '-',
                'route_label' => trim(collect([$price->src, $price->dst])->filter()->implode(' → ')) ?: '-',
            ];
        });
    }
}
