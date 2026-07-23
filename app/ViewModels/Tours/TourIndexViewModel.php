<?php

namespace App\ViewModels\Tours;

use App\Services\Tours\TourPricingService;
use Illuminate\Support\Collection;

class TourIndexViewModel
{
    public function __construct(
        public readonly Collection $activeTours,
        public readonly Collection $draftTours,
        public readonly Collection $archivedTours,
        public readonly object|null $usdRate,
        public readonly object|null $tax,
        private readonly TourPricingService $pricingService,
    ) {
    }

    public function allTours(): Collection
    {
        return $this->activeTours->concat($this->draftTours)->concat($this->archivedTours)->values();
    }

    public function totalTours(): int
    {
        return $this->allTours()->count();
    }

    public function stats(): array
    {
        return [
            ['label' => 'Total Tours', 'value' => $this->totalTours(), 'meta' => 'Active, draft, and archived', 'icon' => 'dw dw-map-6', 'tone' => 'blue'],
            ['label' => 'Active', 'value' => $this->activeTours->count(), 'meta' => 'Published packages', 'icon' => 'fa fa-check-circle', 'tone' => 'green'],
            ['label' => 'Draft', 'value' => $this->draftTours->count(), 'meta' => 'Needs review', 'icon' => 'fa fa-pencil-square-o', 'tone' => 'amber'],
            ['label' => 'Archived', 'value' => $this->archivedTours->count(), 'meta' => 'Hidden from selling flow', 'icon' => 'fa fa-archive', 'tone' => 'slate'],
        ];
    }

    public function rows(): Collection
    {
        return $this->allTours()->map(fn ($tour) => [
            'model' => $tour,
            'type_name' => $tour->type?->type ?: '-',
            'duration' => $this->duration($tour),
            'price_count' => $tour->prices->count(),
            'lowest_rate' => $this->lowestPublishedRate($tour->prices),
            'status_tone' => $this->statusTone($tour->status),
        ]);
    }

    public function statusTone(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'active' => 'active',
            'archived' => 'muted',
            default => 'draft',
        };
    }

    private function duration($tour): string
    {
        return trim(($tour->duration_days ? $tour->duration_days . 'D' : '') . ($tour->duration_nights ? '/' . $tour->duration_nights . 'N' : '')) ?: '-';
    }

    private function lowestPublishedRate(Collection $prices): int
    {
        return (int) $prices
            ->map(fn ($price) => $this->pricingService->publishedRate($price->contract_rate, $price->markup, $this->usdRate, $this->tax))
            ->filter()
            ->min();
    }
}
