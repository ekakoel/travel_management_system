<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToursImagesTable extends Migration
{
    public function up()
    {
        Schema::create('tours_images', function (Blueprint $table) {
            $table->id();
            $table->string("image");
            $table->string("type");
            $table->foreignId("tour_id")->constrained("tours")->onDelete("cascade");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tours_images');
    }
}
