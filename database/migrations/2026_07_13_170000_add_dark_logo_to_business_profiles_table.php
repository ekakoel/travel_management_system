<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDarkLogoToBusinessProfilesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('business_profiles')) {
            return;
        }

        Schema::table('business_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('business_profiles', 'logo_dark')) {
                $table->string('logo_dark')->nullable()->after('logo');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('business_profiles')) {
            return;
        }

        Schema::table('business_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('business_profiles', 'logo_dark')) {
                $table->dropColumn('logo_dark');
            }
        });
    }
}
