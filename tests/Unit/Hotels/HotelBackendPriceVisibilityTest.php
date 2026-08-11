<?php

namespace Tests\Unit\Hotels;

use App\Models\HotelPackage;
use App\Models\HotelPrice;
use App\Models\HotelPromo;
use App\Models\Hotels;
use App\Models\OptionalRate;
use App\Services\Hotels\HotelPricingService;
use App\ViewModels\Hotels\HotelDetailViewModel;
use PHPUnit\Framework\TestCase;

class HotelBackendPriceVisibilityTest extends TestCase
{
    public function test_backend_summary_excludes_expired_prices_but_keeps_prices_ending_today(): void
    {
        $viewModel = new HotelDetailViewModel(
            hotel: new Hotels(['status' => 'Active']),
            rooms: collect(),
            normalPrices: collect([
                new HotelPrice(['end_date' => '2026-08-09']),
                new HotelPrice(['end_date' => '2026-08-10']),
                new HotelPrice(['end_date' => '2026-08-11']),
            ]),
            promos: collect([
                new HotelPromo(['book_periode_end' => '2026-08-09', 'periode_end' => '2026-09-01']),
                new HotelPromo(['book_periode_end' => '2026-08-10', 'periode_end' => '2026-08-10']),
                new HotelPromo(['book_periode_end' => '2026-09-01', 'periode_end' => '2026-08-09']),
                new HotelPromo(['book_periode_end' => '2026-09-01', 'periode_end' => '2026-09-01']),
            ]),
            packages: collect([
                new HotelPackage(['stay_period_end' => '2026-08-09']),
                new HotelPackage(['stay_period_end' => '2026-08-10']),
                new HotelPackage(['stay_period_end' => '2026-08-11']),
            ]),
            additionalCharges: collect([
                new OptionalRate(['must_buy_end' => '2026-08-09']),
                new OptionalRate(['must_buy_end' => '2026-08-10']),
                new OptionalRate(['active_date' => '2026-08-09']),
                new OptionalRate(['active_date' => '2026-08-10']),
                new OptionalRate(),
            ]),
            contracts: collect(),
            usdRate: null,
            tax: null,
            now: '2026-08-10',
            latestPrice: null,
            author: null,
            pricingService: new HotelPricingService(),
        );

        $pricingStat = collect($viewModel->stats())->firstWhere('label', 'Pricing Rows');

        $this->assertSame('9', $pricingStat['value']);
        $this->assertSame('2 normal / 2 promos / 2 packages / 3 charges', $pricingStat['meta']);
    }
}
