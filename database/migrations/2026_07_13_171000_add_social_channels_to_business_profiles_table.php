<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialChannelsToBusinessProfilesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('business_profiles')) {
            return;
        }

        Schema::table('business_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('business_profiles', 'youtube')) {
                $table->string('youtube')->nullable()->after('twitter');
            }

            if (!Schema::hasColumn('business_profiles', 'linkedin')) {
                $table->string('linkedin')->nullable()->after('youtube');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('business_profiles')) {
            return;
        }

        Schema::table('business_profiles', function (Blueprint $table) {
            foreach (['linkedin', 'youtube'] as $column) {
                if (Schema::hasColumn('business_profiles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
