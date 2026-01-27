<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->text('cover')->nulable();
            $table->string('name');
            $table->string('location');
            $table->string('type');
            $table->string('contact_name');
            $table->string('phone');
            $table->string('email');
            $table->text('term')->nulable();
            $table->text('description')->nulable();
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendors');
    }
}
