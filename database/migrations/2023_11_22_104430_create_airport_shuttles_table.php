<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirportShuttlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airport_shuttles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('date')->nullable();
            $table->string('flight_number',125)->nullable();
            $table->integer('number_of_guests',11)->nullable();
            $table->integer("order_id")->nullable()->nullable();
            $table->foreignId("spk_id")->constrained("spks")->onDelete("cascade")->nullable();
            $table->foreignId("transport_id")->constrained("transports")->onDelete("cascade")->nullable();
            $table->foreignId("price_id")->nullable()->constrained("transport_prices")->onDelete("cascade")->nullable();
            $table->string('src')->nullable();
            $table->string('dst')->nullable();
            $table->integer('duration',11)->nullable();
            $table->integer('distance',11)->nullable();
            $table->string('price',125)->nullable();
            $table->string("order_wedding_id")->nullable();
            $table->enum('nav', ['In', 'Out','None'])->nullable();
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
        Schema::dropIfExists('airport_shuttles');
    }
}
