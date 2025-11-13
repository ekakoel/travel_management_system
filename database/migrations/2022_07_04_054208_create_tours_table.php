<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToursTable extends Migration
{
    public function up()
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('name_traditional');
            $table->string('name_simplified');
            $table->string('slug')->unique();
            $table->string('cover')->nullable();
            $table->string('area')->nullable();
            $table->string('area_traditional')->nullable();
            $table->string('area_simplified')->nullable();
            $table->foreignId('type_id')->constrained()->cascadeOnDelete()->nullable();
            $table->text('short_description')->nullable();
            $table->text('short_description_traditional')->nullable();
            $table->text('short_description_simplified')->nullable();
            $table->longText('description')->nullable();
            $table->longText('description_traditional')->nullable();
            $table->longText('description_simplified')->nullable();
            $table->integer('duration_days')->default(1);
            $table->integer('duration_nights')->default(0);
            $table->longText('itinerary')->nullable();
            $table->longText('itinerary_traditional')->nullable();
            $table->longText('itinerary_simplified')->nullable();
            $table->longText('include')->nullable();
            $table->longText('include_traditional')->nullable();
            $table->longText('include_simplified')->nullable();
            $table->longText('exclude')->nullable();
            $table->longText('exclude_traditional')->nullable();
            $table->longText('exclude_simplified')->nullable();
            $table->longText('additional_info')->nullable();
            $table->longText('additional_info_traditional')->nullable();
            $table->longText('additional_info_simplified')->nullable();
            $table->longText('cancellation_policy')->nullable();
            $table->longText('cancellation_policy_traditional')->nullable();
            $table->longText('cancellation_policy_simplified')->nullable();
            $table->enum('status', ['Draft', 'Active'])->default('Draft');
            $table->softDeletes();
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('tours');
    }
}
