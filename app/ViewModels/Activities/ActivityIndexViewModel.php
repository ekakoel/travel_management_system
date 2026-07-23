<?php

namespace App\ViewModels\Activities;

use App\Services\Activities\ActivityPricingService;
use Illuminate\Support\Collection;

class ActivityIndexViewModel
{
    public function __construct(
        public readonly Collection $activities,
        public readonly Collection $activeActivities,
        public readonly Collection $draftActivities,
        public readonly Collection $archivedActivities,
        public readonly object|null $usdRate,
        public readonly object|null $tax,
        private readonly ActivityPricingService $pricingService,
    ) {
    }

    public function stats(): array
    {
        return [
            ['label' => 'Total Products', 'value' => $this->activities->count(), 'meta' => 'Visible and draft activity records', 'icon' => 'fa fa-child', 'tone' => 'teal'],
            ['label' => 'Active', 'value' => $this->activeActivities->count(), 'meta' => 'Ready for sales workflow', 'icon' => 'fa fa-check', 'tone' => 'green'],
            ['label' => 'Draft', 'value' => $this->draftActivities->count(), 'meta' => 'Needs review before publish', 'icon' => 'fa fa-pencil', 'tone' => 'amber'],
            ['label' => 'Archived', 'value' => $this->archivedActivities->count(), 'meta' => 'Hidden from active operations', 'icon' => 'fa fa-archive', 'tone' => 'blue'],
        ];
    }

    public function rows(): Collection
    {
        return $this->activities->map(function ($activity) {
            return [
                'model' => $activity,
                'partner_name' => $activity->partners?->name ?: '-',
                'published_rate' => $this->pricingService->publishedRate($activity->contract_rate, $activity->markup, $this->usdRate, $this->tax),
                'status_tone' => $this->statusTone($activity->status),
            ];
        });
    }

    public function statusTone(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'active' => 'active',
            'archived' => 'muted',
            default => 'draft',
        };
    }
}
