<?php

namespace App\Services\Tours;

use App\Data\Pricing\PricingQuote;
use App\Exceptions\PricingException;
use App\Models\BookingCode;
use App\Models\Orders;
use App\Models\Promotion;
use App\Models\TourPrices;
use App\Models\Tours;
use App\Services\Pricing\CurrencyRateResolver;
use App\Services\Pricing\PricingEngine;
use App\Services\Pricing\TaxResolver;
use App\Support\Pricing\FixedScale;
use App\ValueObjects\Money;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

final class TourPackagePricingService
{
    public function __construct(
        private readonly PricingEngine $engine,
        private readonly CurrencyRateResolver $rateResolver,
        private readonly TaxResolver $taxResolver,
        private readonly TourMarkupResolver $markupResolver,
    ) {
    }

    public function eligiblePrices(
        Tours $tour,
        CarbonInterface $serviceDate,
        bool $lockForUpdate = false,
    ): Collection
    {
        if ($tour->status !== 'Active' || $tour->trashed()) {
            return collect();
        }

        $query = $tour->prices()
            ->readyForTravel($serviceDate->toDateString())
            ->orderBy('min_qty')
            ->orderBy('max_qty');

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        return $query->get();
    }

    public function resolveEligiblePrice(
        Tours $tour,
        int $guestCount,
        CarbonInterface $serviceDate,
        ?int $preferredPriceId = null,
        bool $lockForUpdate = false,
    ): TourPrices {
        if ($guestCount <= 0) {
            throw PricingException::unavailable('PRICING_PAX_TIER_NOT_FOUND');
        }

        $prices = $this->eligiblePrices($tour, $serviceDate, $lockForUpdate)
            ->filter(fn (TourPrices $price) => $guestCount >= (int) $price->min_qty
                && $guestCount <= (int) $price->max_qty)
            ->values();

        if ($prices->isEmpty()) {
            throw PricingException::unavailable('PRICING_PAX_TIER_NOT_FOUND');
        }

        if ($prices->count() !== 1) {
            throw PricingException::unavailable('PRICING_PAX_TIER_AMBIGUOUS');
        }

        $price = $prices->first();

        if ($preferredPriceId !== null && (int) $price->id !== $preferredPriceId) {
            throw PricingException::unavailable('PRICING_PAX_TIER_NOT_FOUND');
        }

        return $price;
    }

    public function quote(
        Tours $tour,
        int $guestCount,
        CarbonInterface $serviceDate,
        ?int $preferredPriceId = null,
        ?int $promotionId = null,
        ?string $bookingCode = null,
        ?int $actorId = null,
        bool $lockForUpdate = false,
        ?int $existingOrderId = null,
    ): PricingQuote {
        $price = $this->resolveEligiblePrice(
            $tour,
            $guestCount,
            $serviceDate,
            $preferredPriceId,
            $lockForUpdate
        );
        $calculatedAt = CarbonImmutable::now();
        $rate = $this->rateResolver->resolveUsdSell($calculatedAt);
        $tax = $this->taxResolver->resolve('Tour Package', $calculatedAt);
        $markup = $this->markupResolver->resolve($price);
        $discountCandidates = $this->discountCandidates(
            $promotionId,
            $bookingCode,
            $actorId,
            $calculatedAt,
            $lockForUpdate,
            $existingOrderId
        );

        return $this->engine->calculate(
            service: 'Tour Package',
            serviceId: (int) $tour->id,
            priceId: (int) $price->id,
            contractRateIdr: (int) $price->contract_rate_idr,
            markup: $markup,
            quantity: $guestCount,
            rate: $rate,
            tax: $tax,
            discountCandidates: $discountCandidates,
            calculatedAt: $calculatedAt,
            context: [
                'service_date' => $serviceDate->toDateString(),
                'promotion_id' => $promotionId,
                'booking_code' => $bookingCode,
                'markup_type' => $price->resolvedMarkupType(),
                'markup_input_amount' => (string) $price->markup_amount,
            ],
        );
    }

    public function quoteEachTier(
        Tours $tour,
        CarbonInterface $serviceDate,
        ?int $promotionId = null,
        ?string $bookingCode = null,
        ?int $actorId = null,
    ): Collection {
        return $this->quoteEachTierReport(
            $tour,
            $serviceDate,
            $promotionId,
            $bookingCode,
            $actorId,
        )['quotes'];
    }

    public function quoteEachTierReport(
        Tours $tour,
        CarbonInterface $serviceDate,
        ?int $promotionId = null,
        ?string $bookingCode = null,
        ?int $actorId = null,
    ): array {
        $eligiblePrices = $this->eligiblePrices($tour, $serviceDate);
        $failureCodes = collect();

        foreach ([
            fn () => $this->rateResolver->resolveUsdSell(CarbonImmutable::now()),
            fn () => $this->taxResolver->resolve('Tour Package', CarbonImmutable::now()),
        ] as $resolveRequirement) {
            try {
                $resolveRequirement();
            } catch (PricingException $exception) {
                $failureCodes->push($exception->pricingCode);
            }
        }

        $quotes = $eligiblePrices
            ->map(function (TourPrices $price) use (
                $tour,
                $serviceDate,
                $promotionId,
                $bookingCode,
                $actorId,
                $failureCodes,
            ) {
                try {
                    return [
                        'price' => $price,
                        'quote' => $this->quote(
                            $tour,
                            (int) $price->min_qty,
                            $serviceDate,
                            (int) $price->id,
                            $promotionId,
                            $bookingCode,
                            $actorId
                        ),
                    ];
                } catch (PricingException $exception) {
                    $failureCodes->push($exception->pricingCode);

                    return null;
                }
            })
            ->filter()
            ->values();

        return [
            'quotes' => $quotes,
            'eligible_prices' => $eligiblePrices->values(),
            'failure_codes' => $failureCodes->unique()->values(),
        ];
    }

    private function discountCandidates(
        ?int $promotionId,
        ?string $bookingCode,
        ?int $actorId,
        CarbonImmutable $calculatedAt,
        bool $lockForUpdate,
        ?int $existingOrderId = null,
    ): array {
        $candidates = [];

        $promotionQuery = Promotion::query()
                ->where('status', 'Active')
                ->where('pricing_data_status', 'ready')
                ->where('service_scope', 'Tour Package')
                ->where(function ($query) use ($calculatedAt) {
                    $query->whereNull('valid_from')->orWhere('valid_from', '<=', $calculatedAt);
                })
                ->where(function ($query) use ($calculatedAt) {
                    $query->whereNull('valid_until')->orWhere('valid_until', '>=', $calculatedAt);
                });

        if ($promotionId !== null) {
            $promotionQuery->whereKey($promotionId);
        }

        if ($lockForUpdate) {
            $promotionQuery->lockForUpdate();
        }

        $promotions = $promotionQuery->get();

        if ($promotionId !== null && $promotions->isEmpty()) {
            throw PricingException::unavailable('PRICING_PROMOTION_INVALID');
        }

        $promotions->each(function (Promotion $promotion) use (&$candidates) {
            $candidates[] = $this->discountCandidate(
                    'promotion',
                    (string) $promotion->id,
                    (string) $promotion->discount_type,
                    (string) $promotion->discount_value,
                    $promotion->discount_currency
            );
        });

        if ($bookingCode !== null && trim($bookingCode) !== '') {
            $bookingCode = trim($bookingCode);
            $bookingCodeQuery = BookingCode::query()
                ->where('code', $bookingCode)
                ->where('status', 'Active')
                ->where('pricing_data_status', 'ready')
                ->where('service_scope', 'Tour Package')
                ->when($existingOrderId === null, fn ($query) => $query->whereColumn('used', '<', 'amount'))
                ->where(function ($query) use ($calculatedAt) {
                    $query->whereNull('valid_from')->orWhere('valid_from', '<=', $calculatedAt);
                })
                ->where(function ($query) use ($calculatedAt) {
                    $query->whereNull('valid_until')->orWhere('valid_until', '>=', $calculatedAt);
                });

            if ($lockForUpdate) {
                $bookingCodeQuery->lockForUpdate();
            }

            $code = $bookingCodeQuery->first();

            $alreadyUsed = $actorId !== null && Orders::query()
                ->where('user_id', $actorId)
                ->where('bookingcode', $bookingCode)
                ->when($existingOrderId, fn ($query) => $query->where('id', '!=', $existingOrderId))
                ->exists();

            if ($code === null || $alreadyUsed) {
                throw PricingException::unavailable('PRICING_BOOKING_CODE_INVALID');
            }

            $candidates[] = $this->discountCandidate(
                'booking_code',
                (string) $code->code,
                (string) $code->discount_type,
                (string) $code->discount_value,
                $code->discount_currency
            );
        }

        return $candidates;
    }

    private function discountCandidate(
        string $source,
        string $identifier,
        string $type,
        string $value,
        ?string $currency,
    ): array {
        if ($type === 'fixed') {
            if (!in_array($currency, [Money::IDR, Money::USD], true)) {
                throw PricingException::unavailable('PRICING_DISCOUNT_INVALID');
            }

            return [
                'source' => $source,
                'identifier' => $identifier,
                'type' => 'fixed',
                'currency' => $currency,
                'amount_minor' => FixedScale::parseDecimal(
                    $value,
                    $currency === Money::USD ? 100 : 1
                ),
            ];
        }

        if ($type === 'percentage') {
            return [
                'source' => $source,
                'identifier' => $identifier,
                'type' => 'percentage',
                'percentage_scaled' => FixedScale::parseDecimal(
                    $value,
                    FixedScale::PERCENTAGE_SCALE
                ),
            ];
        }

        throw PricingException::unavailable('PRICING_DISCOUNT_INVALID');
    }
}
