<?php

namespace App\Data\Pricing;

use DateTimeImmutable;

final class ResolvedTaxPolicy
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $service,
        public readonly int $percentageScaled,
        public readonly int $percentageScale,
        public readonly string $calculationType,
        public readonly string $taxableBase,
        public readonly DateTimeImmutable $effectiveFrom,
        public readonly ?DateTimeImmutable $effectiveUntil,
    ) {
    }
}
