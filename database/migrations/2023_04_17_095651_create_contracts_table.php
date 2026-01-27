<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{

    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('hotels_id')->nullable();
            $table->string('villa_id')->nullable();
            $table->string('name')->nullable();
            $table->text('file_name')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
