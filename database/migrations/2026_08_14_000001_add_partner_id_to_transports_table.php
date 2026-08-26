<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transports') || Schema::hasColumn('transports', 'partner_id')) {
            return;
        }

        Schema::table('transports', function (Blueprint $table) {
            $table->unsignedBigInteger('partner_id')->nullable()->after('id')->index();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('transports') || ! Schema::hasColumn('transports', 'partner_id')) {
            return;
        }

        Schema::table('transports', function (Blueprint $table) {
            $table->dropIndex(['partner_id']);
            $table->dropColumn('partner_id');
        });
    }
};
