<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeddingMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wedding_menus', function (Blueprint $table) {
            $table->id();
            $table->integer('hotels_id');
            $table->string('type');
            $table->string('category')->nullable();
            $table->string('name');
            $table->string('duration')->nullable();
            $table->longText('include')->nullable();
            $table->longText('description')->nullable();
            $table->integer('agent_rate')->nullable();
            $table->integer('public_rate')->nullable();
            $table->integer('fee')->nullable();
            $table->longText('note')->nullable();
            $table->string('status');
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
        Schema::dropIfExists('wedding_menus');
    }
}
