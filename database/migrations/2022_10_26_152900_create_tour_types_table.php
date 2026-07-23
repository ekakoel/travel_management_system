<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourTypesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('tour_types')) {
            return;
        }

        Schema::create('tour_types', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('type_traditional')->nullable();
            $table->string('type_simplified')->nullable();
            $table->longText('description')->nullable();
            $table->longText('description_traditional')->nullable();
            $table->longText('description_simplified')->nullable();
            $table->timestamps();
        });

        if (Schema::hasTable('tours') && Schema::hasColumn('tours', 'type_id')) {
            Schema::table('tours', function (Blueprint $table) {
                $table->foreign('type_id', 'tours_type_id_foreign')
                    ->references('id')
                    ->on('tour_types')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('tours') && Schema::hasColumn('tours', 'type_id')) {
            Schema::table('tours', function (Blueprint $table) {
                $table->dropForeign('tours_type_id_foreign');
            });
        }

        Schema::dropIfExists('tour_types');
    }
}
