<?php

namespace Tests\Feature\Pricing;

use App\Http\Requests\Backend\Operations\Tours\UpdateTourPriceAdminRequest;
use App\Models\TourPrices;
use App\Models\Tours;
use App\Exceptions\PricingException;
use App\Services\Tours\TourPackagePricingService;
use App\Services\Tours\TourPriceOverlapValidator;
use App\Services\Tours\TourPricingService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as LaravelValidator;
use Illuminate\Validation\ValidationException;
use Carbon\CarbonImmutable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class TourPriceCrudServiceTest extends TestCase
{
    private TourPricingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:') {
            $this->fail('Tour Price CRUD tests require SQLite :memory:.');
        }

        Schema::dropIfExists('invoice_admins');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('tour_prices');
        Schema::dropIfExists('tours');
        $this->createSchema();
        $this->service = new TourPricingService(new TourPriceOverlapValidator());
    }

    public function test_create_automatically_activates_a_complete_price(): void
    {
        $price = $this->service->createPrice($this->tour(), $this->payload(), 99);

        $this->assertSame(TourPrices::STATUS_READY, $price->pricing_data_status);
        $this->assertSame('Active', $price->status);
        $this->assertSame(TourPrices::MARKUP_TYPE_USD, $price->markup_type);
        $this->assertSame('20', $price->markup_amount);
        $this->assertSame('20', DB::table('tour_prices')->where('id', $price->id)->value('markup_amount'));
        $this->assertNotNull($price->markup_verified_at);
        $this->assertSame(1, TourPrices::query()->readyForTravel('2026-09-01')->count());
    }

    public function test_create_sets_verification_and_is_quote_master_eligible(): void
    {
        $price = $this->service->createPrice($this->tour(), $this->payload(), 99);

        $this->assertSame(TourPrices::STATUS_READY, $price->pricing_data_status);
        $this->assertSame('Active', $price->status);
        $this->assertSame(99, (int) $price->markup_verified_by);
        $this->assertNotNull($price->markup_verified_at);
        $this->assertSame(1, TourPrices::query()->readyForTravel('2026-09-01')->count());
    }

    public function test_create_supports_all_markup_types_and_derives_internal_currency(): void
    {
        foreach ([
            TourPrices::MARKUP_TYPE_PERCENTAGE => 'IDR',
            TourPrices::MARKUP_TYPE_USD => 'USD',
            TourPrices::MARKUP_TYPE_IDR => 'IDR',
        ] as $markupType => $expectedCurrency) {
            $price = $this->service->createPrice($this->tour(), $this->payload([
                'markup_type' => $markupType,
                'markup_amount' => $markupType === TourPrices::MARKUP_TYPE_IDR ? '250000' : '12.50',
            ]), 99);

            $this->assertSame($markupType, $price->markup_type);
            $this->assertSame($expectedCurrency, $price->markup_currency);
            $this->assertSame('admin-crud:'.$markupType, $price->markup_source);
            $this->assertSame(TourPrices::STATUS_READY, $price->pricing_data_status);
        }
    }

    public function test_overlapping_ready_pax_and_date_range_is_rejected(): void
    {
        $tour = $this->tour();
        $this->service->createPrice($tour, $this->payload(), 99);

        $this->expectException(ValidationException::class);
        $this->service->createPrice($tour, $this->payload([
            'min_qty' => 4,
            'max_qty' => 8,
            'valid_from' => '2026-09-01',
            'valid_until' => '2026-10-31',
        ]), 99);
    }

    public function test_non_overlapping_period_does_not_block_another_price(): void
    {
        $tour = $this->tour();
        $first = $this->service->createPrice($tour, $this->payload(), 99);
        $ready = $this->service->createPrice($tour, $this->payload([
            'valid_from' => '2027-01-01',
            'valid_until' => '2027-12-31',
        ]), 99);

        $this->assertSame(TourPrices::STATUS_READY, $first->pricing_data_status);
        $this->assertSame(TourPrices::STATUS_READY, $ready->pricing_data_status);
    }

    public function test_cross_tour_update_is_rejected(): void
    {
        $tour = $this->tour();
        $otherTour = $this->tour();
        $price = $this->service->createPrice($tour, $this->payload(), 99);

        $this->expectException(NotFoundHttpException::class);
        $this->service->updatePrice($otherTour, $price, $this->payload(), 99);
    }

    public function test_update_ready_keeps_the_same_record_and_refreshes_verification(): void
    {
        $tour = $this->tour();
        $price = $this->service->createPrice($tour, $this->payload(), 99);

        $updated = $this->service->updatePrice($tour, $price, $this->payload([
            'contract_rate_idr' => 1_250_000,
            'markup_amount' => '25.50',
        ]), 100);

        $this->assertSame($price->id, $updated->id);
        $this->assertSame(1_250_000, $updated->contract_rate_idr);
        $this->assertSame('25.5', $updated->markup_amount);
        $this->assertSame('25.5', DB::table('tour_prices')->where('id', $updated->id)->value('markup_amount'));
        $this->assertSame(100, (int) $updated->markup_verified_by);
    }

    public function test_update_request_excludes_the_current_price_from_overlap_validation(): void
    {
        $tour = $this->tour();
        $price = $this->service->createPrice($tour, $this->payload(), 99);
        $request = TourPriceUpdateRequestForCrudTest::create('/', 'PUT', $this->payload([
            'contract_rate_idr' => 1_250_000,
        ]));
        $route = new class(['tour' => $tour, 'tourPrice' => $price]) {
            public function __construct(private readonly array $parameters)
            {
            }

            public function parameter(string $name, mixed $default = null): mixed
            {
                return $this->parameters[$name] ?? $default;
            }
        };
        $request->setRouteResolver(fn () => $route);
        $request->normalizeForTest();
        $validator = Validator::make($request->all(), $request->rules());
        $request->attachAfterValidationForTest($validator);

        $this->assertFalse($validator->fails(), $validator->errors()->toJson());
        $this->assertSame($price->id, $request->excludedPriceIdForTest());
    }

    public function test_soft_deleted_price_is_not_ready_for_travel_and_can_be_restored(): void
    {
        $tour = $this->tour();
        $price = $this->service->createPrice($tour, $this->payload(), 99);
        $this->service->deletePrice($tour, $price);

        $this->assertSoftDeleted('tour_prices', ['id' => $price->id]);
        $this->assertSame(0, TourPrices::query()->readyForTravel('2026-09-01')->count());

        $restored = $this->service->restorePrice($tour, (int) $price->id);
        $this->assertNull($restored->deleted_at);
    }

    public function test_update_automatically_reactivates_legacy_unresolved_metadata(): void
    {
        $tour = $this->tour();
        $price = $this->service->createPrice($tour, $this->payload(), 99);
        $price->forceFill([
            'pricing_data_status' => TourPrices::STATUS_UNRESOLVED,
            'status' => 'Draft',
        ])->save();

        $updated = $this->service->updatePrice($tour, $price, $this->payload(), 99);

        $this->assertSame(TourPrices::STATUS_READY, $updated->pricing_data_status);
        $this->assertSame('Active', $updated->status);
    }

    public function test_runtime_tier_resolution_fails_closed_if_legacy_overlap_exists(): void
    {
        $tour = $this->tour();
        $price = $this->service->createPrice($tour, $this->payload(), 99);
        $duplicate = $price->replicate();
        $duplicate->min_qty = 4;
        $duplicate->max_qty = 8;
        $duplicate->save();

        try {
            app(TourPackagePricingService::class)->resolveEligiblePrice(
                $tour,
                4,
                CarbonImmutable::parse('2026-09-01'),
                (int) $price->id,
            );
            $this->fail('Overlapping ready tiers must fail closed.');
        } catch (PricingException $exception) {
            $this->assertSame('PRICING_PAX_TIER_AMBIGUOUS', $exception->pricingCode);
        }
    }

    public function test_master_update_and_delete_do_not_change_historical_order_or_invoice_values(): void
    {
        $tour = $this->tour();
        $price = $this->service->createPrice($tour, $this->payload(), 99);
        DB::table('orders')->insert([
            'id' => 1,
            'price_id' => $price->id,
            'pricing_snapshot_id' => 501,
            'final_total_idr' => 2_666_400,
            'final_total_usd_minor' => 16_665,
        ]);
        DB::table('invoice_admins')->insert([
            'id' => 1,
            'order_id' => 1,
            'total_usd_minor' => 16_665,
        ]);

        $this->service->updatePrice($tour, $price, $this->payload([
            'contract_rate_idr' => 2_000_000,
        ]), 100);
        $this->service->deletePrice($tour, $price->refresh());

        $this->assertDatabaseHas('orders', [
            'id' => 1,
            'pricing_snapshot_id' => 501,
            'final_total_idr' => 2_666_400,
            'final_total_usd_minor' => 16_665,
        ]);
        $this->assertDatabaseHas('invoice_admins', [
            'order_id' => 1,
            'total_usd_minor' => 16_665,
        ]);
    }

    private function tour(): Tours
    {
        return Tours::query()->create([
            'name' => 'Tour '.uniqid(),
            'status' => 'Active',
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'min_qty' => 1,
            'max_qty' => 5,
            'contract_rate_idr' => 1_000_000,
            'markup_amount' => '20.00',
            'markup_type' => TourPrices::MARKUP_TYPE_USD,
            'valid_from' => '2026-08-01',
            'valid_until' => '2026-12-31',
        ], $overrides);
    }

    private function createSchema(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('tour_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_id');
            $table->unsignedInteger('min_qty');
            $table->unsignedInteger('max_qty');
            $table->string('contract_rate');
            $table->unsignedBigInteger('contract_rate_idr')->nullable();
            $table->string('markup');
            $table->string('markup_amount', 32)->nullable();
            $table->string('markup_type', 16)->nullable();
            $table->char('markup_currency', 3)->nullable();
            $table->string('markup_source', 64)->nullable();
            $table->dateTime('markup_verified_at')->nullable();
            $table->unsignedBigInteger('markup_verified_by')->nullable();
            $table->string('pricing_data_status');
            $table->date('valid_from')->nullable();
            $table->string('expired_date');
            $table->date('valid_until')->nullable();
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('price_id');
            $table->unsignedBigInteger('pricing_snapshot_id');
            $table->unsignedBigInteger('final_total_idr');
            $table->unsignedBigInteger('final_total_usd_minor');
        });

        Schema::create('invoice_admins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('total_usd_minor');
        });
    }
}

class TourPriceUpdateRequestForCrudTest extends UpdateTourPriceAdminRequest
{
    public function normalizeForTest(): void
    {
        $this->prepareForValidation();
    }

    public function attachAfterValidationForTest(LaravelValidator $validator): void
    {
        $this->withValidator($validator);
    }

    public function excludedPriceIdForTest(): ?int
    {
        return $this->overlapExceptPriceId();
    }
}
