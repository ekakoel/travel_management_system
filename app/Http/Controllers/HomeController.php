<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tours;
use App\Models\Hotels;
use App\Models\HotelPromo;
use App\Models\Transports;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    
    public function index(Request $request)
    {
        $now = Carbon::now();
        $promos = HotelPromo::with('hotels')
            ->active()
            ->validForBooking($now)
            ->orderBy('book_periode_start', 'asc')
            ->get()
            ->unique('hotels_id');
        return view('frontend.home.index', compact('promos'));
    }

    public function about_us(Request $request)
    {
        return view('home.landing-page.about');
    }

    public function contact_us(Request $request)
    {
        return view('home.landing-page.contact');
    }

    public function transportation_service(Request $request)
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
        return view('home.landing-page.accommodation', compact('hotels','regions', 'searchName', 'searchRegion'));
    }
    public function tour_package_service(Request $request)
    {
        $searchType = $request->input('search_type');
        $searchRegion = $request->input('search_region');
        $tours = Tours::where('status','Active')->get();
        if ($searchType) {
            $tours->where('type', 'LIKE', "%{$searchType}%");
        }
        if ($searchRegion) {
            $tours->where('region', 'LIKE', "%{$searchRegion}%");
        }
        $types = $tours->pluck('type')->unique();
        $regions = $tours->pluck('region')->unique();
        return view('home.tour-packages.index', compact('tours','types' ,'regions', 'searchType', 'searchRegion'));
    }

    public function show($id)
    {
        $hotel = Hotels::with(['rooms', 'optionalrates', 'wedding_venue', 'wedding_planner'])->findOrFail($id);
        return view('home.hotels.detail', compact('hotel'));
    }
    public function show_transport($id)
    {
        $transport = Transports::findOrFail($id);
        return view('home.transports.detail', compact('transport'));
    }
    public function show_tour_package($id)
    {
        $tour = Tours::with('images')->find($id);

        if (!$tour || $tour->status !== 'Active') {
            return redirect("/tour-package-service")->with('danger', "The tour can't be found!");
        }

        return view('home.tour-packages.detail', compact('tour'));
    }



}
