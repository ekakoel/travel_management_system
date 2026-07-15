<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFooterSettingsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('footer_settings')) {
            return;
        }

        Schema::create('footer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->text('value_traditional')->nullable();
            $table->text('value_simplified')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('footer_settings');
    }
}
