<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelTypesTable extends Migration
{
    public function up()
    {
        Schema::create('hotel_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId("hotels_id")->constrained("hotels")->onDelete("cascade");
            $table->string("type");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hotel_types');
    }
}
