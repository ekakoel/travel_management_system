<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tour_prices')) {
            return;
        }

        Schema::table('tour_prices', function (Blueprint $table) {
            if (!Schema::hasColumn('tour_prices', 'contract_rate_idr')) {
                $table->unsignedBigInteger('contract_rate_idr')->nullable()->after('contract_rate');
            }
            if (!Schema::hasColumn('tour_prices', 'markup_amount')) {
                $table->decimal('markup_amount', 20, 6)->nullable()->after('markup');
            }
            if (!Schema::hasColumn('tour_prices', 'markup_currency')) {
                $table->char('markup_currency', 3)->nullable()->after('markup_amount');
            }
            if (!Schema::hasColumn('tour_prices', 'markup_source')) {
                $table->string('markup_source', 64)->nullable()->after('markup_currency');
            }
            if (!Schema::hasColumn('tour_prices', 'markup_verified_at')) {
                $table->dateTime('markup_verified_at', 6)->nullable()->after('markup_source');
            }
            if (!Schema::hasColumn('tour_prices', 'markup_verified_by')) {
                $table->unsignedBigInteger('markup_verified_by')->nullable()->after('markup_verified_at');
            }
            if (!Schema::hasColumn('tour_prices', 'pricing_data_status')) {
                $table->string('pricing_data_status', 32)->default('unresolved')->after('markup_verified_by');
            }
            if (!Schema::hasColumn('tour_prices', 'valid_until')) {
                $table->date('valid_until')->nullable()->after('expired_date');
            }
        });

        Schema::table('tour_prices', function (Blueprint $table) {
            $table->index(
                ['tour_id', 'status', 'pricing_data_status', 'valid_until', 'min_qty', 'max_qty', 'deleted_at'],
                'tour_prices_pricing_eligibility_idx'
            );
            $table->index('markup_verified_by', 'tour_prices_markup_verifier_idx');

            if (Schema::hasTable('users')) {
                $table->foreign('markup_verified_by', 'tour_prices_markup_verifier_fk')
                    ->references('id')
                    ->on('users')
                    ->restrictOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('tour_prices')) {
            return;
        }

        Schema::table('tour_prices', function (Blueprint $table) {
            if (Schema::hasColumn('tour_prices', 'markup_verified_by')) {
                if (DB::getDriverName() !== 'sqlite') {
                    $table->dropForeign('tour_prices_markup_verifier_fk');
                }
                $table->dropIndex('tour_prices_markup_verifier_idx');
            }
            if (Schema::hasColumn('tour_prices', 'pricing_data_status')) {
                $table->dropIndex('tour_prices_pricing_eligibility_idx');
            }
        });

        $columns = array_values(array_filter([
            'contract_rate_idr',
            'markup_amount',
            'markup_currency',
            'markup_source',
            'markup_verified_at',
            'markup_verified_by',
            'pricing_data_status',
            'valid_until',
        ], fn (string $column) => Schema::hasColumn('tour_prices', $column)));

        if ($columns !== []) {
            Schema::table('tour_prices', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
