<?php

namespace Tests\Feature\Pricing;

use App\Models\TaxPolicy;
use App\Services\Pricing\TourTaxPolicyActivationService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Tests\TestCase;

class TourTaxPolicyActivationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:') {
            $this->fail('Tour tax activation tests require SQLite :memory:.');
        }

        Schema::create('users', function (Blueprint $table) {
            $table->id();
        });
        DB::table('users')->insert(['id' => 7]);
        (require database_path('migrations/2026_07_29_170200_create_tax_policies_table.php'))->up();
    }

    public function test_initial_fixture_is_exactly_approved_tour_1_5_percent_exclusive(): void
    {
        $effectiveFrom = CarbonImmutable::parse('2026-08-01T00:00:00+08:00');
        $policy = app(TourTaxPolicyActivationService::class)
            ->activateInitialPolicy($effectiveFrom, 7);

        $this->assertSame('Tour Package', $policy->service);
        $this->assertSame(1_500_000, $policy->percentage_scaled);
        $this->assertSame(1_000_000, $policy->percentage_scale);
        $this->assertSame('exclusive', $policy->calculation_type);
        $this->assertSame('contract_plus_markup', $policy->taxable_base);
        $this->assertSame('active', $policy->status);
        $this->assertSame(7, $policy->approved_by);
        $this->assertNotNull($policy->approved_at);
        $this->assertNull($policy->effective_until);
    }

    public function test_overlapping_active_tour_policy_is_rejected(): void
    {
        $service = app(TourTaxPolicyActivationService::class);
        $service->activateInitialPolicy(
            CarbonImmutable::parse('2026-08-01T00:00:00+08:00'),
            7
        );

        $this->expectException(InvalidArgumentException::class);
        $service->activateInitialPolicy(
            CarbonImmutable::parse('2026-09-01T00:00:00+08:00'),
            7
        );
    }

    public function test_replacing_tax_policy_versions_the_effective_period(): void
    {
        $service = app(TourTaxPolicyActivationService::class);
        $initial = $service->activateInitialPolicy(
            CarbonImmutable::parse('2026-08-01T00:00:00+08:00'),
            7,
        );
        $replacementAt = CarbonImmutable::parse('2026-09-01T00:00:00+08:00');
        $replacement = $service->replaceActivePolicy('2.25', $replacementAt, 7);

        $this->assertTrue($initial->refresh()->effective_until->equalTo($replacementAt));
        $this->assertSame(2_250_000, $replacement->percentage_scaled);
        $this->assertSame(7, $replacement->approved_by);
        $this->assertNull($replacement->effective_until);
        $this->assertSame(2, TaxPolicy::query()->count());
    }
}
