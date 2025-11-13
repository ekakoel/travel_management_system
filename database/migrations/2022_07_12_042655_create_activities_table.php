<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('partners_id')->nullable();
            $table->string('name');
            $table->string('code');
            $table->string('type');
            $table->string('location');
            $table->string('map');
            $table->longText('description')->nullable();
            $table->longText('itinerary')->nullable();
            $table->string('duration');
            $table->longText('include')->nullable();
            $table->longText('additional_info')->nullable();
            $table->longText('cancellation_policy')->nullable();
            $table->integer('contract_rate');
            $table->integer('markup');
            $table->string('qty');
            $table->string('min_pax');
            $table->string('status');
            $table->integer('author_id');
            $table->text('cover');
            $table->string('validity');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activities');
    }
}
