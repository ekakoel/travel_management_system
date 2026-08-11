<?php

namespace Tests\Feature\Pricing;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PricingSchemaMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:') {
            $this->fail('Pricing migration tests require SQLite :memory:.');
        }

        $this->createBaseSchema();
    }

    public function test_pricing_migrations_are_additive_and_reversible(): void
    {
        $migrations = $this->pricingMigrations();

        foreach ($migrations as $migration) {
            $migration->up();
        }

        foreach ([
            'contract_rate_idr',
            'markup_amount',
            'markup_type',
            'markup_currency',
            'markup_source',
            'markup_verified_at',
            'markup_verified_by',
            'pricing_data_status',
            'valid_from',
            'valid_until',
        ] as $column) {
            $this->assertTrue(Schema::hasColumn('tour_prices', $column));
        }

        $this->assertTrue(Schema::hasColumn('usd_rates', 'retrieved_at'));
        $this->assertTrue(Schema::hasColumn('usd_rates', 'retrieval_source'));
        $this->assertTrue(Schema::hasTable('tax_policies'));
        $this->assertTrue(Schema::hasTable('order_pricing_snapshots'));
        $this->assertTrue(Schema::hasColumn('orders', 'pricing_snapshot_id'));
        $this->assertTrue(Schema::hasColumn('orders', 'final_total_idr'));
        $this->assertTrue(Schema::hasColumn('orders', 'final_total_usd_minor'));
        $this->assertTrue(Schema::hasColumn('orders', 'submission_token_hash'));
        $this->assertTrue(Schema::hasColumn('promotions', 'discount_type'));
        $this->assertTrue(Schema::hasColumn('booking_codes', 'discount_currency'));
        $this->assertSame('string', Schema::getColumnType('tour_prices', 'markup_amount'));

        foreach (array_reverse($migrations) as $migration) {
            $migration->down();
        }

        $this->assertFalse(Schema::hasTable('order_pricing_snapshots'));
        $this->assertFalse(Schema::hasTable('tax_policies'));
        $this->assertFalse(Schema::hasColumn('tour_prices', 'contract_rate_idr'));
        $this->assertFalse(Schema::hasColumn('tour_prices', 'valid_from'));
        $this->assertFalse(Schema::hasColumn('orders', 'pricing_snapshot_id'));
        $this->assertFalse(Schema::hasColumn('orders', 'submission_token_hash'));
        $this->assertTrue(Schema::hasColumn('tour_prices', 'contract_rate'));
        $this->assertTrue(Schema::hasColumn('orders', 'final_price'));
    }

    public function test_markup_storage_removes_legacy_decimal_padding(): void
    {
        foreach ([
            '2026_07_29_170000_add_pricing_shadow_fields_to_tour_prices_table.php',
            '2026_07_31_120000_add_valid_from_to_tour_prices_table.php',
            '2026_07_31_130000_add_markup_type_to_tour_prices_table.php',
        ] as $migration) {
            (require database_path('migrations/'.$migration))->up();
        }

        DB::table('tour_prices')->insert([
            'id' => 1,
            'tour_id' => '1',
            'min_qty' => 1,
            'max_qty' => 2,
            'contract_rate' => '1000000',
            'markup' => '20',
            'markup_amount' => '20.000000',
            'markup_currency' => 'USD',
            'expired_date' => '2026-12-31',
            'status' => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        (require database_path(
            'migrations/2026_07_31_140000_store_tour_price_markup_as_exact_numeric_string.php'
        ))->up();

        $this->assertSame('20', DB::table('tour_prices')->where('id', 1)->value('markup_amount'));
    }

    private function pricingMigrations(): array
    {
        return [
            require database_path('migrations/2026_07_29_170000_add_pricing_shadow_fields_to_tour_prices_table.php'),
            require database_path('migrations/2026_07_29_170100_add_retrieval_metadata_to_usd_rates_table.php'),
            require database_path('migrations/2026_07_29_170200_create_tax_policies_table.php'),
            require database_path('migrations/2026_07_29_170300_create_order_pricing_snapshots_table.php'),
            require database_path('migrations/2026_07_29_170400_add_pricing_summary_to_orders_table.php'),
            require database_path('migrations/2026_07_29_170500_add_pricing_metadata_to_tour_discounts.php'),
            require database_path('migrations/2026_07_29_170600_add_tour_order_idempotency_to_orders_table.php'),
            require database_path('migrations/2026_07_31_120000_add_valid_from_to_tour_prices_table.php'),
            require database_path('migrations/2026_07_31_130000_add_markup_type_to_tour_prices_table.php'),
            require database_path('migrations/2026_07_31_140000_store_tour_price_markup_as_exact_numeric_string.php'),
        ];
    }

    private function createBaseSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
        });

        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
        });

        Schema::create('tour_prices', function (Blueprint $table) {
            $table->id();
            $table->string('tour_id');
            $table->integer('min_qty');
            $table->integer('max_qty');
            $table->string('contract_rate');
            $table->string('markup');
            $table->string('expired_date');
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('usd_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rate');
            $table->string('sell');
            $table->string('buy');
            $table->string('difference');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('service')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('final_price')->nullable();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('status');
        });

        Schema::create('booking_codes', function (Blueprint $table) {
            $table->id();
            $table->string('status');
        });
    }
}
