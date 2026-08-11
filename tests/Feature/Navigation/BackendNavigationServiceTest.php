<?php

namespace Tests\Feature\Navigation;

use App\Models\User;
use App\Services\Navigation\BackendNavigationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BackendNavigationServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:') {
            $this->markTestSkipped('Navigation query regression test requires isolated SQLite in-memory.');
        }

        $this->createSchema();
        Carbon::setTestNow('2026-08-11 10:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_it_builds_shared_badges_once_per_request(): void
    {
        DB::table('services')->insert([
            'name' => 'Tour Packages',
            'nicname' => 'tour-packages',
            'icon' => 'fas fa-route',
            'status' => 'Active',
        ]);
        DB::table('services')->insert([
            'name' => 'Activities',
            'nicname' => 'activities',
            'icon' => 'fas fa-hiking',
            'status' => 'Draft',
        ]);
        DB::table('promotions')->insert([
            'name' => 'August',
            'discounts' => 10,
            'periode_start' => '2026-08-01 00:00:00',
            'periode_end' => '2026-08-31 23:59:59',
            'status' => 'Active',
        ]);
        DB::table('orders')->insert([
            ['user_id' => 7, 'status' => 'Draft', 'checkin' => '2026-08-20 00:00:00'],
            ['user_id' => 7, 'status' => 'Approved', 'checkin' => '2026-08-21 00:00:00'],
            ['user_id' => 8, 'status' => 'Pending', 'checkin' => '2026-08-22 00:00:00'],
            ['user_id' => 8, 'status' => 'Pending', 'checkin' => '2026-08-01 00:00:00'],
        ]);
        DB::table('order_weddings')->insert([
            [
                'agent_id' => 7,
                'status' => 'Draft',
                'checkin' => '2026-08-20 00:00:00',
                'wedding_date' => '2026-08-25 00:00:00',
            ],
            [
                'agent_id' => 8,
                'status' => 'Pending',
                'checkin' => '2026-08-20 00:00:00',
                'wedding_date' => '2026-08-26 00:00:00',
            ],
        ]);

        $user = new User();
        $user->forceFill([
            'id' => 7,
            'position' => 'developer',
            'status' => 'Active',
            'is_approved' => true,
        ]);
        $request = Request::create('/orders-admin', 'GET');
        $request->setUserResolver(fn () => $user);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $service = app(BackendNavigationService::class);
        $globalServices = $service->navigationItems();
        $first = $service->data($request);
        $queriesAfterFirstResolution = count(DB::getQueryLog());
        $second = $service->data($request);

        $this->assertSame(1, $first['orderCounts']['Draft']);
        $this->assertSame(1, $first['orderCounts']['Approved']);
        $this->assertSame(1, $first['weddingOrderCounts']['Draft']);
        $this->assertSame(1, $first['pendingCounts']['tour']);
        $this->assertSame(1, $first['pendingCounts']['wedding']);
        $this->assertSame(2, $first['pendingCounts']['operations']);
        $this->assertCount(1, $first['services']);
        $this->assertSame($globalServices, $first['services']);
        $this->assertSame('tour-packages', $first['services']->first()['canonical_slug']);
        $this->assertSame('view.tour-packages-service', $first['services']->first()['public_route']);
        $this->assertSame('admin.tour-packages.index', $first['services']->first()['admin_route']);
        $this->assertCount(1, $first['promotions']);
        $this->assertSame($first, $second);
        $this->assertLessThanOrEqual(13, $queriesAfterFirstResolution);
        $this->assertSame($queriesAfterFirstResolution, count(DB::getQueryLog()));
    }

    public function test_navigation_blades_do_not_execute_database_queries(): void
    {
        foreach ([
            resource_path('views/component/menu.blade.php'),
            resource_path('views/backend/partials/left-navbar.blade.php'),
        ] as $view) {
            $contents = file_get_contents($view);

            $this->assertStringNotContainsString('::where(', $contents);
            $this->assertStringNotContainsString('::query(', $contents);
            $this->assertStringNotContainsString('Schema::', $contents);
        }
    }

    private function createSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_approved')->default(true);
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('status');
            $table->dateTime('checkin');
        });
        Schema::create('order_weddings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id');
            $table->string('status');
            $table->dateTime('checkin');
            $table->dateTime('wedding_date');
        });
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nicname');
            $table->string('icon');
            $table->string('status');
        });
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('discounts', 10, 2);
            $table->dateTime('periode_start');
            $table->dateTime('periode_end');
            $table->string('status');
        });
    }
}
