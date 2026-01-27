<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeddingVenuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wedding_venues', function (Blueprint $table) {
            $table->id();
            $table->integer('hotels_id');
            $table->string('cover');
            $table->string('name');
            $table->string('slot')->nullable();
            $table->string('basic_price')->nullable();
            $table->string('arrangement_price')->nullable();
            $table->string('capacity');
            $table->longText('description')->nullable();
            $table->longText('terms_and_conditions')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wedding_venues');
    }
}
