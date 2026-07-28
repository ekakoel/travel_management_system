<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable()->after('handled_date')->index();
                }

                if (!Schema::hasColumn('orders', 'completed_by')) {
                    $table->unsignedBigInteger('completed_by')->nullable()->after('completed_at');
                }
            });
        }

        if (Schema::hasTable('hotel_rooms')) {
            Schema::table('hotel_rooms', function (Blueprint $table) {
                if (!Schema::hasColumn('hotel_rooms', 'inventory')) {
                    $table->unsignedInteger('inventory')->nullable()->after('capacity_child');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'completed_by')) {
                    $table->dropColumn('completed_by');
                }

                if (Schema::hasColumn('orders', 'completed_at')) {
                    $table->dropColumn('completed_at');
                }
            });
        }

        if (Schema::hasTable('hotel_rooms') && Schema::hasColumn('hotel_rooms', 'inventory')) {
            Schema::table('hotel_rooms', function (Blueprint $table) {
                $table->dropColumn('inventory');
            });
        }
    }
};
