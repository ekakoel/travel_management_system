<?php

namespace Tests\Feature\Pricing;

use App\Http\Requests\Backend\Operations\Tours\StoreTourPriceAdminRequest;
use App\Models\TourPrices;
use App\Support\CanonicalDateInput;
use App\Support\CanonicalDecimalInput;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TourPriceCrudDateContractTest extends TestCase
{
    public function test_canonical_and_known_legacy_picker_dates_normalize_to_database_format(): void
    {
        $this->assertSame('2026-07-31', CanonicalDateInput::normalize('2026-07-31'));
        $this->assertSame('2026-07-31', CanonicalDateInput::normalize('31 July 2026'));
        $this->assertSame('2026-07-31', CanonicalDateInput::normalize('31 Jul 2026'));
        $this->assertNull(CanonicalDateInput::normalize('  '));
    }

    public function test_ambiguous_or_invalid_dates_are_not_guessed(): void
    {
        $this->assertSame('07/08/2026', CanonicalDateInput::normalize('07/08/2026'));
        $this->assertSame('31 February 2026', CanonicalDateInput::normalize('31 February 2026'));
    }

    public function test_markup_decimal_storage_uses_minimal_numeric_representation(): void
    {
        $this->assertSame('20', CanonicalDecimalInput::normalize('20.000000'));
        $this->assertSame('20.5', CanonicalDecimalInput::normalize('20.50'));
        $this->assertSame('20.25', CanonicalDecimalInput::normalize('20.25'));
        $this->assertSame('250000', CanonicalDecimalInput::normalize('250000'));
    }

    public function test_tour_price_model_canonicalizes_markup_before_persistence(): void
    {
        $price = new TourPrices();
        $price->markup_amount = '20.500000';

        $this->assertSame('20.5', $price->getAttributes()['markup_amount']);
    }

    public function test_store_and_update_request_normalize_legacy_picker_payload_before_validation(): void
    {
        $request = TourPriceDateRequestForTest::create('/', 'POST', [
            'min_qty' => 1,
            'max_qty' => 5,
            'contract_rate_idr' => '1,000,000',
            'markup_amount' => '20,00',
            'markup_currency' => 'USD',
            'valid_from' => '01 August 2026',
            'valid_until' => '31 December 2026',
        ]);
        $request->normalizeForTest();

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertFalse($validator->fails(), $validator->errors()->toJson());
        $this->assertSame('2026-08-01', $request->input('valid_from'));
        $this->assertSame('2026-12-31', $request->input('valid_until'));
        $this->assertSame('1000000', $request->input('contract_rate_idr'));
        $this->assertSame('20', $request->input('markup_amount'));
        $this->assertSame('usd', $request->input('markup_type'));
    }

    public function test_tour_price_blade_uses_canonical_backend_picker_not_legacy_date_picker(): void
    {
        $view = file_get_contents(resource_path('views/backend/operations/tours/partials/price-fields.blade.php'));
        $detailView = file_get_contents(resource_path('views/backend/operations/tours/detail.blade.php'));
        $backendJs = file_get_contents(resource_path('backend/js/app.js'));
        $detailJs = file_get_contents(resource_path('backend/js/operations/tours/detail.js'));

        $this->assertStringNotContainsString('class="backend-form-control date-picker', $view);
        $this->assertSame(2, substr_count($view, 'data-backend-picker="date"'));
        $this->assertSame(2, substr_count($view, 'data-backend-picker-format="yyyy-mm-dd"'));
        $this->assertStringContainsString("dateFormat: input.data('backend-picker-format') || 'yyyy-mm-dd'", $backendJs);
        $this->assertStringContainsString('name="_tour_price_form_context"', $view);
        $this->assertStringContainsString("old('_tour_price_form_context') === \$formContext", $view);
        $this->assertStringContainsString("'formContext' => 'update:'.\$price->id", $detailView);
        $this->assertStringContainsString("'formContext' => 'create'", $detailView);
        $this->assertStringContainsString('/^update:(\\d+)$/.exec(priceFormContext)', $detailJs);
        $this->assertStringContainsString('name="markup_type"', $view);
        $this->assertStringNotContainsString('name="pricing_data_status"', $view);
        $this->assertStringContainsString('[data-tour-markup-type]', $detailJs);
        $this->assertStringNotContainsString('{{ $price->status }}', $detailView);
        $this->assertStringContainsString("number_format((float) \$editingPrice->markup_amount, 2, '.', '')", $view);
        $this->assertStringContainsString('step="0.01"', $view);
    }

    public function test_tour_detail_excludes_soft_deleted_prices_from_its_source_collection(): void
    {
        $inventory = file_get_contents(app_path('Services/Tours/TourInventoryService.php'));
        $viewModel = file_get_contents(app_path('ViewModels/Tours/TourDetailViewModel.php'));
        $detailView = file_get_contents(resource_path('views/backend/operations/tours/detail.blade.php'));

        $this->assertStringNotContainsString(
            "'prices' => fn (\$query) => \$query->withTrashed()",
            $inventory
        );
        $this->assertStringContainsString(
            "->reject(fn (\$price) => \$price->trashed())",
            $viewModel
        );
        $this->assertStringNotContainsString("if (\$price->trashed())", $detailView);
        $this->assertStringNotContainsString("unless (\$price->trashed())", $detailView);
    }
}

class TourPriceDateRequestForTest extends StoreTourPriceAdminRequest
{
    public function normalizeForTest(): void
    {
        $this->prepareForValidation();
    }
}
