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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id')->constrained()->onDelete('cascade'); // user pemilik cart
            $table->foreignId('hotels_id')->constrained()->onDelete('cascade'); // hotel terkait
            $table->foreignId('hotel_rooms_id')->constrained()->onDelete('cascade');  // room terkait
            $table->date('checkin');
            $table->date('checkout');
            $table->integer('guests')->nullable();
            $table->integer('quantity')->default(1); // jumlah kamar
            $table->json('price', 12, 2); // harga per malam
            $table->decimal('total', 12, 2); // total harga
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
