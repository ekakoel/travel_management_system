<?php

namespace App\Http\Controllers;

use App\Models\Tours;
use App\Services\Tours\TourPackagePricingService;
use App\Support\MoneyFormatter;
use App\ValueObjects\Money;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class TourPricesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    
    public function getPrices(
        Request $request,
        $tour_id,
        TourPackagePricingService $pricing,
        MoneyFormatter $formatter,
    )
    {
        $validated = $request->validate([
            'travel_date' => ['nullable', 'date', 'after_or_equal:today'],
            'booking_code' => ['nullable', 'string', 'max:100'],
            'promotion_id' => ['nullable', 'integer'],
        ]);
        $tour = Tours::query()->where('status', 'Active')->findOrFail($tour_id);
        $serviceDate = CarbonImmutable::parse($validated['travel_date'] ?? now());

        $prices = $pricing->quoteEachTier(
            $tour,
            $serviceDate,
            isset($validated['promotion_id']) ? (int) $validated['promotion_id'] : null,
            $validated['booking_code'] ?? null,
            $request->user()?->id,
        )->map(function (array $tier) use ($formatter) {
                $price = $tier['price'];
                $quote = $tier['quote'];

                return [
                    'id' => $price->id,
                    'min_qty' => $price->min_qty,
                    'max_qty' => $price->max_qty,
                    'valid_from' => optional($price->valid_from)->toDateString(),
                    'valid_until' => optional($price->valid_until)->toDateString(),
                    'price_per_pax_usd_minor' => $quote->unitPriceUsdMinor(),
                    'price_per_pax_usd' => $formatter->decimal(
                        Money::usdCents($quote->unitPriceUsdMinor())
                    ),
                ];
            });

        return response()->json([
            'price_available' => $prices->isNotEmpty(),
            'message' => $prices->isEmpty() ? 'Price temporarily unavailable' : null,
            'prices' => $prices,
        ]);
    }
}
