<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tours;
use App\Models\Hotels;
use App\Models\HotelPromo;
use App\Models\Transports;
use Illuminate\Http\Request;

class FrontEndController extends Controller
{
    public function index(Request $request)
    {
        try {
            $now = Carbon::now();
            $promosQuery = HotelPromo::with('hotels')
                ->active()
                ->validForBooking($now)
                ->orderBy('book_periode_start', 'asc')
                ->get();

            // Filter a second time to ensure hotels relationship exists and is not null.
            $promos = $promosQuery->filter(function ($promo) {
                return $promo->hotels !== null;
            })->unique('hotels_id');

            return view('frontend.home.index', compact('promos'));
        } catch (\Exception $e) {
            \Log::error('Error on homepage: ' . $e->getMessage());
            abort(500, 'An error occurred while loading the homepage.');
        }
    }

    public function accommodation_service(Request $request)
    {
        $searchName = $request->input('search_name');
        $searchRegion = $request->input('search_region');
        $hotels = Hotels::where('status','Active')->get();
        if ($searchName) {
            $hotels->where('name', 'LIKE', "%{$searchName}%");
        }
        if ($searchRegion) {
            $hotels->where('region', 'LIKE', "%{$searchRegion}%");
        }
        $regions = $hotels->pluck('region')->unique();
        return view('frontend.accommodations.index', compact('hotels','regions', 'searchName', 'searchRegion'));
    }

    public function accommodation_detail(Request $request, $code)
    {
        $hotel = Hotels::with(['rooms', 'optionalrates', 'wedding_venue', 'wedding_planner'])->where('code', $code)->first();
        return view('frontend.accommodations.detail', compact('hotel'));
    }

    public function transport_service(Request $request)
    {
        $searchName = $request->input('search_name');
        $searchType = $request->input('search_region');
        $transports = Transports::where('status','Active')->get();
        if ($searchName) {
            $transports->where('name', 'LIKE', "%{$searchName}%");
        }
        if ($searchType) {
            $transports->where('region', 'LIKE', "%{$searchType}%");
        }
        $types = $transports->pluck('type')->unique();
        return view('home.landing-page.transport', compact('transports','types', 'searchName', 'searchType'));
    }
}
