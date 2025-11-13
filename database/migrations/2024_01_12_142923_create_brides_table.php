<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBridesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brides', function (Blueprint $table) {
            $table->id();
            $table->string('bride');
            $table->string('bride_chinese')->nullable();
            $table->string('bride_contact')->nullable();
            $table->string('bride_pasport_id')->nullable();
            $table->string('bride_pasport_img')->nullable();
            $table->string('groom');
            $table->string('groom_contact')->nullable();
            $table->string('groom_chinese')->nullable();
            $table->string('groom_pasport_id')->nullable();
            $table->string('groom_pasport_img')->nullable();
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
        Schema::dropIfExists('brides');
    }
}
