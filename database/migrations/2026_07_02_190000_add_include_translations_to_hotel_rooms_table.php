<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('hotel_rooms', 'include_traditional')) {
                $table->longText('include_traditional')->nullable()->after('include');
            }

            if (!Schema::hasColumn('hotel_rooms', 'include_simplified')) {
                $table->longText('include_simplified')->nullable()->after('include_traditional');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hotel_rooms', function (Blueprint $table) {
            if (Schema::hasColumn('hotel_rooms', 'include_simplified')) {
                $table->dropColumn('include_simplified');
            }

            if (Schema::hasColumn('hotel_rooms', 'include_traditional')) {
                $table->dropColumn('include_traditional');
            }
        });
    }
};
