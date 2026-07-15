<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'include_traditional')) {
                $table->longText('include_traditional')->nullable()->after('include');
            }

            if (!Schema::hasColumn('orders', 'include_simplified')) {
                $table->longText('include_simplified')->nullable()->after('include_traditional');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('orders', 'include_simplified')) {
                $columnsToDrop[] = 'include_simplified';
            }

            if (Schema::hasColumn('orders', 'include_traditional')) {
                $columnsToDrop[] = 'include_traditional';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
