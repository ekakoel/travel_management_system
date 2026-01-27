<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeddingAccomodationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wedding_accomodations', function (Blueprint $table) {
            $table->id();
            $table->integer('wedding_planner_id');
            $table->integer('order_wedding_package_id');
            $table->string('room_for');
            $table->string('hotels_id')->nullable();
            $table->string('rooms_id');
            $table->string('checkin')->nullable();
            $table->string('checkout')->nullable();
            $table->integer('duration');
            $table->string('guest_detail')->nullable();
            $table->string('number_of_guests');
            $table->string('extra_bed_id');
            $table->text("remark")->nullable();
            $table->text("note")->nullable();
            $table->string("status");
            $table->string("public_rate");
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
        Schema::dropIfExists('wedding_accomodations');
    }
}
