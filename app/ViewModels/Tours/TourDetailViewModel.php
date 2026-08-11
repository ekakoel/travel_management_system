<?php

namespace App\ViewModels\Tours;

use App\Models\Tours;
use App\Services\Tours\TourPackagePricingService;
use App\Support\MoneyFormatter;
use App\ValueObjects\Money;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class TourDetailViewModel
{
    public function __construct(
        public readonly Tours $tour,
        public readonly Collection $actionLogs,
        private readonly TourPackagePricingService $pricingService,
        private readonly MoneyFormatter $formatter,
        private readonly CarbonInterface $serviceDate,
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
            ['label' => 'Pricing', 'value' => $this->priceRows()->contains('price_available', true) ? 'Ready' : 'Unavailable', 'meta' => 'Authoritative pricing readiness', 'icon' => 'fa fa-money', 'tone' => 'amber'],
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
        $quotes = $this->pricingService->quoteEachTier($this->tour, $this->serviceDate)
            ->keyBy(fn (array $tier) => $tier['price']->id);

        return $this->tour->prices
            ->reject(fn ($price) => $price->trashed())
            ->map(function ($price) use ($quotes) {
                $capacity = $price->min_qty . ' - ' . $price->max_qty . ' Guests';
                $quote = $quotes->get($price->id)['quote'] ?? null;
                $data = $quote?->toArray();
                $displayStatus = $this->displayPricingStatus($price);

                return [
                    'model' => $price,
                    'capacity' => $capacity,
                    'status_tone' => match ($displayStatus) {
                        'Valid' => 'active',
                        'Expired' => 'muted',
                        'Scheduled' => 'draft',
                        'Invalid' => 'danger',
                        default => 'draft',
                    },
                    'display_status' => $displayStatus,
                    'needs_review' => $displayStatus === 'Invalid',
                    'markup_type' => match ($price->resolvedMarkupType()) {
                        'usd' => 'USD',
                        'idr' => 'IDR',
                        'percentage' => 'Percentage',
                        default => '-',
                    },
                    'markup_display' => $this->markupDisplay($price),
                    'price_available' => $quote !== null,
                    'quoteable_status' => $quote !== null ? 'Quoteable' : 'Not quoteable',
                    'published_rate' => $quote
                        ? $this->formatter->decimal(Money::usdCents($quote->unitPriceUsdMinor()))
                        : null,
                    'contract_rate_usd' => $data
                        ? $this->formatter->decimal(Money::usdCents($data['contract_rate_usd_minor']))
                        : null,
                    'tax_amount' => $data
                        ? $this->formatter->decimal(Money::usdCents($data['tax_amount_usd_minor']))
                        : null,
                ];
            })->values();
    }

    private function displayPricingStatus($price): string
    {
        if (! $price->hasCompleteConfiguration()) {
            return 'Invalid';
        }

        if ($price->valid_from->gt($this->serviceDate->toDateString())) {
            return 'Scheduled';
        }

        if ($price->valid_until->lt($this->serviceDate->toDateString())) {
            return 'Expired';
        }

        return 'Valid';
    }

    private function markupDisplay($price): string
    {
        return match ($price->resolvedMarkupType()) {
            'percentage' => number_format((float) $price->markup_amount, 2, '.', ',').'%',
            'usd' => 'USD '.number_format((float) $price->markup_amount, 2, '.', ','),
            'idr' => 'IDR '.number_format((float) $price->markup_amount, 0, '.', ','),
            default => '-',
        };
    }
}
