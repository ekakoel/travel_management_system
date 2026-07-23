<?php

namespace App\ViewModels\Tours;

use App\Models\Tours;
use App\Services\Tours\TourPricingService;
use Illuminate\Support\Collection;

class TourDetailViewModel
{
    public function __construct(
        public readonly Tours $tour,
        public readonly object|null $usdRate,
        public readonly object|null $tax,
        public readonly Collection $actionLogs,
        private readonly TourPricingService $pricingService,
    ) {
    }

    public function status(): string
    {
        return $this->tour->status ?: 'Draft';
    }

    public function statusTone(): string
    {
        return match (strtolower($this->status())) {
            'active' => 'active',
            'archived' => 'muted',
            default => 'draft',
        };
    }

    public function duration(): string
    {
        return trim(($this->tour->duration_days ? $this->tour->duration_days . 'D' : '') . ($this->tour->duration_nights ? '/' . $this->tour->duration_nights . 'N' : '')) ?: '-';
    }

    public function stats(): array
    {
        return [
            ['label' => 'Duration', 'value' => $this->duration(), 'meta' => 'Tour itinerary length', 'icon' => 'dw dw-map-6', 'tone' => 'blue'],
            ['label' => 'Price Rows', 'value' => number_format($this->tour->prices->count()), 'meta' => 'Active price validity rows', 'icon' => 'fa fa-tags', 'tone' => 'green'],
            ['label' => 'Gallery', 'value' => number_format($this->tour->images->count()), 'meta' => 'Uploaded tour images', 'icon' => 'fa fa-picture-o', 'tone' => 'teal'],
            ['label' => 'USD Rate', 'value' => number_format($this->usdRate->rate ?? 0), 'meta' => 'Pricing conversion reference', 'icon' => 'fa fa-money', 'tone' => 'amber'],
        ];
    }

    public function contentBlocks(): array
    {
        return [
            'Short Description' => $this->tour->short_description,
            'Package Highlights' => $this->tour->package_highlights,
            'Itinerary' => $this->tour->itinerary,
            'Inclusions' => $this->tour->include,
            'Exclusions' => $this->tour->exclude,
            'Additional Information' => $this->tour->additional_info,
            'Cancellation Policy' => $this->tour->cancellation_policy,
        ];
    }

    public function priceRows(): Collection
    {
        return $this->tour->prices->map(function ($price) {
            $capacity = $price->min_qty . ' - ' . $price->max_qty . ' Guests';

            return [
                'model' => $price,
                'capacity' => $capacity,
                'status_tone' => strtolower((string) $price->status) === 'active' ? 'active' : 'draft',
                'published_rate' => $this->pricingService->publishedRate($price->contract_rate, $price->markup, $this->usdRate, $this->tax),
                'contract_rate_usd' => $this->pricingService->contractRateUsd($price->contract_rate, $this->usdRate),
                'tax_amount' => $this->pricingService->taxAmount($price->contract_rate, $price->markup, $this->usdRate, $this->tax),
            ];
        });
    }
}
