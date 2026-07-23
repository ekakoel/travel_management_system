<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpkIdToGuestsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('guests') || Schema::hasColumn('guests', 'spk_id')) {
            return;
        }

        Schema::table('guests', function (Blueprint $table) {
            $table->unsignedBigInteger('spk_id')->nullable()->after('id')->index();
        });
    }

    public function down()
    {
        if (!Schema::hasTable('guests') || !Schema::hasColumn('guests', 'spk_id')) {
            return;
        }

        Schema::table('guests', function (Blueprint $table) {
            $table->dropIndex(['spk_id']);
            $table->dropColumn('spk_id');
        });
    }
}
