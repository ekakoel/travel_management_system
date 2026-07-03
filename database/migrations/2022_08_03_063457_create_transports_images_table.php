<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportsImagesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('transports_images')) {
            return;
        }

        Schema::create('transports_images', function (Blueprint $table) {
            $table->id();
            $table->string("image");
            $table->foreignId("transports_id")->constrained("transports")->onDelete("cascade");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transports_images');
    }
}
