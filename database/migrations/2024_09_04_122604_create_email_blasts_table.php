<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailBlastsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_blasts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('title_mandarin');
            $table->foreignId("hotel_id")->constrained("hotels")->onDelete("cascade")->nullable();
            $table->foreignId("promo_id")->constrained("hotel_promos")->onDelete("cascade")->nullable();
            $table->longText('suggestion');
            $table->longText('suggestion_mandarin');
            $table->longText('benefits');
            $table->longText('benefits_mandarin');
            $table->longText('content');
            $table->longText('content_mandarin');
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
        Schema::dropIfExists('email_blasts');
    }
}
