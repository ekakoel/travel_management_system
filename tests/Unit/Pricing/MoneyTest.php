<?php

namespace Tests\Unit\Pricing;

use App\Exceptions\PricingException;
use App\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_money_uses_integer_minor_units(): void
    {
        $this->assertSame(1_000_000, Money::idr(1_000_000)->amount);
        $this->assertSame(9_075, Money::usdCents(9_075)->amount);
        $this->assertSame(2_000, Money::usdCents(1_000)->add(Money::usdCents(1_000))->amount);
    }

    public function test_money_rejects_currency_mismatch(): void
    {
        $this->expectException(PricingException::class);
        Money::idr(1)->add(Money::usdCents(1));
    }
}
