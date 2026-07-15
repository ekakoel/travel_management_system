<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Snapshot fields now store rendered HTML and structured booking summaries.
     * Widen them to LONGTEXT to avoid truncation on rich tour package orders.
     */
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        $columns = [
            'itinerary',
            'guest_detail',
            'include',
            'exclude',
            'destinations',
            'additional_info',
            'msg',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('orders', $column)) {
                DB::statement(sprintf(
                    'ALTER TABLE `orders` MODIFY `%s` LONGTEXT NULL',
                    $column
                ));
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        $textColumns = [
            'itinerary',
            'guest_detail',
            'include',
            'exclude',
            'additional_info',
            'msg',
        ];

        foreach ($textColumns as $column) {
            if (Schema::hasColumn('orders', $column)) {
                DB::statement(sprintf(
                    'ALTER TABLE `orders` MODIFY `%s` TEXT NULL',
                    $column
                ));
            }
        }

        if (Schema::hasColumn('orders', 'destinations')) {
            DB::statement('ALTER TABLE `orders` MODIFY `destinations` VARCHAR(255) NULL');
        }
    }
};
