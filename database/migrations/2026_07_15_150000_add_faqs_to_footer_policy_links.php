<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('footer_links')) {
            return;
        }

        $now = now();

        DB::table('footer_links')->updateOrInsert(
            [
                'group' => 'policies',
                'label' => 'FAQs',
            ],
            [
                'route_name' => 'faq',
                'url' => null,
                'icon' => null,
                'sort_order' => 30,
                'open_new_tab' => false,
                'status' => true,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        $this->clearFooterCache();
    }

    public function down(): void
    {
        if (!Schema::hasTable('footer_links')) {
            return;
        }

        DB::table('footer_links')
            ->where('group', 'policies')
            ->where('label', 'FAQs')
            ->where('route_name', 'faq')
            ->delete();

        $this->clearFooterCache();
    }

    private function clearFooterCache(): void
    {
        foreach (['en', 'zh', 'zh-CN'] as $locale) {
            Cache::forget("footer_content.$locale");
        }
    }
};
