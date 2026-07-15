<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders') || !Schema::hasColumn('orders', 'destinations')) {
            return;
        }

        DB::statement('ALTER TABLE `orders` MODIFY `destinations` LONGTEXT NULL');
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders') || !Schema::hasColumn('orders', 'destinations')) {
            return;
        }

        DB::statement('ALTER TABLE `orders` MODIFY `destinations` VARCHAR(255) NULL');
    }
};
