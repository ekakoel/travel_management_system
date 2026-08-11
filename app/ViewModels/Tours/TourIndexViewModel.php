<?php

namespace App\ViewModels\Tours;

use App\Services\Tours\TourPackagePricingService;
use App\Support\MoneyFormatter;
use App\ValueObjects\Money;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class TourIndexViewModel
{
    public function __construct(
        public readonly Collection $activeTours,
        public readonly Collection $draftTours,
        public readonly Collection $archivedTours,
        private readonly TourPackagePricingService $pricingService,
        private readonly MoneyFormatter $formatter,
        private readonly CarbonInterface $serviceDate,
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
            'lowest_rate' => $this->lowestPublishedRate($tour),
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

    private function lowestPublishedRate($tour): ?string
    {
        $minor = $this->pricingService->quoteEachTier($tour, $this->serviceDate)
            ->map(fn (array $tier) => $tier['quote']->unitPriceUsdMinor())
            ->min();

        return $minor === null
            ? null
            : $this->formatter->decimal(Money::usdCents((int) $minor));
    }
}
