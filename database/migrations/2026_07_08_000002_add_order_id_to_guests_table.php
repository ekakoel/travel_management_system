<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderIdToGuestsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('guests') || Schema::hasColumn('guests', 'order_id')) {
            return;
        }

        Schema::table('guests', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->after('order_wedding_id')->index();
        });
    }

    public function down()
    {
        if (!Schema::hasTable('guests') || !Schema::hasColumn('guests', 'order_id')) {
            return;
        }

        Schema::table('guests', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropColumn('order_id');
        });
    }
}
