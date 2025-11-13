<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeddingDinnerPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wedding_dinner_packages', function (Blueprint $table) {
            $table->id();
            $table->string("dinner_venues_id");
            $table->string("hotels_id");
            $table->string("period_start");
            $table->string("period_end");
            $table->string("name");
            $table->string("number_of_guests");
            $table->longText("include");
            $table->longText("additional_info");
            $table->string("public_rate");
            $table->string("additional_guest_rate");
            $table->string("status");
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
        Schema::dropIfExists('wedding_dinner_packages');
    }
}
