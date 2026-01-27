<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomFacilitiesTable extends Migration
{
    public function up()
    {
        Schema::create('room_facilities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId("rooms_id")->constrained("rooms")->onDelete("cascade");
            $table->integer('wifi');
            $table->integer('single_bed');
            $table->integer('double_bed');
            $table->integer('extra_bed');
            $table->integer('air_conditioning');
            $table->integer('pool');
            $table->integer('tv_channel');
            $table->integer('water_heater');
            $table->integer('bathtub');
        });
    }


    public function down()
    {
        Schema::dropIfExists('room_facilities');
    }
}
