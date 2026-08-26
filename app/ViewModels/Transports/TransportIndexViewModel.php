<?php

namespace App\ViewModels\Transports;

use Illuminate\Support\Collection;

class TransportIndexViewModel
{
    public function __construct(
        public readonly Collection $activeTransports,
        public readonly Collection $draftTransports,
        public readonly Collection $archivedTransports,
    ) {
    }

    public function visibleTransports(): Collection
    {
        return $this->activeTransports->concat($this->draftTransports)->values();
    }

    public function allTransports(): Collection
    {
        return $this->visibleTransports()->concat($this->archivedTransports)->values();
    }

    public function stats(): array
    {
        return [
            ['label' => 'Total Inventory', 'value' => $this->allTransports()->count(), 'meta' => 'All listed transport records', 'icon' => 'dw dw-bus', 'tone' => 'blue'],
            ['label' => 'Active', 'value' => $this->activeTransports->count(), 'meta' => 'Published to operations', 'icon' => 'fa fa-check', 'tone' => 'green'],
            ['label' => 'Draft', 'value' => $this->draftTransports->count(), 'meta' => 'Waiting for completion', 'icon' => 'fa fa-pencil', 'tone' => 'amber'],
            ['label' => 'Archived', 'value' => $this->archivedTransports->count(), 'meta' => 'Hidden from active list', 'icon' => 'fa fa-archive', 'tone' => 'slate'],
        ];
    }

    public function rows(): Collection
    {
        return $this->visibleTransports()->map(fn ($transport) => [
            'model' => $transport,
            'partner' => $transport->partner?->name ?: '-',
            'brand' => $transport->brand ?: 'No brand',
            'type' => $transport->type ?: '-',
            'capacity' => $transport->capacity . ' Seats',
            'price_count' => $transport->prices->count(),
            'status_tone' => $this->statusTone($transport->status),
        ]);
    }

    public function archivedRows(): Collection
    {
        return $this->archivedTransports->map(fn ($transport) => [
            'model' => $transport,
            'partner' => $transport->partner?->name ?: '-',
            'brand' => $transport->brand ?: 'No brand',
            'type' => $transport->type ?: '-',
            'capacity' => $transport->capacity . ' Seats',
            'status_tone' => $this->statusTone($transport->status),
        ]);
    }

    public function statusTone(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'active' => 'active',
            'archived' => 'muted',
            'removed' => 'danger',
            default => 'draft',
        };
    }
}
