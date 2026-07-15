<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFooterLinksTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('footer_links')) {
            return;
        }

        Schema::create('footer_links', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('label');
            $table->string('label_traditional')->nullable();
            $table->string('label_simplified')->nullable();
            $table->string('route_name')->nullable();
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('open_new_tab')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('footer_links');
    }
}
