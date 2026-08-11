<?php

namespace Tests\Feature\Pricing;

use App\Http\Requests\Backend\Operations\Tours\StoreTourPriceAdminRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as LaravelValidator;
use Tests\TestCase;

class TourPriceDataActivationValidationTest extends TestCase
{
    public function test_operator_rules_do_not_expose_internal_status_or_verification_fields(): void
    {
        $rules = (new StoreTourPriceAdminRequest())->rules();

        $this->assertArrayNotHasKey('pricing_data_status', $rules);
        $this->assertArrayNotHasKey('status', $rules);
        $this->assertArrayNotHasKey('markup_currency', $rules);
        $this->assertArrayNotHasKey('markup_source', $rules);
        $this->assertArrayNotHasKey('markup_verified_at', $rules);
        $this->assertArrayNotHasKey('markup_verified_by', $rules);
    }

    public function test_simplified_price_requires_only_complete_commercial_inputs(): void
    {
        $validator = $this->validator($this->payload([
            'contract_rate_idr' => null,
            'markup_type' => null,
            'markup_amount' => null,
            'valid_from' => null,
            'valid_until' => null,
        ]));

        $this->assertTrue($validator->fails());
        $this->assertEqualsCanonicalizing([
            'contract_rate_idr',
            'markup_type',
            'markup_amount',
            'valid_from',
            'valid_until',
        ], array_keys($validator->errors()->toArray()));
    }

    public function test_percentage_usd_and_idr_markup_inputs_are_supported(): void
    {
        foreach ([
            ['markup_type' => 'percentage', 'markup_amount' => '12.5'],
            ['markup_type' => 'usd', 'markup_amount' => '20.50'],
            ['markup_type' => 'idr', 'markup_amount' => '250000'],
        ] as $markup) {
            $validator = $this->validator($this->payload($markup));

            $this->assertFalse($validator->fails(), $validator->errors()->toJson());
        }
    }

    public function test_markup_type_specific_precision_is_enforced(): void
    {
        $percentage = $this->validator($this->payload([
            'markup_type' => 'percentage',
            'markup_amount' => '12.345',
        ]));
        $usd = $this->validator($this->payload([
            'markup_type' => 'usd',
            'markup_amount' => '20.001',
        ]));
        $idr = $this->validator($this->payload([
            'markup_type' => 'idr',
            'markup_amount' => '250000.50',
        ]));

        $this->assertTrue($percentage->errors()->has('markup_amount'));
        $this->assertTrue($usd->errors()->has('markup_amount'));
        $this->assertTrue($idr->errors()->has('markup_amount'));
    }

    public function test_invalid_money_dates_pax_and_markup_type_are_rejected(): void
    {
        $validator = $this->validator($this->payload([
            'min_qty' => 0,
            'max_qty' => -1,
            'contract_rate_idr' => 0,
            'markup_type' => 'unknown',
            'markup_amount' => '-10.00',
            'valid_from' => '2026-12-31',
            'valid_until' => '2026-01-01',
        ]));

        foreach (['min_qty', 'max_qty', 'contract_rate_idr', 'markup_type', 'markup_amount', 'valid_until'] as $field) {
            $this->assertTrue($validator->errors()->has($field), "Expected {$field} to fail validation.");
        }
    }

    public function test_only_authorized_admin_positions_can_mutate_tour_prices(): void
    {
        $authorized = new StoreTourPriceAdminRequest();
        $authorized->setUserResolver(fn () => (new User())->forceFill([
            'type' => 'admin',
            'position' => 'developer',
        ]));

        $unauthorized = new StoreTourPriceAdminRequest();
        $unauthorized->setUserResolver(fn () => (new User())->forceFill([
            'type' => 'admin',
            'position' => 'staff',
        ]));

        $this->assertTrue($authorized->authorize());
        $this->assertFalse($unauthorized->authorize());
    }

    private function validator(array $payload): LaravelValidator
    {
        $request = TourPriceValidationRequestForTest::create('/', 'POST', $payload);
        $request->normalizeForTest();
        $validator = Validator::make($request->all(), $request->rules());
        $request->attachAfterValidationForTest($validator);

        return $validator;
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'min_qty' => 2,
            'max_qty' => 4,
            'contract_rate_idr' => 1_000_000,
            'markup_type' => 'percentage',
            'markup_amount' => '12.5',
            'valid_from' => '2026-08-01',
            'valid_until' => '2026-12-31',
        ], $overrides);
    }
}

class TourPriceValidationRequestForTest extends StoreTourPriceAdminRequest
{
    public function normalizeForTest(): void
    {
        $this->prepareForValidation();
    }

    public function attachAfterValidationForTest(LaravelValidator $validator): void
    {
        $this->withValidator($validator);
    }
}
