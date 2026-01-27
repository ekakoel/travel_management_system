<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttentionsTable extends Migration
{
    public function up()
    {
        Schema::create('attentions', function (Blueprint $table) {
            $table->id();
            $table->string('page');
            $table->string('name');
            $table->text('attention_zh');
            $table->text('attention_en');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('attentions');
    }
}
