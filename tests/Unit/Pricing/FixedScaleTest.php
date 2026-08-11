<?php

namespace Tests\Unit\Pricing;

use App\Exceptions\PricingException;
use App\Support\Pricing\FixedScale;
use PHPUnit\Framework\TestCase;

class FixedScaleTest extends TestCase
{
    public function test_it_parses_fixed_scale_decimal_without_float(): void
    {
        $this->assertSame(18_077_043_000, FixedScale::parseDecimal('18077.043', 1_000_000));
        $this->assertSame(2_000, FixedScale::parseDecimal('20.00', 100));
        $this->assertSame(1_500_000, FixedScale::parseDecimal('1.5', 1_000_000));
    }

    public function test_it_rounds_multiply_divide_half_up(): void
    {
        $this->assertSame(0, FixedScale::multiplyDivideHalfUp(1, 49, 100));
        $this->assertSame(1, FixedScale::multiplyDivideHalfUp(1, 50, 100));
        $this->assertSame(1, FixedScale::multiplyDivideHalfUp(1, 51, 100));
    }

    public function test_it_rounds_multiply_divide_up_to_the_next_integer(): void
    {
        $this->assertSame(1, FixedScale::multiplyDivideUp(1, 1, 100));
        $this->assertSame(1, FixedScale::multiplyDivideUp(1, 100, 100));
        $this->assertSame(2, FixedScale::multiplyDivideUp(1, 101, 100));
    }

    public function test_it_rejects_malformed_decimal_and_overflow(): void
    {
        foreach ([' 1.00', '1e3', '-1', '1,000', 'abc'] as $value) {
            try {
                FixedScale::parseDecimal($value, 100);
                $this->fail("Expected {$value} to be rejected.");
            } catch (PricingException) {
                $this->addToAssertionCount(1);
            }
        }

        $this->expectException(PricingException::class);
        FixedScale::checkedMultiply(PHP_INT_MAX, 2);
    }
}
