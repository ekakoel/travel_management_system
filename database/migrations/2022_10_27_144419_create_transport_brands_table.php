<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportBrandsTable extends Migration
{
    public function up()
    {
        Schema::create('transport_brands', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transport_brands');
    }
}
