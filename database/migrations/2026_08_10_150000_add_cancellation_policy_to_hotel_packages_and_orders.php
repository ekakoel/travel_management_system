<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hotel_packages')) {
            $packageColumns = Schema::getColumnListing('hotel_packages');

            Schema::table('hotel_packages', function (Blueprint $table) use ($packageColumns) {
                if (! in_array('cancellation_policy', $packageColumns, true)) {
                    $table->longText('cancellation_policy')->nullable()->after('additional_info_simplified');
                }

                if (! in_array('cancellation_policy_traditional', $packageColumns, true)) {
                    $table->longText('cancellation_policy_traditional')->nullable()->after('cancellation_policy');
                }

                if (! in_array('cancellation_policy_simplified', $packageColumns, true)) {
                    $table->longText('cancellation_policy_simplified')->nullable()->after('cancellation_policy_traditional');
                }
            });
        }

        if (Schema::hasTable('orders')) {
            $orderColumns = Schema::getColumnListing('orders');

            Schema::table('orders', function (Blueprint $table) use ($orderColumns) {
                if (! in_array('cancellation_policy_traditional', $orderColumns, true)) {
                    $table->longText('cancellation_policy_traditional')->nullable()->after('cancellation_policy');
                }

                if (! in_array('cancellation_policy_simplified', $orderColumns, true)) {
                    $table->longText('cancellation_policy_simplified')->nullable()->after('cancellation_policy_traditional');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            $existingColumns = Schema::getColumnListing('orders');
            $columns = array_values(array_intersect(
                ['cancellation_policy_traditional', 'cancellation_policy_simplified'],
                $existingColumns
            ));

            Schema::table('orders', function (Blueprint $table) use ($columns) {
                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }

        if (Schema::hasTable('hotel_packages')) {
            $existingColumns = Schema::getColumnListing('hotel_packages');
            $columns = array_values(array_intersect(
                [
                    'cancellation_policy',
                    'cancellation_policy_traditional',
                    'cancellation_policy_simplified',
                ],
                $existingColumns
            ));

            Schema::table('hotel_packages', function (Blueprint $table) use ($columns) {
                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
