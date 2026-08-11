<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['promotions', 'booking_codes'] as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'discount_type')) {
                    $table->string('discount_type', 20)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'discount_value')) {
                    $table->decimal('discount_value', 20, 6)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'discount_currency')) {
                    $table->char('discount_currency', 3)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'service_scope')) {
                    $table->string('service_scope', 64)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'valid_from')) {
                    $table->dateTime('valid_from', 6)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'valid_until')) {
                    $table->dateTime('valid_until', 6)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'pricing_data_status')) {
                    $table->string('pricing_data_status', 32)->default('unresolved');
                }
            });

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->index(
                    ['status', 'pricing_data_status', 'service_scope', 'valid_from', 'valid_until'],
                    $tableName === 'promotions'
                        ? 'promotions_pricing_eligibility_idx'
                        : 'booking_codes_pricing_eligibility_idx'
                );
            });
        }
    }

    public function down(): void
    {
        foreach (['promotions', 'booking_codes'] as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'pricing_data_status')) {
                    $table->dropIndex(
                        $tableName === 'promotions'
                            ? 'promotions_pricing_eligibility_idx'
                            : 'booking_codes_pricing_eligibility_idx'
                    );
                }

            });

            $columns = array_values(array_filter([
                'discount_type',
                'discount_value',
                'discount_currency',
                'service_scope',
                'valid_from',
                'valid_until',
                'pricing_data_status',
            ], fn (string $column) => Schema::hasColumn($tableName, $column)));

            if ($columns !== []) {
                Schema::table($tableName, function (Blueprint $table) use ($columns) {
                    $table->dropColumn($columns);
                });
            }
        }
    }
};
