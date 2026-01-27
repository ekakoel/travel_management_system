<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportsTable extends Migration
{
    public function up()
    {
        Schema::create('transports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code');
            $table->string('type');
            $table->string('brand');
            $table->longText('description');
            $table->longText('include')->nullable();
            $table->longText('additional_info')->nullable();
            $table->longText('cancellation_policy')->nullable();
            $table->string('capacity');
            $table->string('cover');
            $table->string('status');
            $table->integer('author_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transports');
    }
}
