<?php

namespace App\Data\Pricing;

use DateTimeImmutable;

final class ResolvedCurrencyRate
{
    public function __construct(
        public readonly int $id,
        public readonly string $pair,
        public readonly string $side,
        public readonly int $valueScaled,
        public readonly int $scale,
        public readonly string $source,
        public readonly DateTimeImmutable $retrievedAt,
        public readonly int $maxAgeSeconds,
        public readonly int $ageSeconds,
    ) {
    }
}
