<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsTable extends Migration
{
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('cover')->nullable();
            $table->integer('author_id');
            $table->integer('editor_id')->nullable();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('discounts');
            $table->string('periode_start');
            $table->string('periode_end');
            $table->string('status');
            $table->longText('term')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('promotions');
    }
}
