<?php

namespace Tests\Unit\Pricing;

use App\Models\TourPrices;
use App\Services\Tours\TourMarkupResolver;
use App\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

class TourMarkupResolverTest extends TestCase
{
    public function test_usd_markup_is_resolved_to_cents(): void
    {
        $markup = (new TourMarkupResolver())->resolve($this->price('usd', '20.50'));

        $this->assertSame(Money::USD, $markup->currency);
        $this->assertSame(2_050, $markup->amount);
    }

    public function test_idr_markup_is_resolved_to_whole_rupiah(): void
    {
        $markup = (new TourMarkupResolver())->resolve($this->price('idr', '250001.000000'));

        $this->assertSame(Money::IDR, $markup->currency);
        $this->assertSame(250_001, $markup->amount);
    }

    public function test_percentage_markup_is_calculated_from_contract_rate_half_up(): void
    {
        $markup = (new TourMarkupResolver())->resolve($this->price('percentage', '12.500000'));

        $this->assertSame(Money::IDR, $markup->currency);
        $this->assertSame(125_000, $markup->amount);
    }

    private function price(string $type, string $amount): TourPrices
    {
        return (new TourPrices())->forceFill([
            'contract_rate_idr' => 1_000_000,
            'markup_type' => $type,
            'markup_amount' => $amount,
        ]);
    }
}
