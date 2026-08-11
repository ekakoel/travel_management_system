<?php

namespace Tests\Feature\Navigation;

use App\Services\FooterContentService;
use App\Services\Navigation\BackendNavigationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FooterServiceRegistryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:') {
            $this->markTestSkipped('Footer registry regression test requires isolated SQLite in-memory.');
        }

        $this->createSchema();
        Cache::flush();
    }

    protected function tearDown(): void
    {
        Cache::flush();

        parent::tearDown();
    }

    public function test_footer_services_follow_the_active_registry_without_waiting_for_static_footer_cache(): void
    {
        DB::table('business_profiles')->insert([
            'profile_key' => 'primary',
            'name' => 'Bali Kami Tour',
        ]);
        DB::table('services')->insert([
            ['name' => 'Hotels', 'nicname' => 'hotels', 'icon' => 'fas fa-hotel', 'status' => 'Active'],
            ['name' => 'Tour Packages', 'nicname' => 'tour-packages', 'icon' => 'fas fa-route', 'status' => 'Active'],
            ['name' => 'Activities', 'nicname' => 'activities', 'icon' => 'fas fa-hiking', 'status' => 'Draft'],
        ]);
        DB::table('footer_links')->insert([
            [
                'group' => 'services',
                'label' => 'Legacy Activities Link',
                'route_name' => 'view.activities-service',
                'sort_order' => 1,
                'open_new_tab' => false,
                'status' => true,
            ],
            [
                'group' => 'quick_links',
                'label' => 'About Us',
                'route_name' => 'about-us',
                'sort_order' => 1,
                'open_new_tab' => false,
                'status' => true,
            ],
        ]);

        $firstFooter = app(FooterContentService::class)->data();

        $this->assertSame(
            ['Hotels', 'Tour Packages'],
            collect($this->section($firstFooter, 'services')['links'])->pluck('label')->all()
        );
        $this->assertFalse(
            collect($this->section($firstFooter, 'services')['links'])->contains('label', 'Legacy Activities Link')
        );
        $this->assertTrue(
            collect($this->section($firstFooter, 'quick_links')['links'])->contains('label', 'About Us')
        );
        $this->assertTrue(Cache::has('footer_content.en'));

        DB::table('services')->where('nicname', 'hotels')->update(['status' => 'Draft']);
        app()->forgetInstance(BackendNavigationService::class);
        app()->forgetInstance(FooterContentService::class);

        $secondFooter = app(FooterContentService::class)->data();

        $this->assertSame(
            ['Tour Packages'],
            collect($this->section($secondFooter, 'services')['links'])->pluck('label')->all()
        );
        $this->assertTrue(Cache::has('footer_content.en'));
    }

    private function section(array $footer, string $key): array
    {
        return collect($footer['link_sections'])->firstWhere('key', $key);
    }

    private function createSchema(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nicname');
            $table->text('icon');
            $table->string('status');
        });
        Schema::create('footer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->text('value_traditional')->nullable();
            $table->text('value_simplified')->nullable();
            $table->boolean('status')->default(true);
        });
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
        });
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('profile_key')->unique();
            $table->string('name');
        });
    }
}
