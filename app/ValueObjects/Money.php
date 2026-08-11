<?php

namespace App\ValueObjects;

use App\Exceptions\PricingException;
use App\Support\Pricing\FixedScale;
use JsonSerializable;

final class Money implements JsonSerializable
{
    public const IDR = 'IDR';
    public const USD = 'USD';

    public function __construct(
        public readonly int $amount,
        public readonly string $currency,
    ) {
        if ($amount < 0) {
            throw PricingException::arithmetic('Money cannot be negative.');
        }

        if (!in_array($currency, [self::IDR, self::USD], true)) {
            throw PricingException::arithmetic('Unsupported currency.', [
                'currency' => $currency,
            ]);
        }
    }

    public static function idr(int $rupiah): self
    {
        return new self($rupiah, self::IDR);
    }

    public static function usdCents(int $cents): self
    {
        return new self($cents, self::USD);
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self(FixedScale::checkedAdd($this->amount, $other->amount), $this->currency);
    }

    public function subtractFloorZero(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self(
            FixedScale::checkedSubtractNonNegative($this->amount, $other->amount),
            $this->currency
        );
    }

    public function multiply(int $quantity): self
    {
        return new self(FixedScale::checkedMultiply($this->amount, $quantity), $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->currency === $other->currency && $this->amount === $other->amount;
    }

    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw PricingException::arithmetic('Money currencies do not match.');
        }
    }
}
