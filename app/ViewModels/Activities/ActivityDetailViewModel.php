<?php

namespace App\ViewModels\Activities;

use App\Data\Pricing\ActivityPricingQuote;
use App\Exceptions\PricingException;
use App\Models\Activities;
use App\Services\Activities\ActivityPricingService;

class ActivityDetailViewModel
{
    private bool $pricingResolved = false;

    private ?ActivityPricingQuote $pricingQuote = null;

    private ?string $pricingErrorCode = null;

    public function __construct(
        public readonly Activities $activity,
        public readonly object|null $partner,
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

    public function priceAvailable(): bool
    {
        return $this->quote() !== null;
    }

    public function taxAmount(): ?string
    {
        return $this->quote()?->taxAmountUsd();
    }

    public function taxPercentage(): ?string
    {
        return $this->quote()?->taxPercentage();
    }

    public function publishedRate(): ?string
    {
        return $this->quote()?->unitPriceUsd();
    }

    public function pricingUnavailableCode(): ?string
    {
        $this->quote();

        return $this->pricingErrorCode;
    }

    public function stats(): array
    {
        return [
            [
                'label' => 'Published Rate',
                'value' => $this->priceAvailable() ? currencyFormatUsd($this->publishedRate()) : 'Unavailable',
                'meta' => $this->priceAvailable()
                    ? 'Current published price per pax'
                    : 'Pricing requirements are not met',
                'icon' => 'fa fa-usd',
                'tone' => 'blue',
            ],
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

    private function quote(): ?ActivityPricingQuote
    {
        if ($this->pricingResolved) {
            return $this->pricingQuote;
        }

        $this->pricingResolved = true;

        try {
            return $this->pricingQuote = $this->pricingService->quote(
                $this->activity,
                max((int) ($this->activity->min_pax ?: 1), 1),
            );
        } catch (PricingException $exception) {
            $this->pricingErrorCode = $exception->pricingCode;

            return null;
        }
    }
}
