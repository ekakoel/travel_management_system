<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('hotels')) {
            return;
        }

        Schema::table('hotels', function (Blueprint $table) {
            if (!Schema::hasColumn('hotels', 'benefits_traditional')) {
                $table->longText('benefits_traditional')->nullable()->after('benefits');
            }

            if (!Schema::hasColumn('hotels', 'benefits_simplified')) {
                $table->longText('benefits_simplified')->nullable()->after('benefits_traditional');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('hotels')) {
            return;
        }

        Schema::table('hotels', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('hotels', 'benefits_traditional')) {
                $columnsToDrop[] = 'benefits_traditional';
            }

            if (Schema::hasColumn('hotels', 'benefits_simplified')) {
                $columnsToDrop[] = 'benefits_simplified';
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
