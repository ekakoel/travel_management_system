<?php

namespace App\ViewModels\Hotels;

use App\Models\Hotels;
use App\Services\Hotels\HotelPricingService;
use Illuminate\Support\Collection;

class HotelDetailViewModel
{
    public function __construct(
        public readonly Hotels $hotel,
        public readonly Collection $rooms,
        public readonly Collection $normalPrices,
        public readonly Collection $promos,
        public readonly Collection $packages,
        public readonly Collection $additionalCharges,
        public readonly Collection $contracts,
        public readonly object|null $usdRate,
        public readonly object|null $tax,
        public readonly string $now,
        public readonly object|null $latestPrice,
        public readonly object|null $author,
        private readonly HotelPricingService $pricingService,
    ) {
    }

    public function status(): string
    {
        return $this->hotel->status ?: 'Draft';
    }

    public function statusTone(): string
    {
        return match (strtolower($this->status())) {
            'active' => 'active',
            'archived' => 'muted',
            'expired' => 'expired',
            default => 'draft',
        };
    }

    public function stats(): array
    {
        $activeRooms = $this->rooms->where('status', 'Active')->count();
        $draftRooms = $this->rooms->where('status', 'Draft')->count();
        $normalPrices = $this->visibleNormalPrices();
        $promos = $this->visiblePromos();
        $packages = $this->visiblePackages();
        $additionalCharges = $this->visibleAdditionalCharges();
        $pricingRows = $normalPrices->count()
            + $promos->count()
            + $packages->count()
            + $additionalCharges->count();

        return [
            ['label' => 'Status', 'value' => $this->status(), 'meta' => 'Current publication state', 'icon' => 'fa fa-check-circle', 'tone' => $this->statusTone() === 'active' ? 'green' : 'amber'],
            ['label' => 'Rooms', 'value' => number_format($this->rooms->count()), 'meta' => "{$activeRooms} active / {$draftRooms} draft", 'icon' => 'fa fa-bed', 'tone' => 'teal'],
            ['label' => 'Contracts', 'value' => number_format($this->contracts->count()), 'meta' => 'Supplier contract records', 'icon' => 'fa fa-file-alt', 'tone' => 'blue'],
            ['label' => 'Pricing Rows', 'value' => number_format($pricingRows), 'meta' => "{$normalPrices->count()} normal / {$promos->count()} promos / {$packages->count()} packages / {$additionalCharges->count()} charges", 'icon' => 'fa fa-tags', 'tone' => 'amber'],
            ['label' => 'Latest Price', 'value' => $this->latestPriceDate() ? dateFormat($this->latestPriceDate()) : '-', 'meta' => 'Most recent normal price update', 'icon' => 'fa fa-calendar-check-o', 'tone' => 'green'],
        ];
    }

    public function contractCount(): int
    {
        return $this->contracts->count();
    }

    public function latestPriceDate(): ?string
    {
        return $this->latestPrice->date ?? null;
    }

    public function createdAge(): string
    {
        return $this->hotel->created_at?->diffForHumans() ?: '-';
    }

    public function normalPriceRows(): Collection
    {
        $conflictingIds = $this->normalPriceConflictIds();

        return $this->visibleNormalPrices()->map(function ($price) use ($conflictingIds) {
            $kickBack = max((int) ($price->kick_back ?? 0), 0);
            $hasConflict = in_array((int) $price->id, $conflictingIds, true);
            $statusLabel = $hasConflict
                ? 'Conflict'
                : $this->periodStatus($price->start_date, $price->end_date);
            $pricing = $this->pricingService->rateBreakdown(
                $price->contract_rate,
                $price->markup,
                $this->usdRate,
                $this->tax,
                1,
                $kickBack
            );

            return [
                'model' => $price,
                'room_name' => $price->rooms?->rooms ?: '-',
                'search' => strtolower(($price->rooms?->rooms ?? '').' '.$statusLabel),
                'period' => dateFormat($price->start_date).' - '.dateFormat($price->end_date),
                'kick_back' => $kickBack,
                'kick_back_label' => $kickBack > 0 ? currencyFormatUsd($kickBack) : '-',
                'pricing' => $pricing,
                'published_rate' => $pricing['published_rate'],
                'net_rate' => $pricing['net_rate'],
                'status_label' => $statusLabel,
                'status_tone' => $hasConflict
                    ? 'danger'
                    : $this->statusToneForPeriod('Active', $price->end_date, $price->start_date),
            ];
        });
    }

    private function normalPriceConflictIds(): array
    {
        $conflicts = [];

        foreach ($this->visibleNormalPrices()->groupBy('rooms_id') as $roomPrices) {
            $prices = $roomPrices->sortBy('start_date')->values();

            for ($index = 0; $index < $prices->count(); $index++) {
                $current = $prices[$index];
                if (!$current->start_date || !$current->end_date) {
                    continue;
                }

                for ($comparison = $index + 1; $comparison < $prices->count(); $comparison++) {
                    $candidate = $prices[$comparison];
                    if (!$candidate->start_date || !$candidate->end_date) {
                        continue;
                    }
                    if ($candidate->start_date > $current->end_date) {
                        break;
                    }

                    if ($candidate->end_date >= $current->start_date) {
                        $conflicts[] = (int) $current->id;
                        $conflicts[] = (int) $candidate->id;
                    }
                }
            }
        }

        return array_values(array_unique($conflicts));
    }

    public function promoRows(): Collection
    {
        return $this->visiblePromos()->map(function ($promo) {
            $roomName = $promo->rooms?->rooms ?: '-';
            $pricing = $this->pricingService->rateBreakdown($promo->contract_rate, $promo->markup, $this->usdRate, $this->tax);

            return [
                'model' => $promo,
                'room_name' => $roomName,
                'search' => strtolower(($promo->name ?? '').' '.$roomName),
                'booking_period' => dateFormat($promo->book_periode_start).' - '.dateFormat($promo->book_periode_end),
                'stay_period' => dateFormat($promo->periode_start).' - '.dateFormat($promo->periode_end),
                'pricing' => $pricing,
                'published_rate' => $pricing['published_rate'],
                'status_tone' => $this->statusToneForPeriod($promo->status, $promo->book_periode_end),
            ];
        });
    }

    public function packageRows(): Collection
    {
        return $this->visiblePackages()->map(function ($package) {
            $roomName = $package->room?->rooms ?: '-';
            $pricing = $this->pricingService->rateBreakdown(
                $package->contract_rate,
                $package->markup,
                $this->usdRate,
                $this->tax,
                max((int) ($package->duration ?? 1), 1)
            );

            return [
                'model' => $package,
                'room_name' => $roomName,
                'search' => strtolower(($package->name ?? '').' '.$roomName),
                'stay_period' => dateFormat($package->stay_period_start).' - '.dateFormat($package->stay_period_end),
                'pricing' => $pricing,
                'published_rate' => $pricing['published_rate'],
                'status_tone' => $this->statusToneForPeriod($package->status, $package->stay_period_end),
            ];
        });
    }

    public function additionalChargeRows(): Collection
    {
        return $this->visibleAdditionalCharges()->map(function ($charge) {
            $pricing = $this->pricingService->rateBreakdown($charge->contract_rate, $charge->markup, $this->usdRate, $this->tax);

            return [
                'model' => $charge,
                'mandatory_label' => $charge->mandatory
                    ? dateFormat($charge->mandatory_start).' - '.dateFormat($charge->mandatory_end)
                    : '-',
                'pricing' => $pricing,
                'published_rate' => $pricing['published_rate'],
            ];
        });
    }

    private function periodStatus(?string $startDate, ?string $endDate): string
    {
        if ($endDate && $endDate < $this->now) {
            return 'Expired';
        }

        if ($startDate && $startDate > $this->now) {
            return 'Upcoming';
        }

        return 'Active';
    }

    private function visibleNormalPrices(): Collection
    {
        return $this->normalPrices
            ->reject(fn ($price) => $this->hasExpired($price->end_date))
            ->values();
    }

    private function visiblePromos(): Collection
    {
        return $this->promos
            ->reject(fn ($promo) => $this->hasExpired($promo->book_periode_end) || $this->hasExpired($promo->periode_end))
            ->values();
    }

    private function visiblePackages(): Collection
    {
        return $this->packages
            ->reject(fn ($package) => $this->hasExpired($package->stay_period_end))
            ->values();
    }

    private function visibleAdditionalCharges(): Collection
    {
        return $this->additionalCharges
            ->reject(function ($charge) {
                $endDate = $charge->must_buy_end ?: $charge->active_date;

                return $this->hasExpired($endDate);
            })
            ->values();
    }

    private function hasExpired(?string $endDate): bool
    {
        return filled($endDate) && $endDate < $this->now;
    }

    private function statusToneForPeriod(?string $status, ?string $endDate, ?string $startDate = null): string
    {
        if ($endDate && $endDate < $this->now) {
            return 'expired';
        }

        if ($startDate && $startDate > $this->now) {
            return 'info';
        }

        return strtolower((string) $status) === 'active' ? 'active' : 'draft';
    }
}
