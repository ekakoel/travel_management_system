<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeMapColumnTypeOnBusinessProfilesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('business_profiles') || !Schema::hasColumn('business_profiles', 'map')) {
            return;
        }

        DB::statement('ALTER TABLE `business_profiles` MODIFY `map` TEXT NULL');
    }

    public function down()
    {
        if (!Schema::hasTable('business_profiles') || !Schema::hasColumn('business_profiles', 'map')) {
            return;
        }

        DB::statement('ALTER TABLE `business_profiles` MODIFY `map` VARCHAR(255) NULL');
    }
}
