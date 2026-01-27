<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('group');
            $table->string('flight');
            $table->string('time');
            $table->string('guests')->nullable();
            $table->string('guests_contact')->nullable();
            $table->string('number_of_guests');
            $table->integer('order_id')->nullable();
            $table->integer('order_wedding_id')->nullable();
            $table->integer('wedding_planner_id')->nullable();
            $table->string('status');
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
        Schema::dropIfExists('flights');
    }
}
