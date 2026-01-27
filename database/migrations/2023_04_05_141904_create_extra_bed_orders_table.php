<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtraBedOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_bed_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->string('rooms_id');
            $table->string('extra_bed_id');
            $table->string('duration');
            $table->string('price_pax');
            $table->string('total_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extra_bed_orders');
    }
}
