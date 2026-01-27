<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("license")->nullable();
            $table->string("tax_number")->nullable();
            $table->string("address")->nullable();
            $table->string("nickname")->nullable();
            $table->string("tax_id")->nullable();
            $table->string("type")->nullable();
            $table->string("map")->nullable();
            $table->string("phone")->nullable();
            $table->text('logo')->nullable();
            $table->string('caption')->nullable();
            $table->string('website')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('business_profiles');
    }
}
