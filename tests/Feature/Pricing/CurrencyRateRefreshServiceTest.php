<?php

namespace Tests\Feature\Pricing;

use App\Exceptions\CurrencyRateRefreshException;
use App\Services\Pricing\CurrencyRateRefreshService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CurrencyRateRefreshServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:') {
            $this->markTestSkipped('Currency refresh regression test requires isolated SQLite in-memory.');
        }

        Schema::create('usd_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rate');
            $table->string('sell');
            $table->string('buy');
            $table->string('difference');
            $table->dateTime('retrieved_at')->nullable();
            $table->string('retrieval_source')->nullable();
            $table->timestamps();
        });

        config([
            'services.exchange_rate.key' => 'testing-key',
            'services.exchange_rate.base_url' => 'https://v6.exchangerate-api.com/v6',
        ]);
        Carbon::setTestNow('2026-08-12 00:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_it_atomically_refreshes_all_supported_rates_and_preserves_spreads(): void
    {
        $this->seedRates();
        $this->fakeSuccessfulProvider();
        Cache::put('usd_rates', 'stale');
        Cache::put('pricing.usd_sell', 'stale');
        Cache::put('backend.currency.external_rates', 'stale');

        $result = app(CurrencyRateRefreshService::class)->refresh();

        $this->assertSame('15000', $result['rates']['USD']);
        $this->assertSame('2000', $result['rates']['CNY']);
        $this->assertSame('500', $result['rates']['TWD']);
        $this->assertDatabaseHas('usd_rates', [
            'name' => 'USD',
            'sell' => '15000',
            'buy' => '14900',
            'difference' => '100',
            'retrieval_source' => 'exchangerate-api-v6',
        ]);
        $this->assertDatabaseHas('usd_rates', [
            'name' => 'CNY',
            'sell' => '2000',
            'buy' => '1970',
            'difference' => '30',
        ]);
        $this->assertDatabaseHas('usd_rates', [
            'name' => 'TWD',
            'sell' => '500',
            'buy' => '495',
            'difference' => '5',
        ]);
        $this->assertNull(Cache::get('usd_rates'));
        $this->assertNull(Cache::get('pricing.usd_sell'));
        $this->assertNull(Cache::get('backend.currency.external_rates'));
        Http::assertSentCount(1);
    }

    public function test_invalid_provider_response_keeps_every_existing_rate_unchanged(): void
    {
        $this->seedRates();
        Http::fake([
            'https://v6.exchangerate-api.com/*' => Http::response([
                'result' => 'success',
                'base_code' => 'USD',
                'conversion_rates' => [
                    'USD' => 1,
                    'IDR' => 15000,
                    'CNY' => 7.5,
                ],
            ]),
        ]);

        try {
            app(CurrencyRateRefreshService::class)->refresh();
            $this->fail('An incomplete provider response must be rejected.');
        } catch (CurrencyRateRefreshException) {
            $this->assertDatabaseHas('usd_rates', ['name' => 'USD', 'sell' => '14000']);
            $this->assertDatabaseHas('usd_rates', ['name' => 'CNY', 'sell' => '1900']);
            $this->assertDatabaseHas('usd_rates', ['name' => 'TWD', 'sell' => '450']);
        }
    }

    public function test_duplicate_currency_records_fail_closed_without_writing_rates(): void
    {
        $this->seedRates();
        DB::table('usd_rates')->insert([
            'name' => 'USD',
            'rate' => '14500',
            'sell' => '14500',
            'buy' => '14400',
            'difference' => '100',
        ]);
        $this->fakeSuccessfulProvider();

        $this->expectException(CurrencyRateRefreshException::class);

        app(CurrencyRateRefreshService::class)->refresh();
    }

    public function test_scheduler_and_manual_refresh_route_use_the_canonical_refresh_flow(): void
    {
        $kernel = file_get_contents(app_path('Console/Kernel.php'));
        $controller = file_get_contents(app_path('Http/Controllers/UsdRatesController.php'));
        $routes = file_get_contents(base_path('routes/web.php'));

        $this->assertStringContainsString("command('currency:refresh-rates')", $kernel);
        $this->assertStringContainsString("dailyAt('00:00')", $kernel);
        $this->assertStringContainsString("timezone(config('app.timezone'))", $kernel);
        $this->assertStringContainsString('CurrencyRateRefreshService', $controller);
        $this->assertStringContainsString("name('admin.currency.refresh-rates')", $routes);
    }

    private function seedRates(): void
    {
        DB::table('usd_rates')->insert([
            ['name' => 'USD', 'rate' => '14000', 'sell' => '14000', 'buy' => '13900', 'difference' => '100'],
            ['name' => 'CNY', 'rate' => '1900', 'sell' => '1900', 'buy' => '1870', 'difference' => '30'],
            ['name' => 'TWD', 'rate' => '450', 'sell' => '450', 'buy' => '445', 'difference' => '5'],
        ]);
    }

    private function fakeSuccessfulProvider(): void
    {
        Http::fake([
            'https://v6.exchangerate-api.com/*' => Http::response([
                'result' => 'success',
                'base_code' => 'USD',
                'conversion_rates' => [
                    'USD' => 1,
                    'IDR' => 15000,
                    'CNY' => 7.5,
                    'TWD' => 30,
                ],
            ]),
        ]);
    }
}
