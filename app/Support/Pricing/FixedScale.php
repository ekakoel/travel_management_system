<?php

namespace App\Support\Pricing;

use App\Exceptions\PricingException;

final class FixedScale
{
    public const FX_SCALE = 1_000_000;
    public const PERCENTAGE_SCALE = 1_000_000;
    public const PERCENTAGE_DENOMINATOR = 100 * self::PERCENTAGE_SCALE;

    public static function parseDecimal(string|int $value, int $scale): int
    {
        if ($scale < 1 || !self::isPowerOfTen($scale)) {
            throw PricingException::arithmetic('Unsupported fixed scale.', [
                'scale' => $scale,
            ]);
        }

        $value = (string) $value;

        if ($value === '' || trim($value) !== $value || !preg_match('/^\d+(?:\.\d+)?$/', $value)) {
            throw PricingException::arithmetic('Malformed decimal value.');
        }

        $decimalPlaces = strlen((string) $scale) - 1;
        [$whole, $fraction] = array_pad(explode('.', $value, 2), 2, '');

        if (strlen($fraction) > $decimalPlaces) {
            $discarded = substr($fraction, $decimalPlaces);

            if (trim($discarded, '0') !== '') {
                throw PricingException::arithmetic('Decimal value exceeds supported scale.', [
                    'scale' => $scale,
                ]);
            }

            $fraction = substr($fraction, 0, $decimalPlaces);
        }

        $wholeAmount = self::checkedMultiply(self::parseUnsignedInteger($whole), $scale);
        $fractionAmount = $fraction === ''
            ? 0
            : self::parseUnsignedInteger(str_pad($fraction, $decimalPlaces, '0'));

        return self::checkedAdd($wholeAmount, $fractionAmount);
    }

    public static function formatDecimal(int $value, int $scale): string
    {
        if ($value < 0 || $scale < 1 || !self::isPowerOfTen($scale)) {
            throw PricingException::arithmetic('Invalid fixed-scale value.');
        }

        $decimalPlaces = strlen((string) $scale) - 1;

        if ($decimalPlaces === 0) {
            return (string) $value;
        }

        return intdiv($value, $scale)
            .'.'
            .str_pad((string) ($value % $scale), $decimalPlaces, '0', STR_PAD_LEFT);
    }

    public static function checkedAdd(int ...$values): int
    {
        $result = 0;

        foreach ($values as $value) {
            if ($value < 0 || $result > PHP_INT_MAX - $value) {
                throw PricingException::overflow();
            }

            $result += $value;
        }

        return $result;
    }

    public static function checkedSubtractNonNegative(int $left, int $right): int
    {
        if ($left < 0 || $right < 0) {
            throw PricingException::arithmetic('Pricing amounts cannot be negative.');
        }

        return $right >= $left ? 0 : $left - $right;
    }

    public static function checkedMultiply(int $left, int $right): int
    {
        if ($left < 0 || $right < 0) {
            throw PricingException::arithmetic('Pricing factors cannot be negative.');
        }

        if ($left !== 0 && $right > intdiv(PHP_INT_MAX, $left)) {
            throw PricingException::overflow();
        }

        return $left * $right;
    }

    public static function multiplyDivideHalfUp(int $left, int $right, int $divisor): int
    {
        if ($left < 0 || $right < 0 || $divisor <= 0) {
            throw PricingException::arithmetic('Invalid multiply/divide operands.');
        }

        if ($left === 0 || $right === 0) {
            return 0;
        }

        $leftDivisorGcd = self::greatestCommonDivisor($left, $divisor);
        $left = intdiv($left, $leftDivisorGcd);
        $divisor = intdiv($divisor, $leftDivisorGcd);

        $rightDivisorGcd = self::greatestCommonDivisor($right, $divisor);
        $right = intdiv($right, $rightDivisorGcd);
        $divisor = intdiv($divisor, $rightDivisorGcd);

        $numerator = self::checkedMultiply($left, $right);
        $quotient = intdiv($numerator, $divisor);
        $remainder = $numerator % $divisor;

        return $remainder >= $divisor - $remainder
            ? self::checkedAdd($quotient, 1)
            : $quotient;
    }

    public static function multiplyDivideUp(int $left, int $right, int $divisor): int
    {
        if ($left < 0 || $right < 0 || $divisor <= 0) {
            throw PricingException::arithmetic('Invalid multiply/divide operands.');
        }

        if ($left === 0 || $right === 0) {
            return 0;
        }

        $leftDivisorGcd = self::greatestCommonDivisor($left, $divisor);
        $left = intdiv($left, $leftDivisorGcd);
        $divisor = intdiv($divisor, $leftDivisorGcd);

        $rightDivisorGcd = self::greatestCommonDivisor($right, $divisor);
        $right = intdiv($right, $rightDivisorGcd);
        $divisor = intdiv($divisor, $rightDivisorGcd);

        $numerator = self::checkedMultiply($left, $right);
        $quotient = intdiv($numerator, $divisor);

        return $numerator % $divisor === 0
            ? $quotient
            : self::checkedAdd($quotient, 1);
    }

    public static function greatestCommonDivisor(int $left, int $right): int
    {
        $left = abs($left);
        $right = abs($right);

        while ($right !== 0) {
            [$left, $right] = [$right, $left % $right];
        }

        return max($left, 1);
    }

    private static function parseUnsignedInteger(string $value): int
    {
        $normalised = ltrim($value, '0');

        if ($normalised === '') {
            return 0;
        }

        $max = (string) PHP_INT_MAX;

        if (strlen($normalised) > strlen($max)
            || (strlen($normalised) === strlen($max) && strcmp($normalised, $max) > 0)) {
            throw PricingException::overflow();
        }

        return (int) $normalised;
    }

    private static function isPowerOfTen(int $scale): bool
    {
        while ($scale > 1 && $scale % 10 === 0) {
            $scale = intdiv($scale, 10);
        }

        return $scale === 1;
    }
}
