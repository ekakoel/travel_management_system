<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transports') || Schema::hasColumn('transports', 'inventory')) {
            return;
        }

        Schema::table('transports', function (Blueprint $table) {
            $table->unsignedInteger('inventory')->nullable()->after('capacity');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('transports') || ! Schema::hasColumn('transports', 'inventory')) {
            return;
        }

        Schema::table('transports', function (Blueprint $table) {
            $table->dropColumn('inventory');
        });
    }
};
