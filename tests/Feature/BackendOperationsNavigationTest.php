<?php

namespace Tests\Feature;

use Tests\TestCase;

class BackendOperationsNavigationTest extends TestCase
{
    public function test_sidebar_groups_order_reservation_and_invoice_navigation(): void
    {
        $sidebar = file_get_contents(resource_path('views/backend/partials/left-navbar.blade.php'));
        $navigationService = file_get_contents(app_path('Services/Navigation/BackendNavigationService.php'));

        $this->assertStringContainsString("@canany(['posDev','posRsv','weddingRsv'])", $sidebar);
        $this->assertStringContainsString("@lang('messages.Operations')", $sidebar);
        $this->assertStringContainsString("route('admin.order.index')", $sidebar);
        $this->assertStringContainsString("route('view.reservation')", $sidebar);
        $this->assertStringContainsString("route('admin.invoices.index')", $sidebar);
        $this->assertStringContainsString('id="operations-submenu"', $sidebar);
        $this->assertStringContainsString("\$operationsNavigationActive ? 'show' : ''", $sidebar);
        $this->assertStringContainsString("request->routeIs('admin.invoices.*')", $navigationService);
        $this->assertStringContainsString("request->routeIs('view.reservation*'", $navigationService);
        $this->assertStringContainsString("request->routeIs('admin.order.*'", $navigationService);
        $this->assertStringContainsString("__('messages.Pending Orders')", $sidebar);
        $this->assertSame(1, substr_count($sidebar, "route('admin.order.index')"));
        $this->assertStringContainsString("\$backendNavigation['pendingCounts']['operations']", $sidebar);
        $this->assertStringNotContainsString('::where(', $sidebar);
        $this->assertStringNotContainsString('Schema::', $sidebar);
    }

    public function test_operations_navigation_copy_exists_in_every_active_locale(): void
    {
        $english = require resource_path('lang/en/messages.php');
        $traditional = require resource_path('lang/zh/messages.php');
        $simplified = require resource_path('lang/zh-CN/messages.php');

        $this->assertSame('Operations', $english['Operations']);
        $this->assertSame('營運管理', $traditional['Operations']);
        $this->assertSame('运营管理', $simplified['Operations']);
        $this->assertArrayHasKey('Orders', $english);
        $this->assertArrayHasKey('Reservations', $english);
        $this->assertArrayHasKey('Invoices', $english);
    }

    public function test_operations_navigation_targets_registered_get_routes(): void
    {
        $routes = app('router')->getRoutes();

        foreach (['admin.order.index', 'view.reservation', 'admin.invoices.index'] as $routeName) {
            $route = $routes->getByName($routeName);

            $this->assertNotNull($route, "Missing operations navigation route: {$routeName}");
            $this->assertContains('GET', $route->methods());
        }
    }
}
