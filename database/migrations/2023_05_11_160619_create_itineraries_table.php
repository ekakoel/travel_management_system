<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItinerariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itineraries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tour_id')->nullable()->index();
            $table->integer('day_number')->default(1);
            $table->string('time')->nullable();
            $table->string('title')->nullable();
            $table->string('title_traditional')->nullable();
            $table->string('title_simplified')->nullable();
            $table->text('description')->nullable();
            $table->text('description_traditional')->nullable();
            $table->text('description_simplified')->nullable();
            $table->string('location')->nullable();
            $table->integer('sort_order')->default(0);
            $table->enum('status', ['Draft', 'Active'])->default('Draft');

            $table->timestamps();
            $table->foreign('tour_id')
                ->references('id')
                ->on('tours')
                ->onUpdate('cascade')   // kalau id tour diubah (jarang terjadi)
                ->nullOnDelete(); 
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
        Schema::dropIfExists('itineraries');
    }
}
