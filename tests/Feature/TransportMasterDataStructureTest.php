<?php

namespace Tests\Feature;

use Tests\TestCase;

class TransportMasterDataStructureTest extends TestCase
{
    public function test_transport_master_data_uses_existing_models_shared_crud_and_canonical_views(): void
    {
        $typeModel = file_get_contents(app_path('Models/TransportType.php'));
        $brandModel = file_get_contents(app_path('Models/TransportBrand.php'));
        $typeController = file_get_contents(app_path('Http/Controllers/TransportTypeController.php'));
        $brandController = file_get_contents(app_path('Http/Controllers/TransportBrandController.php'));
        $service = file_get_contents(app_path('Services/Transports/TransportMasterDataService.php'));
        $routes = file_get_contents(base_path('routes/web.php'));
        $navigation = file_get_contents(resource_path('views/backend/partials/left-navbar.blade.php'));
        $indexView = file_get_contents(resource_path('views/backend/operations/transport-master-data/index.blade.php'));
        $transportRequest = file_get_contents(app_path('Http/Requests/Backend/Operations/Transports/StoreTransportAdminRequest.php'));

        foreach (['public function transports()', 'hasMany(Transports::class'] as $pattern) {
            $this->assertStringContainsString($pattern, $typeModel);
            $this->assertStringContainsString($pattern, $brandModel);
        }

        foreach (['TransportMasterDataService', 'withCount', 'cannot be renamed', 'still used'] as $pattern) {
            $this->assertStringContainsString($pattern, $service);
        }

        foreach ([
            'admin.transport-types.index',
            'admin.transport-types.store',
            'admin.transport-types.update',
            'admin.transport-types.destroy',
            'admin.transport-brands.index',
            'admin.transport-brands.store',
            'admin.transport-brands.update',
            'admin.transport-brands.destroy',
        ] as $routeName) {
            $this->assertStringContainsString($routeName, $routes);
        }

        foreach (['transport-types.index', 'transport-brands.index'] as $routeFragment) {
            $this->assertStringContainsString($routeFragment, $navigation);
        }

        foreach (['x-backend.page-hero', 'x-backend.breadcrumb-toolbar', 'backend-panel', 'backend-form', 'backend-form-control', 'backend-modal', 'backend-modal__footer', 'x-backend.modal-close', 'CreateModal', 'EditModal'] as $pattern) {
            $this->assertStringContainsString($pattern, $indexView);
        }

        foreach (['backend-filter-control', 'backend-table-actions', 'data-transport-delete'] as $pattern) {
            $this->assertStringContainsString($pattern, $indexView);
        }

        $this->assertFileDoesNotExist(resource_path('views/backend/operations/transport-master-data/form.blade.php'));
        $this->assertStringNotContainsString("->name('admin.transport-types.create')", $routes);
        $this->assertStringNotContainsString("->name('admin.transport-types.edit')", $routes);
        $this->assertStringNotContainsString("->name('admin.transport-brands.create')", $routes);
        $this->assertStringNotContainsString("->name('admin.transport-brands.edit')", $routes);

        foreach (["Rule::exists('transport_types', 'type')", "Rule::exists('transport_brands', 'brand')"] as $pattern) {
            $this->assertStringContainsString($pattern, $transportRequest);
        }

        $this->assertStringContainsString('TransportMasterDataService', $typeController . $brandController);
        $this->assertStringNotContainsString('public function create', $typeController . $brandController);
        $this->assertStringNotContainsString('public function edit', $typeController . $brandController);
    }
}
