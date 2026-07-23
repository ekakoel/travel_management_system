<?php

namespace App\ViewModels\Activities;

use App\Models\Activities;
use App\Services\Activities\ActivityPricingService;

class ActivityDetailViewModel
{
    public function __construct(
        public readonly Activities $activity,
        public readonly object|null $partner,
        public readonly object|null $usdRate,
        public readonly object|null $tax,
        private readonly ActivityPricingService $pricingService,
    ) {
    }

    public function status(): string
    {
        return $this->activity->status ?: 'Draft';
    }

    public function statusTone(): string
    {
        return match (strtolower($this->status())) {
            'active' => 'active',
            'archived' => 'muted',
            default => 'draft',
        };
    }

    public function taxAmount(): int
    {
        return $this->pricingService->taxAmount($this->activity->contract_rate, $this->activity->markup, $this->usdRate, $this->tax);
    }

    public function publishedRate(): int
    {
        return $this->pricingService->publishedRate($this->activity->contract_rate, $this->activity->markup, $this->usdRate, $this->tax);
    }

    public function stats(): array
    {
        return [
            ['label' => 'Published Rate', 'value' => currencyFormatUsd($this->publishedRate()), 'meta' => 'Calculated price per pax', 'icon' => 'fa fa-usd', 'tone' => 'blue'],
            ['label' => 'Capacity', 'value' => number_format((int) $this->activity->qty), 'meta' => 'Maximum pax', 'icon' => 'fa fa-users', 'tone' => 'teal'],
            ['label' => 'Minimum Pax', 'value' => number_format((int) $this->activity->min_pax), 'meta' => 'Minimum booking pax', 'icon' => 'fa fa-user-plus', 'tone' => 'amber'],
            ['label' => 'Type', 'value' => $this->activity->type ?: '-', 'meta' => $this->activity->location ?: 'No location', 'icon' => 'fa fa-tags', 'tone' => 'green'],
        ];
    }

    public function contentBlocks(): array
    {
        return [
            'Description' => $this->activity->description,
            'Itinerary' => $this->activity->itinerary,
            'Include' => $this->activity->include,
            'Additional Information' => $this->activity->additional_info,
            'Cancellation Policy' => $this->activity->cancellation_policy,
        ];
    }
}
