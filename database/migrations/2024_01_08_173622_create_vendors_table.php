<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('vendors')) {
            return;
        }

        Schema::create('vendors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('cover')->nulable();
            $table->string('name')->nulable();
            $table->string('location')->nulable();
            $table->string('type')->nulable();
            $table->string('contact_name')->nulable();
            $table->string('phone')->nulable();
            $table->string('email')->nulable();
            $table->text('term')->nulable()->nulable();
            $table->text('description')->nulable();
            $table->string('status')->nulable();
            $table->integer('author_id')->nulable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendors');
    }
}
