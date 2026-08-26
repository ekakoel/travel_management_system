<?php

namespace Tests\Feature;

use Tests\TestCase;

class TransportPriceSchemaContractTest extends TestCase
{
    public function test_transport_price_write_contract_does_not_reference_missing_name_column(): void
    {
        $migration = file_get_contents(base_path('database/migrations/2022_12_23_104043_create_transport_prices_table.php'));
        $model = file_get_contents(app_path('Models/TransportPrice.php'));
        $request = file_get_contents(app_path('Http/Requests/Backend/Operations/Transports/StoreTransportPriceAdminRequest.php'));
        $service = file_get_contents(app_path('Services/Transports/TransportPricingService.php'));
        $form = file_get_contents(resource_path('views/backend/operations/transports/partials/price-fields.blade.php'));
        $detail = file_get_contents(resource_path('views/backend/operations/transports/detail.blade.php'));

        foreach ([$migration, $model, $request, $service, $form, $detail] as $source) {
            $this->assertStringNotContainsString("'name'", $source);
            $this->assertStringNotContainsString('price->name', $source);
        }

        foreach (['transports_id', 'type', 'src', 'dst', 'duration', 'contract_rate', 'markup', 'extra_time', 'additional_info', 'author_id'] as $column) {
            $this->assertStringContainsString("'{$column}'", $model);
            $this->assertStringContainsString("'{$column}'", $service);
        }
    }
}
