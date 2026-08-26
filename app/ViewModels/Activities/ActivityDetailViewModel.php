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

    public function taxAmountIdr(): ?int
    {
        return $this->quote()?->taxAmountIdr();
    }

    public function taxPercentage(): ?string
    {
        return $this->quote()?->taxPercentage();
    }

    public function publishedRate(): ?string
    {
        return $this->quote()?->unitPriceUsd();
    }

    public function sellingPrice(): ?string
    {
        return $this->publishedRate();
    }

    public function sellingPriceIdr(): ?int
    {
        return $this->quote()?->unitPriceIdr();
    }

    public function contractRateUsd(): ?string
    {
        return $this->quote()?->contractRateUsd();
    }

    public function contractRateIdr(): ?int
    {
        return $this->quote()?->contractRateIdr();
    }

    public function markupUsd(): ?string
    {
        return $this->quote()?->markupUsd();
    }

    public function markupIdr(): ?int
    {
        return $this->quote()?->markupIdr();
    }

    public function dualCurrencyPrice(?string $usdAmount, ?int $idrAmount): string
    {
        if ($usdAmount === null || $idrAmount === null) {
            return __('messages.Price cannot be calculated.');
        }

        return currencyFormatUsd($usdAmount).' / '.currencyFormatIdr($idrAmount);
    }

    public function pricingUnavailableCode(): ?string
    {
        $this->quote();

        return $this->pricingErrorCode;
    }

    public function pricingUnavailableMessage(): string
    {
        $code = $this->pricingUnavailableCode();

        return self::pricingUnavailableMessageFor($code);
    }

    public static function pricingUnavailableMessageFor(?string $code): string
    {
        return match ($code) {
            'MISSING_CONTRACT_RATE' => __('messages.Missing Contract Rate.'),
            'MISSING_MARKUP' => __('messages.Missing Markup.'),
            'MISSING_TAX' => __('messages.Tax configuration is not available.'),
            'MISSING_USD_RATE' => __('messages.USD Rate is not available.'),
            'MISSING_VALID_UNTIL' => __('messages.Valid Until has not been configured.'),
            'ACTIVITY_PRICE_DATE_OUT_OF_VALIDITY' => __('messages.The selected activity date is outside the current price validity period.'),
            'ACTIVITY_PAX_INVALID' => __('messages.Number of guests is outside the Activity pax rules.'),
            default => __('messages.Activity pricing is not available.'),
        };
    }

    public function stats(): array
    {
        return [
            [
                'label' => 'Calculated Price',
                'value' => $this->priceAvailable()
                    ? $this->dualCurrencyPrice($this->sellingPrice(), $this->sellingPriceIdr())
                    : __('messages.Price cannot be calculated.'),
                'meta' => $this->priceAvailable()
                    ? 'Current calculated selling price per pax'
                    : $this->pricingUnavailableMessage(),
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

    public function translationGroups(): array
    {
        return [
            [
                'title' => 'Description',
                'description' => 'Short overview displayed on the public Activity page.',
                'fields' => [
                    ['label' => 'English', 'content' => $this->activity->description],
                    ['label' => 'Traditional Chinese', 'content' => $this->activity->description_traditional],
                    ['label' => 'Simplified Chinese', 'content' => $this->activity->description_simplified],
                ],
            ],
            [
                'title' => 'Itinerary',
                'description' => 'Sequence or schedule shown to customers before booking.',
                'fields' => [
                    ['label' => 'English', 'content' => $this->activity->itinerary],
                    ['label' => 'Traditional Chinese', 'content' => $this->activity->itinerary_traditional],
                    ['label' => 'Simplified Chinese', 'content' => $this->activity->itinerary_simplified],
                ],
            ],
            [
                'title' => 'Include',
                'description' => 'Services and benefits included in this Activity.',
                'fields' => [
                    ['label' => 'English', 'content' => $this->activity->include],
                    ['label' => 'Traditional Chinese', 'content' => $this->activity->include_traditional],
                    ['label' => 'Simplified Chinese', 'content' => $this->activity->include_simplified],
                ],
            ],
            [
                'title' => 'Cancellation Policy',
                'description' => 'Cancellation conditions shown before customers place an order.',
                'fields' => [
                    ['label' => 'English', 'content' => $this->activity->cancellation_policy],
                    ['label' => 'Traditional Chinese', 'content' => $this->activity->cancellation_policy_traditional],
                    ['label' => 'Simplified Chinese', 'content' => $this->activity->cancellation_policy_simplified],
                ],
            ],
            [
                'title' => 'Additional Information',
                'description' => 'Extra customer-facing notes, restrictions, or preparation details.',
                'fields' => [
                    ['label' => 'English', 'content' => $this->activity->additional_info],
                    ['label' => 'Traditional Chinese', 'content' => $this->activity->additional_info_traditional],
                    ['label' => 'Simplified Chinese', 'content' => $this->activity->additional_info_simplified],
                ],
            ],
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
