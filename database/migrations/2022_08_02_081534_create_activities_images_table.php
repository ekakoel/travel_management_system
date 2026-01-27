<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesImagesTable extends Migration
{
    public function up()
    {
        Schema::create('activities_images', function (Blueprint $table) {
            $table->id();
            $table->string("image");
            $table->foreignId("activities_id")->constrained("activities")->onDelete("cascade");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activities_images');
    }
}
