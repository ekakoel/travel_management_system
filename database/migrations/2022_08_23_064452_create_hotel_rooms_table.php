<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelRoomsTable extends Migration
{
    public function up()
    {
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId("hotels_id")->constrained("hotels")->onDelete("cascade");
            $table->string("cover");
            $table->string("rooms");
            $table->integer("capacity_adult");
            $table->integer("capacity_child");
            $table->text("view")->nullable();
            $table->text("beds")->nullable();
            $table->text("size")->nullable();
            $table->longText("amenities")->nullable();
            $table->longText("amenities_traditional")->nullable();
            $table->longText("amenities_simplified")->nullable();
            $table->longText("additional_info")->nullable();
            $table->longText("additional_info_traditional")->nullable();
            $table->longText("additional_info_simplified")->nullable();
            $table->string("status");
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('hotel_rooms');
    }
}
