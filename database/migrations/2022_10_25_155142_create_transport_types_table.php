<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportTypesTable extends Migration
{
    public function up()
    {
        Schema::create('transport_types', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->timestamps();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('transport_types');
    }
}
