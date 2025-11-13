<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeddingDecorationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wedding_decorations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('hotel_id')->nullable();
            $table->integer('vendor_id')->nullable();
            $table->string('venue');
            $table->string('cover');
            $table->string('name');
            $table->integer('duration');
            $table->integer('capacity');
            $table->longText('description')->nullable();
            $table->longText('terms_and_conditions')->nullable();
            $table->string('price')->nullable();
            $table->string('status');
            $table->integer('author_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wedding_decorations');
    }
}
