<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTermAndConditionsTable extends Migration
{
    public function up()
    {
        Schema::create('term_and_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('name_id');
            $table->string('name_en');
            $table->string('name_zh');
            $table->longText('policy_en');
            $table->longText('policy_zh');
            $table->longText('policy_id');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('term_and_conditions');
    }
}
