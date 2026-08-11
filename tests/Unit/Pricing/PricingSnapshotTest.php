<?php

namespace Tests\Unit\Pricing;

use App\Data\Pricing\PricingQuote;
use App\Data\Pricing\PricingSnapshot;
use PHPUnit\Framework\TestCase;

class PricingSnapshotTest extends TestCase
{
    public function test_snapshot_checksum_is_deterministic_and_versioned(): void
    {
        $quote = new PricingQuote([
            'pricing_version' => 'tour-package-v1',
            'service' => 'Tour Package',
            'service_id' => 1,
            'price_id' => 2,
            'base_currency' => 'IDR',
            'display_currency' => 'USD',
            'final_total_idr' => 1_452_000,
            'final_total_usd_minor' => 9_075,
            'calculated_at' => '2026-07-29 12:00:00.000000',
        ]);

        $left = PricingSnapshot::fromQuote($quote, 1, 10, 'initial order');
        $right = PricingSnapshot::fromQuote($quote, 1, 10, 'initial order');

        $this->assertSame($left->checksum, $right->checksum);
        $this->assertSame(1, $left->data['snapshot_sequence']);
        $this->assertSame(10, $left->data['calculated_by']);
    }
}
