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
        $pricingRows = $this->normalPrices->count()
            + $this->promos->count()
            + $this->packages->count()
            + $this->additionalCharges->count();

        return [
            ['label' => 'Status', 'value' => $this->status(), 'meta' => 'Current publication state', 'icon' => 'fa fa-check-circle', 'tone' => $this->statusTone() === 'active' ? 'green' : 'amber'],
            ['label' => 'Rooms', 'value' => number_format($this->rooms->count()), 'meta' => "{$activeRooms} active / {$draftRooms} draft", 'icon' => 'fa fa-bed', 'tone' => 'teal'],
            ['label' => 'Contracts', 'value' => number_format($this->contracts->count()), 'meta' => 'Supplier contract records', 'icon' => 'fa fa-file-text-o', 'tone' => 'blue'],
            ['label' => 'Pricing Rows', 'value' => number_format($pricingRows), 'meta' => "{$this->normalPrices->count()} normal / {$this->promos->count()} promos / {$this->packages->count()} packages / {$this->additionalCharges->count()} charges", 'icon' => 'fa fa-tags', 'tone' => 'amber'],
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
        return $this->normalPrices->map(function ($price) {
            $kickBack = max((int) ($price->kick_back ?? 0), 0);

            return [
                'model' => $price,
                'room_name' => $price->rooms?->rooms ?: '-',
                'search' => strtolower($price->rooms?->rooms ?? ''),
                'period' => dateFormat($price->start_date).' - '.dateFormat($price->end_date),
                'kick_back' => $kickBack,
                'kick_back_label' => $kickBack > 0 ? currencyFormatUsd($kickBack) : '-',
                'published_rate' => $this->pricingService->normalPricePublishedRate($price, $this->usdRate, $this->tax),
            ];
        });
    }

    public function promoRows(): Collection
    {
        return $this->promos->map(function ($promo) {
            $roomName = $promo->rooms?->rooms ?: '-';

            return [
                'model' => $promo,
                'room_name' => $roomName,
                'search' => strtolower(($promo->name ?? '').' '.$roomName),
                'booking_period' => dateFormat($promo->book_periode_start).' - '.dateFormat($promo->book_periode_end),
                'stay_period' => dateFormat($promo->periode_start).' - '.dateFormat($promo->periode_end),
                'published_rate' => $this->pricingService->publishedRate($promo->contract_rate, $promo->markup, $this->usdRate, $this->tax),
                'status_tone' => $this->statusToneForPeriod($promo->status, $promo->book_periode_end),
            ];
        });
    }

    public function packageRows(): Collection
    {
        return $this->packages->map(function ($package) {
            $roomName = $package->room?->rooms ?: '-';

            return [
                'model' => $package,
                'room_name' => $roomName,
                'search' => strtolower(($package->name ?? '').' '.$roomName),
                'stay_period' => dateFormat($package->stay_period_start).' - '.dateFormat($package->stay_period_end),
                'published_rate' => $this->pricingService->packagePublishedRate($package, $this->usdRate, $this->tax),
                'status_tone' => $this->statusToneForPeriod($package->status, $package->stay_period_end),
            ];
        });
    }

    public function additionalChargeRows(): Collection
    {
        return $this->additionalCharges->map(function ($charge) {
            return [
                'model' => $charge,
                'mandatory_label' => $charge->mandatory
                    ? dateFormat($charge->mandatory_start).' - '.dateFormat($charge->mandatory_end)
                    : '-',
                'published_rate' => $this->pricingService->publishedRate($charge->contract_rate, $charge->markup, $this->usdRate, $this->tax),
            ];
        });
    }

    private function statusToneForPeriod(?string $status, ?string $endDate): string
    {
        if ($endDate && $endDate < $this->now) {
            return 'expired';
        }

        return strtolower((string) $status) === 'active' ? 'active' : 'draft';
    }
}
