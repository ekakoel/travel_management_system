<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeddingDinnerVenuesTable extends Migration
{
    public function up()
    {
        Schema::create('wedding_dinner_venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId("wedding_id")->constrained("weddings")->onDelete("cascade");
            $table->string("cover");
            $table->string("type");
            $table->string("name");
            $table->integer("capacity");
            $table->integer("min_invitations");
            $table->longText("additional_info")->nullable();
            $table->string("status");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wedding_dinner_venues');
    }
}
