<?php

namespace Tests\Unit\Pricing;

use App\Data\Pricing\ResolvedCurrencyRate;
use App\Data\Pricing\ResolvedTaxPolicy;
use App\Services\Pricing\PricingEngine;
use App\Support\Pricing\FixedScale;
use App\ValueObjects\Money;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PricingEngineTest extends TestCase
{
    public function test_required_pricing_example(): void
    {
        $quote = (new PricingEngine())->calculate(
            service: 'Tour Package',
            serviceId: 1,
            priceId: 70,
            contractRateIdr: 1_000_000,
            markup: Money::usdCents(2_000),
            quantity: 1,
            rate: $this->rate('16000'),
            tax: $this->tax('10'),
            calculatedAt: CarbonImmutable::parse('2026-07-29 12:00:00'),
        );

        $this->assertSame(320_000, $quote->data['markup_idr']);
        $this->assertSame(1_320_000, $quote->data['subtotal_idr']);
        $this->assertSame(132_000, $quote->data['tax_amount_idr']);
        $this->assertSame(1_452_000, $quote->unitPriceIdr());
        $this->assertSame(9_075, $quote->unitPriceUsdMinor());
    }

    public function test_it_multiplies_quantity_and_selects_largest_discount(): void
    {
        $quote = (new PricingEngine())->calculate(
            service: 'Tour Package',
            serviceId: 1,
            priceId: 1,
            contractRateIdr: 1_000_000,
            markup: Money::usdCents(2_000),
            quantity: 2,
            rate: $this->rate('16000'),
            tax: $this->tax('10'),
            discountCandidates: [
                [
                    'source' => 'promotion',
                    'identifier' => '10',
                    'type' => 'percentage',
                    'percentage_scaled' => FixedScale::parseDecimal('5', FixedScale::PERCENTAGE_SCALE),
                ],
                [
                    'source' => 'booking_code',
                    'identifier' => 'SAVE',
                    'type' => 'fixed',
                    'currency' => Money::IDR,
                    'amount_minor' => 200_000,
                ],
            ],
            calculatedAt: CarbonImmutable::parse('2026-07-29 12:00:00'),
        );

        $this->assertSame(2_904_000, $quote->data['gross_total_idr']);
        $this->assertSame(200_000, $quote->data['discount_total_idr']);
        $this->assertSame('booking_code', $quote->data['selected_discount']['source']);
        $this->assertSame(2_704_000, $quote->finalTotalIdr());
    }

    public function test_idr_markup_is_used_without_currency_round_trip(): void
    {
        $quote = (new PricingEngine())->calculate(
            service: 'Tour Package',
            serviceId: 1,
            priceId: 70,
            contractRateIdr: 1_000_000,
            markup: Money::idr(125_001),
            quantity: 1,
            rate: $this->rate('16000'),
            tax: $this->tax('10'),
            calculatedAt: CarbonImmutable::parse('2026-07-29 12:00:00'),
        );

        $this->assertSame('tour-package-v2', $quote->data['pricing_version']);
        $this->assertSame(Money::IDR, $quote->data['markup_currency']);
        $this->assertSame(125_001, $quote->data['markup_idr']);
        $this->assertSame(1_237_501, $quote->unitPriceIdr());
    }

    private function rate(string $value): ResolvedCurrencyRate
    {
        return new ResolvedCurrencyRate(
            id: 1,
            pair: 'USD/IDR',
            side: 'sell',
            valueScaled: FixedScale::parseDecimal($value, FixedScale::FX_SCALE),
            scale: FixedScale::FX_SCALE,
            source: 'fixture',
            retrievedAt: new DateTimeImmutable('2026-07-29 11:00:00'),
            maxAgeSeconds: 86_400,
            ageSeconds: 3_600,
        );
    }

    private function tax(string $percentage): ResolvedTaxPolicy
    {
        return new ResolvedTaxPolicy(
            id: 1,
            name: 'Tour Tax',
            service: 'Tour Package',
            percentageScaled: FixedScale::parseDecimal($percentage, FixedScale::PERCENTAGE_SCALE),
            percentageScale: FixedScale::PERCENTAGE_SCALE,
            calculationType: 'exclusive',
            taxableBase: 'contract_plus_markup',
            effectiveFrom: new DateTimeImmutable('2026-07-29 00:00:00'),
            effectiveUntil: null,
        );
    }
}
