<?php

namespace Tests\Feature;

use App\Http\Controllers\Backend\Operations\Tours\TourAdminController;
use Tests\TestCase;

class BackendTourPackageAccessTest extends TestCase
{
    public function test_backend_tour_package_index_uses_the_canonical_route_contract(): void
    {
        $route = app('router')->getRoutes()->getByName('admin.tour-packages.index');

        $this->assertNotNull($route);
        $this->assertSame('tour-package-admin', $route->uri());
        $this->assertSame(TourAdminController::class.'@index', $route->getActionName());
        $this->assertContains('can:isAdmin', $route->gatherMiddleware());
    }

    public function test_sidebar_maps_tour_routes_explicitly_instead_of_guessing_from_service_slug(): void
    {
        $sidebar = file_get_contents(resource_path('views/backend/partials/left-navbar.blade.php'));

        $this->assertStringContainsString("'public_route' => 'view.tour-packages-service'", $sidebar);
        $this->assertStringContainsString("'admin_route' => 'admin.tour-packages.index'", $sidebar);
        $this->assertStringNotContainsString("route(\"admin.\".\"\$menuadmin->nicname\".\".index\")", $sidebar);
        $this->assertStringNotContainsString("route('view.'.\$femenu->nicname.'-service')", $sidebar);
    }

    public function test_tour_inventory_read_is_not_restricted_to_authoring_positions(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Backend/Operations/Tours/TourAdminController.php'));
        preg_match('/public function index\(.*?\n    }\n\n    public function show/s', $controller, $matches);

        $this->assertNotEmpty($matches);
        $this->assertStringNotContainsString("Gate::allows('posDev')", $matches[0]);
        $this->assertStringContainsString("view('backend.operations.tours.index'", $matches[0]);
    }
}
