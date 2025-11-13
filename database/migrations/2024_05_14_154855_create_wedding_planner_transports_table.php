<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeddingPlannerTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wedding_planner_transports', function (Blueprint $table) {
            $table->id();
            $table->integer('wedding_planner_id');
            $table->integer('order_wedding_id');
            $table->integer('transport_id');
            $table->integer('driver_id')->nullable();
            $table->integer('guide_id')->nullable();
            $table->string('type')->nullable();;
            $table->string('desc_type')->nullable();;
            $table->string('date')->nullable();;
            $table->string('passenger')->nullable();
            $table->integer('number_of_guests');
            $table->string('duration')->nullable();
            $table->string('distance')->nullable();
            $table->longText('remark')->nullable();;
            $table->integer('price')->nullable();
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
        Schema::dropIfExists('wedding_planner_transports');
    }
}
