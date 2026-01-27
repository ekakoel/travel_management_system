<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use App\Models\UsdRates;
use App\Models\TourPrices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoreTourPricesRequest;
use App\Http\Requests\UpdateTourPricesRequest;

class TourPricesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    
    public function getPrices($tour_id)
    {
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));

        $prices = TourPrices::where('tour_id', $tour_id)
            ->where('status', 'Active')
            ->orderBy('min_qty')
            ->get()
            ->map(function ($price) use ($usdrates, $tax) {
                return [
                    'min_qty' => $price->min_qty,
                    'max_qty' => $price->max_qty,
                    'price_per_pax' => $price->calculatePrice($usdrates, $tax),
                ];
            });

        return response()->json($prices);
    }
}
