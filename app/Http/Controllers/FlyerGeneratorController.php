<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Hotels;
use App\Models\UsdRates;
use App\Models\HotelPromo;
use Illuminate\Http\Request;
use App\Models\FlyerGenerator;
use App\Models\BusinessProfile;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoreFlyerGeneratorRequest;
use App\Http\Requests\UpdateFlyerGeneratorRequest;

class FlyerGeneratorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    
    public function index(Request $request)
    {
        $now = Carbon::now();

        // Ambil query search dari request
        $searchName = $request->input('search_name');
        $searchRegion = $request->input('search_region');

        $hotels = Hotels::whereHas('rooms.promos', function ($query) use ($now) {
                $query->active($now)->validForBooking($now);
            })
            ->with([
                'rooms' => function ($query) use ($now) {
                    $query->whereHas('promos', function ($subQuery) use ($now) {
                        $subQuery->active($now)->validForBooking($now);
                    })->with(['promos' => function ($promoQuery) use ($now) {
                        $promoQuery->active()->validForBooking($now);
                    }]);
                }
            ]);

        // 🔎 Filter berdasarkan nama hotel jika ada input
        if ($searchName) {
            $hotels->where('name', 'LIKE', "%{$searchName}%");
        }

        // 🔎 Filter berdasarkan region jika ada input
        if ($searchRegion) {
            $hotels->where('region', 'LIKE', "%{$searchRegion}%");
        }

        // Urutkan berdasarkan promo terbaru
        $hotels = $hotels->get()->sortByDesc(function ($hotel) {
            return optional($hotel->rooms->flatMap->promos->sortByDesc('created_at')->first())->created_at;
        });

        $regions = $hotels->pluck('region')->unique();

        return view('flyers.index', compact('hotels', 'regions', 'searchName', 'searchRegion'));
    }


    public function flyer_detail(Request $request, $id)
    {
        $now = Carbon::now();
        $tax = Cache::remember('tax', 3600, function () {
            return Tax::select('name', 'tax')->where('name', 'tax')->first();
        });
        $business = Cache::remember('business_profile', 3600, function () {
            return BusinessProfile::select('id', 'name', 'address', 'phone')->first();
        });
        $usdrates = Cache::remember('usd_rates', 3600, function () {
            return UsdRates::select('name', 'rate')->where('name', 'USD')->first();
        });
        $hotel_id = $id;
        $hotel = Hotels::with(['promos.rooms'])->find($hotel_id);
        $flyers = FlyerGenerator::where('hotel_id', $hotel_id)->first();
        $promos = HotelPromo::where('hotels_id', $hotel_id)->active()->validForBooking($now)->get();
        if ($promos->isEmpty()) {
            return redirect("/flyers")->with('error',__('messages.Promotion not found!'));
        }
        $rooms = $promos->pluck('rooms')->flatten()->unique('id');
        return view('flyers.flyer-detail', [
            'now'=>$now,
            'tax'=>$tax,
            'business'=>$business,
            'usdrates'=>$usdrates,
            'flyers'=>$flyers,
            'hotel'=>$hotel,
            'promos'=>$promos,
            'rooms'=>$rooms,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFlyerGeneratorRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(FlyerGenerator $flyerGenerator)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FlyerGenerator $flyerGenerator)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFlyerGeneratorRequest $request, FlyerGenerator $flyerGenerator)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FlyerGenerator $flyerGenerator)
    {
        //
    }
}
