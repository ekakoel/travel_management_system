<?php

namespace App\ViewModels\Hotels;

use App\Models\Hotels;
use App\Services\Hotels\HotelPricingService;
use Carbon\Carbon;
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
        private readonly ?Collection $chartNormalPrices = null,
        private readonly ?Collection $chartPromos = null,
        private readonly ?Collection $chartPackages = null,
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

        return [
            ['label' => 'Status', 'value' => $this->status(), 'meta' => 'Current publication state', 'icon' => 'fa fa-check-circle', 'tone' => $this->statusTone() === 'active' ? 'green' : 'amber'],
            ['label' => 'Rooms', 'value' => number_format($this->rooms->count()), 'meta' => "{$activeRooms} active / {$draftRooms} draft", 'icon' => 'fa fa-bed', 'tone' => 'teal'],
            ['label' => 'Latest Price', 'value' => $this->latestPriceDate() ? dateFormat($this->latestPriceDate()) : '-', 'meta' => 'Most recent normal price update', 'icon' => 'fa fa-calendar-check-o', 'tone' => 'green'],
        ];
    }

    public function pricingAgentRateChart(): array
    {
        $yearStart = Carbon::parse($this->now)->startOfYear()->startOfMonth();
        $currentMonthOffset = max(Carbon::parse($this->now)->month - 1, 0);
        $months = collect(range(0, $currentMonthOffset))->map(function (int $offset) use ($yearStart) {
            $month = $yearStart->copy()->addMonths($offset);

            return [
                'label' => $month->format('M'),
                'month' => $month->format('Y-m'),
                'start' => $month->copy()->startOfMonth()->toDateString(),
                'end' => $month->copy()->endOfMonth()->toDateString(),
            ];
        });

        $normalValues = $months->map(function (array $month) {
            $rates = $this->chartNormalPrices()
                ->filter(fn ($price) => $this->periodOverlapsMonth($price->start_date, $price->end_date, $month))
                ->map(function ($price) {
                    $pricing = $this->pricingService->rateBreakdown(
                        $price->contract_rate,
                        $price->markup,
                        $this->usdRate,
                        $this->tax,
                        1,
                        max((int) ($price->kick_back ?? 0), 0)
                    );

                    return (int) ($pricing['net_rate'] ?? 0);
                })
                ->filter(fn (int $rate) => $rate > 0)
                ->values();

            return (int) ($rates->max() ?? 0);
        })->values();

        $promoValues = $months->map(function (array $month) {
            $rates = $this->chartPromos()
                ->filter(fn ($promo) => $this->periodOverlapsMonth($promo->periode_start, $promo->periode_end, $month))
                ->map(function ($promo) {
                    $pricing = $this->pricingService->rateBreakdown(
                        $promo->contract_rate,
                        $promo->markup,
                        $this->usdRate,
                        $this->tax
                    );

                    return (int) ($pricing['net_rate'] ?? 0);
                })
                ->filter(fn (int $rate) => $rate > 0)
                ->values();

            return (int) ($rates->max() ?? 0);
        })->values();

        $packageValues = $months->map(function (array $month) {
            $rates = $this->chartPackages()
                ->filter(fn ($package) => $this->periodOverlapsMonth($package->stay_period_start, $package->stay_period_end, $month))
                ->map(function ($package) {
                    $duration = max((int) ($package->duration ?? 1), 1);
                    $pricing = $this->pricingService->rateBreakdown(
                        $package->contract_rate,
                        $package->markup,
                        $this->usdRate,
                        $this->tax,
                        $duration
                    );

                    return (int) ceil(((int) ($pricing['net_rate'] ?? 0)) / $duration);
                })
                ->filter(fn (int $rate) => $rate > 0)
                ->values();

            return (int) ($rates->max() ?? 0);
        })->values();

        $allValues = $normalValues->merge($promoValues)->merge($packageValues);
        $activeValues = $allValues->filter(fn (int $value) => $value > 0);
        $averageRate = $activeValues->isNotEmpty() ? (int) ceil($activeValues->average()) : 0;
        $currentMonthIndex = max(min(Carbon::parse($this->now)->month - 1, $months->count() - 1), 0);
        $currentRate = (int) ceil(collect([
            $normalValues[$currentMonthIndex] ?? 0,
            $promoValues[$currentMonthIndex] ?? 0,
            $packageValues[$currentMonthIndex] ?? 0,
        ])->filter(fn (int $value) => $value > 0)->average() ?: 0);
        $previousRate = (int) ceil(collect([
            $normalValues[max($currentMonthIndex - 1, 0)] ?? 0,
            $promoValues[max($currentMonthIndex - 1, 0)] ?? 0,
            $packageValues[max($currentMonthIndex - 1, 0)] ?? 0,
        ])->filter(fn (int $value) => $value > 0)->average() ?: 0);
        $deltaPercent = $previousRate > 0
            ? (($currentRate - $previousRate) / $previousRate) * 100
            : ($currentRate > 0 ? 100 : 0);

        return array_merge([
            'title' => 'Pricing Agent Rate',
            'value' => currencyFormatUsd($averageRate),
            'meta' => Carbon::parse($this->now)->format('Y').' year to date highest agent rate',
            'delta' => number_format(abs($deltaPercent), 2).'%',
            'delta_direction' => $deltaPercent < 0 ? 'down' : 'up',
            'delta_label' => 'vs previous month',
            'months' => $months->pluck('label')->all(),
        ], $this->chartGeometry(collect([
            ['key' => 'normal', 'label' => 'Normal Price', 'color' => '#2563eb', 'values' => $normalValues],
            ['key' => 'promo', 'label' => 'Promo Price', 'color' => '#ef4444', 'values' => $promoValues],
            ['key' => 'package', 'label' => 'Package Price', 'color' => '#16a34a', 'values' => $packageValues],
        ]), $months->pluck('label')));
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

        return $this->visibleNormalPrices()
            ->sortBy(fn ($price) => strtolower((string) ($price->rooms?->rooms ?? '')).'|'.$this->normalPriceStayPeriodSortKey($price))
            ->map(function ($price) use ($conflictingIds) {
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
        })->values();
    }

    public function normalPriceGroups(): Collection
    {
        return $this->normalPriceRows()
            ->groupBy(fn ($row) => (string) ($row['model']->rooms_id ?? 'unassigned'))
            ->map(function (Collection $rows) {
                $sortedRows = $rows
                    ->sortBy(fn ($row) => $this->normalPriceStayPeriodSortKey($row['model']))
                    ->values();

                return [
                    'room_name' => $sortedRows->first()['room_name'] ?? '-',
                    'search' => $sortedRows->pluck('search')->implode(' '),
                    'rows' => $sortedRows,
                ];
            })
            ->sortBy([
                fn ($group) => $this->normalPriceStayPeriodSortKey($group['rows']->first()['model']),
                fn ($group) => strtolower((string) $group['room_name']),
            ])
            ->values();
    }

    private function normalPriceStayPeriodSortKey(object $price): string
    {
        return sprintf(
            '%s|%s|%010d',
            filled($price->start_date) ? Carbon::parse($price->start_date)->toDateString() : '9999-12-31',
            filled($price->end_date) ? Carbon::parse($price->end_date)->toDateString() : '9999-12-31',
            (int) ($price->id ?? 0)
        );
    }

    public function extraBedRows(): Collection
    {
        return $this->hotel->extrabeds
            ->sortBy([
                fn ($extraBed) => strtolower((string) ($extraBed->type ?? '')),
                fn ($extraBed) => (int) $extraBed->id,
            ])
            ->map(function ($extraBed) {
                $pricing = $this->pricingService->rateBreakdown(
                    $extraBed->contract_rate,
                    $extraBed->markup,
                    $this->usdRate,
                    $this->tax
                );

                return [
                    'model' => $extraBed,
                    'title' => trim(($extraBed->name ?: 'Extra Bed').' ('.($extraBed->type ?: '-').')'),
                    'age_range' => $this->extraBedAgeRange($extraBed),
                    'description' => trim((string) ($extraBed->description ?: $this->extraBedFallbackDescription($extraBed->type))),
                    'pricing' => $pricing,
                    'published_rate' => $pricing['published_rate'],
                ];
            })
            ->values();
    }

    private function extraBedAgeRange(object $extraBed): string
    {
        $minAge = $extraBed->min_age;
        $maxAge = $extraBed->max_age;

        if ($minAge !== null && $minAge !== '' && $maxAge !== null && $maxAge !== '') {
            return $minAge.' - '.$maxAge.' years';
        }

        if ($minAge !== null && $minAge !== '') {
            return 'From '.$minAge.' years';
        }

        if ($maxAge !== null && $maxAge !== '') {
            return 'Up to '.$maxAge.' years';
        }

        return '-';
    }

    private function extraBedFallbackDescription(?string $type): string
    {
        return match ($type) {
            'Adult' => 'Adult extra bed option.',
            'Children' => 'Child extra bed option.',
            default => 'Guest extra bed option.',
        };
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
        return $this->visiblePromos()
            ->sortBy(fn ($promo) => strtolower((string) ($promo->rooms?->rooms ?? '')).'|'.$this->promoStayPeriodSortKey($promo))
            ->map(function ($promo) {
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
        })->values();
    }

    public function promoGroups(): Collection
    {
        return $this->promoRows()
            ->groupBy(fn ($row) => (string) ($row['model']->rooms_id ?? 'unassigned'))
            ->map(function (Collection $rows) {
                $sortedRows = $rows
                    ->sortBy(fn ($row) => $this->promoStayPeriodSortKey($row['model']))
                    ->values();

                return [
                    'room_name' => $sortedRows->first()['room_name'] ?? '-',
                    'search' => $sortedRows->pluck('search')->implode(' '),
                    'rows' => $sortedRows,
                ];
            })
            ->sortBy([
                fn ($group) => $this->promoStayPeriodSortKey($group['rows']->first()['model']),
                fn ($group) => strtolower((string) $group['room_name']),
            ])
            ->values();
    }

    private function promoStayPeriodSortKey(object $promo): string
    {
        return sprintf(
            '%s|%s|%s|%010d',
            filled($promo->periode_start) ? Carbon::parse($promo->periode_start)->toDateString() : '9999-12-31',
            filled($promo->periode_end) ? Carbon::parse($promo->periode_end)->toDateString() : '9999-12-31',
            strtolower((string) ($promo->name ?? '')),
            (int) ($promo->id ?? 0)
        );
    }

    public function packageRows(): Collection
    {
        return $this->visiblePackages()
            ->sortBy(fn ($package) => strtolower((string) ($package->room?->rooms ?? '')).'|'.$this->packageStayPeriodSortKey($package))
            ->map(function ($package) {
            $roomName = $package->room?->rooms ?: '-';
            $duration = max((int) ($package->duration ?? 1), 1);
            $pricing = $this->pricingService->rateBreakdown(
                $package->contract_rate,
                $package->markup,
                $this->usdRate,
                $this->tax,
                $duration
            );
            $perNightPricing = $this->packagePerNightPricing($pricing, $duration);

            return [
                'model' => $package,
                'room_name' => $roomName,
                'search' => strtolower(($package->name ?? '').' '.$roomName),
                'stay_period' => dateFormat($package->stay_period_start).' - '.dateFormat($package->stay_period_end),
                'pricing' => $perNightPricing,
                'package_total_pricing' => $pricing,
                'published_rate' => $perNightPricing['published_rate'],
                'package_total_rate' => $pricing['published_rate'],
                'status_tone' => $this->statusToneForPeriod($package->status, $package->stay_period_end),
            ];
        })->values();
    }

    public function packageGroups(): Collection
    {
        return $this->packageRows()
            ->groupBy(fn ($row) => (string) ($row['model']->rooms_id ?? 'unassigned'))
            ->map(function (Collection $rows) {
                $sortedRows = $rows
                    ->sortBy(fn ($row) => $this->packageStayPeriodSortKey($row['model']))
                    ->values();

                return [
                    'room_name' => $sortedRows->first()['room_name'] ?? '-',
                    'search' => $sortedRows->pluck('search')->implode(' '),
                    'rows' => $sortedRows,
                ];
            })
            ->sortBy([
                fn ($group) => $this->packageStayPeriodSortKey($group['rows']->first()['model']),
                fn ($group) => strtolower((string) $group['room_name']),
            ])
            ->values();
    }

    private function packageStayPeriodSortKey(object $package): string
    {
        return sprintf(
            '%s|%s|%s|%010d',
            filled($package->stay_period_start) ? Carbon::parse($package->stay_period_start)->toDateString() : '9999-12-31',
            filled($package->stay_period_end) ? Carbon::parse($package->stay_period_end)->toDateString() : '9999-12-31',
            strtolower((string) ($package->name ?? '')),
            (int) ($package->id ?? 0)
        );
    }

    private function packagePerNightPricing(array $pricing, int $duration): array
    {
        $effectiveDuration = max($duration, 1);
        $perNightPricing = $pricing;

        foreach ([
            'effective_contract_rate_idr',
            'contract_rate_usd',
            'markup_usd',
            'markup_idr',
            'subtotal_usd',
            'tax_usd',
            'tax_idr',
            'published_rate',
            'published_rate_idr',
            'kick_back_usd',
            'kick_back_idr',
            'net_rate',
            'net_rate_idr',
        ] as $field) {
            $perNightPricing[$field] = (int) ceil(((float) ($pricing[$field] ?? 0)) / $effectiveDuration);
        }

        $perNightPricing['display_mode'] = 'package_per_night';
        $perNightPricing['package_duration'] = $effectiveDuration;
        $perNightPricing['package_total_published_rate'] = $pricing['published_rate'] ?? 0;
        $perNightPricing['package_total_published_rate_idr'] = $pricing['published_rate_idr'] ?? 0;
        $perNightPricing['package_total_net_rate'] = $pricing['net_rate'] ?? ($pricing['published_rate'] ?? 0);
        $perNightPricing['package_total_net_rate_idr'] = $pricing['net_rate_idr'] ?? ($pricing['published_rate_idr'] ?? 0);

        return $perNightPricing;
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

    private function chartGeometry(Collection $series, Collection $monthLabels): array
    {
        $width = 320;
        $height = 126;
        $left = 42;
        $right = 304;
        $top = 16;
        $bottom = 88;
        $maxValue = max((int) $series->flatMap(fn (array $item) => $item['values'])->max(), 1);
        $roundedMax = (int) ceil($maxValue / 50) * 50;
        $count = max((int) ($series->first()['values']->count() ?? 0), 1);
        $step = $count > 1 ? ($right - $left) / ($count - 1) : 0;

        return [
            'view_box' => "0 0 {$width} {$height}",
            'series' => $series
                ->map(fn (array $item) => array_merge($item, $this->chartSeriesGeometry($item['values'], $roundedMax, $left, $top, $bottom, $step)))
                ->all(),
            'grid_lines' => [$top, ($top + $bottom) / 2, $bottom],
            'scale_labels' => [
                ['y' => $top, 'label' => currencyFormatUsd($roundedMax)],
                ['y' => ($top + $bottom) / 2, 'label' => currencyFormatUsd((int) ceil($roundedMax / 2))],
                ['y' => $bottom, 'label' => '$0'],
            ],
            'month_labels' => $monthLabels
                ->values()
                ->map(fn (string $label, int $index) => [
                    'label' => $label,
                    'x' => round($left + ($step * $index), 2),
                ])
                ->all(),
            'baseline' => $bottom,
        ];
    }

    private function chartSeriesGeometry(Collection $values, int $maxValue, int $left, int $top, int $bottom, float $step): array
    {
        $points = $values->values()->map(function (int $value, int $index) use ($left, $top, $bottom, $maxValue, $step) {
            $x = $left + ($step * $index);
            $y = $bottom - (($value / max($maxValue, 1)) * ($bottom - $top));

            return [
                'x' => round($x, 2),
                'y' => round($y, 2),
                'value' => $value,
                'value_label' => currencyFormatUsd($value),
            ];
        });

        $firstPoint = $points->first() ?? ['x' => $left, 'y' => $bottom];
        $linePath = 'M'.$firstPoint['x'].' '.$firstPoint['y'];

        $pointList = $points->values();
        for ($index = 1; $index < $pointList->count(); $index++) {
            $previous = $pointList[$index - 1];
            $current = $pointList[$index];
            $controlOffset = ($current['x'] - $previous['x']) / 2;
            $linePath .= ' C'.round($previous['x'] + $controlOffset, 2).' '.$previous['y']
                .' '.round($current['x'] - $controlOffset, 2).' '.$current['y']
                .' '.$current['x'].' '.$current['y'];
        }

        return [
            'line_path' => $linePath,
            'points' => $points->all(),
        ];
    }

    private function periodOverlapsMonth(mixed $startDate, mixed $endDate, array $month): bool
    {
        if (!filled($startDate) || !filled($endDate)) {
            return false;
        }

        $periodStart = Carbon::parse($startDate)->toDateString();
        $periodEnd = Carbon::parse($endDate)->toDateString();

        return $periodStart <= $month['end'] && $periodEnd >= $month['start'];
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
            ->reject(fn ($promo) => $this->hasExpired($promo->book_periode_end))
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

    private function chartNormalPrices(): Collection
    {
        return $this->chartNormalPrices ?? $this->normalPrices;
    }

    private function chartPromos(): Collection
    {
        return $this->chartPromos ?? $this->promos;
    }

    private function chartPackages(): Collection
    {
        return $this->chartPackages ?? $this->packages;
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
