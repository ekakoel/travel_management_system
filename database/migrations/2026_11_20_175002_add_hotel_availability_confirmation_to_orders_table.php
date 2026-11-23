<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('hotel_confirmation_number', 64)->nullable()->after('confirmation_order');
            $table->boolean('hotel_availability_confirmed')->default(false)->after('hotel_confirmation_number');
            $table->timestamp('hotel_confirmed_at')->nullable()->after('hotel_availability_confirmed');
            $table->integer('hotel_confirmed_by')->nullable()->after('hotel_confirmed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'hotel_confirmation_number',
                'hotel_availability_confirmed',
                'hotel_confirmed_at',
                'hotel_confirmed_by',
            ]);
        });
    }
};
