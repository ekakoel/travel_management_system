<?php

namespace Tests\Feature\Pricing;

use App\Exceptions\PricingException;
use App\Services\Pricing\CurrencyRateResolver;
use App\Services\Pricing\TaxResolver;
use App\Support\Pricing\FixedScale;
use Carbon\CarbonImmutable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PricingResolverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:') {
            $this->fail('Pricing resolver tests require SQLite :memory:.');
        }

        Schema::create('usd_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rate');
            $table->string('sell');
            $table->string('buy')->nullable();
            $table->string('difference')->nullable();
            $table->dateTime('retrieved_at', 6)->nullable();
            $table->string('retrieval_source')->nullable();
            $table->timestamps();
        });

        Schema::create('tax_policies', function (Blueprint $table) {
            $table->id();
            $table->string('service');
            $table->string('name');
            $table->unsignedBigInteger('percentage_scaled');
            $table->unsignedInteger('percentage_scale');
            $table->string('calculation_type');
            $table->string('taxable_base');
            $table->string('status');
            $table->dateTime('effective_from', 6);
            $table->dateTime('effective_until', 6)->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_at', 6)->nullable();
            $table->timestamps();
        });
    }

    public function test_rate_resolver_uses_sell_and_accepts_exact_24_hour_boundary(): void
    {
        DB::table('usd_rates')->insert([
            'name' => 'USD',
            'rate' => '1',
            'sell' => '16000.125',
            'retrieved_at' => '2026-07-28 12:00:00',
            'retrieval_source' => 'fixture',
            'created_at' => '2026-07-28 12:00:00',
            'updated_at' => '2026-07-28 12:00:00',
        ]);

        $resolved = app(CurrencyRateResolver::class)
            ->resolveUsdSell(CarbonImmutable::parse('2026-07-29 12:00:00'));

        $this->assertSame(FixedScale::parseDecimal('16000.125', 1_000_000), $resolved->valueScaled);
        $this->assertSame('sell', $resolved->side);
        $this->assertSame(86_400, $resolved->ageSeconds);
    }

    public function test_rate_resolver_rejects_stale_zero_and_ambiguous_rate(): void
    {
        DB::table('usd_rates')->insert([
            'name' => 'USD',
            'rate' => '18000',
            'sell' => '16000',
            'retrieved_at' => '2026-07-28 11:59:59',
            'created_at' => '2026-07-28 11:59:59',
            'updated_at' => '2026-07-28 11:59:59',
        ]);

        try {
            app(CurrencyRateResolver::class)
                ->resolveUsdSell(CarbonImmutable::parse('2026-07-29 12:00:00'));
            $this->fail('Expected stale rate failure.');
        } catch (PricingException $exception) {
            $this->assertSame('PRICING_RATE_STALE', $exception->pricingCode);
        }

        DB::table('usd_rates')->delete();
        DB::table('usd_rates')->insert([
            [
                'name' => 'USD',
                'rate' => '18000',
                'sell' => '0',
                'retrieved_at' => '2026-07-29 11:00:00',
                'created_at' => '2026-07-29 11:00:00',
                'updated_at' => '2026-07-29 11:00:00',
            ],
        ]);

        try {
            app(CurrencyRateResolver::class)
                ->resolveUsdSell(CarbonImmutable::parse('2026-07-29 12:00:00'));
            $this->fail('Expected zero rate failure.');
        } catch (PricingException $exception) {
            $this->assertSame('PRICING_RATE_INVALID', $exception->pricingCode);
        }

        DB::table('usd_rates')->update(['sell' => '16000']);
        DB::table('usd_rates')->insert([
            'name' => 'USD',
            'rate' => '18000',
            'sell' => '17000',
            'retrieved_at' => '2026-07-29 11:00:00',
            'created_at' => '2026-07-29 11:00:00',
            'updated_at' => '2026-07-29 11:00:00',
        ]);

        try {
            app(CurrencyRateResolver::class)
                ->resolveUsdSell(CarbonImmutable::parse('2026-07-29 12:00:00'));
            $this->fail('Expected ambiguous rate failure.');
        } catch (PricingException $exception) {
            $this->assertSame('PRICING_RATE_AMBIGUOUS', $exception->pricingCode);
        }
    }

    public function test_tax_resolver_requires_exactly_one_effective_approved_policy(): void
    {
        $this->insertTaxPolicy();

        $resolved = app(TaxResolver::class)
            ->resolve('Tour Package', CarbonImmutable::parse('2026-07-29 12:00:00'));

        $this->assertSame(1_500_000, $resolved->percentageScaled);

        $this->insertTaxPolicy(['name' => 'Overlap']);

        try {
            app(TaxResolver::class)
                ->resolve('Tour Package', CarbonImmutable::parse('2026-07-29 12:00:00'));
            $this->fail('Expected ambiguous tax failure.');
        } catch (PricingException $exception) {
            $this->assertSame('PRICING_TAX_AMBIGUOUS', $exception->pricingCode);
        }

        DB::table('tax_policies')->delete();

        try {
            app(TaxResolver::class)
                ->resolve('Tour Package', CarbonImmutable::parse('2026-07-29 12:00:00'));
            $this->fail('Expected missing tax failure.');
        } catch (PricingException $exception) {
            $this->assertSame('PRICING_TAX_MISSING', $exception->pricingCode);
        }
    }

    private function insertTaxPolicy(array $overrides = []): void
    {
        DB::table('tax_policies')->insert(array_merge([
            'service' => 'Tour Package',
            'name' => 'Tour Tax',
            'percentage_scaled' => 1_500_000,
            'percentage_scale' => 1_000_000,
            'calculation_type' => 'exclusive',
            'taxable_base' => 'contract_plus_markup',
            'status' => 'active',
            'effective_from' => '2026-07-29 00:00:00',
            'effective_until' => null,
            'approved_by' => 1,
            'approved_at' => '2026-07-29 00:00:00',
            'created_at' => '2026-07-29 00:00:00',
            'updated_at' => '2026-07-29 00:00:00',
        ], $overrides));
    }
}
