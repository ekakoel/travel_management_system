<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnforceUniqueBusinessProfileAndFooterData extends Migration
{
    public function up()
    {
        $this->prepareBusinessProfiles();
        $this->prepareFooterSettings();
        $this->prepareFooterLinks();
    }

    public function down()
    {
        if (Schema::hasTable('footer_links')) {
            Schema::table('footer_links', function (Blueprint $table) {
                $table->dropUnique('footer_links_group_label_unique');
            });
        }

        if (Schema::hasTable('footer_settings')) {
            Schema::table('footer_settings', function (Blueprint $table) {
                $table->dropUnique('footer_settings_key_unique_guard');
            });
        }

        if (Schema::hasTable('business_profiles')) {
            Schema::table('business_profiles', function (Blueprint $table) {
                $table->dropUnique('business_profiles_profile_key_unique');
                $table->dropColumn('profile_key');
            });
        }
    }

    protected function prepareBusinessProfiles(): void
    {
        if (!Schema::hasTable('business_profiles')) {
            return;
        }

        if (!Schema::hasColumn('business_profiles', 'profile_key')) {
            Schema::table('business_profiles', function (Blueprint $table) {
                $table->string('profile_key', 50)->nullable()->after('id');
            });
        }

        $profiles = DB::table('business_profiles')->orderBy('id')->get(['id']);

        foreach ($profiles as $index => $profile) {
            DB::table('business_profiles')
                ->where('id', $profile->id)
                ->update([
                    'profile_key' => $index === 0 ? 'primary' : 'legacy-'.$profile->id,
                ]);
        }

        DB::statement("ALTER TABLE `business_profiles` MODIFY `profile_key` VARCHAR(50) NOT NULL DEFAULT 'primary'");
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->unique('profile_key', 'business_profiles_profile_key_unique');
        });
    }

    protected function prepareFooterSettings(): void
    {
        if (!Schema::hasTable('footer_settings')) {
            return;
        }

        $duplicates = DB::table('footer_settings')
            ->select('key', DB::raw('MIN(id) as keep_id'))
            ->groupBy('key')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('footer_settings')
                ->where('key', $duplicate->key)
                ->where('id', '<>', $duplicate->keep_id)
                ->delete();
        }

        if (!$this->indexExists('footer_settings', 'footer_settings_key_unique_guard')) {
            Schema::table('footer_settings', function (Blueprint $table) {
                $table->unique('key', 'footer_settings_key_unique_guard');
            });
        }
    }

    protected function prepareFooterLinks(): void
    {
        if (!Schema::hasTable('footer_links')) {
            return;
        }

        $duplicates = DB::table('footer_links')
            ->select('group', 'label', DB::raw('MIN(id) as keep_id'))
            ->groupBy('group', 'label')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('footer_links')
                ->where('group', $duplicate->group)
                ->where('label', $duplicate->label)
                ->where('id', '<>', $duplicate->keep_id)
                ->delete();
        }

        Schema::table('footer_links', function (Blueprint $table) {
            $table->unique(['group', 'label'], 'footer_links_group_label_unique');
        });
    }

    protected function indexExists(string $table, string $index): bool
    {
        $database = DB::getDatabaseName();
        $result = DB::selectOne(
            'SELECT COUNT(1) as aggregate FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$database, $table, $index]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }
}
