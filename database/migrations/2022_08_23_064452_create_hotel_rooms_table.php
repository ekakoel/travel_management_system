<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelRoomsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hotel_rooms')) {
            return;
        }

        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId("hotels_id")->constrained("hotels")->onDelete("cascade");
            $table->string("cover");
            $table->string("rooms");
            $table->integer("capacity_adult");
            $table->integer("capacity_child");
            $table->text("view")->nullable();
            $table->text("beds")->nullable();
            $table->text("size")->nullable();
            $table->longText("include")->nullable();
            $table->longText("amenities")->nullable();
            $table->longText("amenities_traditional")->nullable();
            $table->longText("amenities_simplified")->nullable();
            $table->longText("additional_info")->nullable();
            $table->longText("additional_info_traditional")->nullable();
            $table->longText("additional_info_simplified")->nullable();
            $table->string("status");
            $table->timestamps();
        });

        if (Schema::hasTable('hotel_prices') && Schema::hasColumn('hotel_prices', 'rooms_id')) {
            Schema::table('hotel_prices', function (Blueprint $table) {
                $table->foreign('rooms_id', 'hotel_prices_rooms_id_foreign')
                    ->references('id')
                    ->on('hotel_rooms')
                    ->cascadeOnDelete();
            });
        }
    }
    public function down()
    {
        if (Schema::hasTable('hotel_prices') && Schema::hasColumn('hotel_prices', 'rooms_id')) {
            Schema::table('hotel_prices', function (Blueprint $table) {
                $table->dropForeign('hotel_prices_rooms_id_foreign');
            });
        }

        Schema::dropIfExists('hotel_rooms');
    }
}
