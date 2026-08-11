<?php

namespace App\Exceptions;

use RuntimeException;

class PricingException extends RuntimeException
{
    public function __construct(
        public readonly string $pricingCode,
        string $message,
        public readonly array $safeContext = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public static function arithmetic(string $message, array $context = []): self
    {
        return new self('PRICING_ARITHMETIC_INVALID', $message, $context);
    }

    public static function overflow(): self
    {
        return new self('PRICING_ARITHMETIC_OVERFLOW', 'Pricing amount exceeds supported bounds.');
    }

    public static function rate(string $code, string $message, array $context = []): self
    {
        return new self($code, $message, $context);
    }

    public static function tax(string $code, string $message, array $context = []): self
    {
        return new self($code, $message, $context);
    }

    public static function unavailable(string $code, string $message = 'Price temporarily unavailable.'): self
    {
        return new self($code, $message);
    }
}
