<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tours;
use App\Models\Hotels;
use App\Models\Services;
use App\Models\Weddings;
use App\Models\Dashboard;
use App\Models\HotelRoom;
use App\Models\Promotion;
use App\Models\Activities;
use App\Models\HotelPromo;
use App\Models\Transports;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoreDashboardRequest;
use App\Http\Controllers\DashboardController;
use App\Http\Requests\UpdateDashboardRequest;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function index()
    {
        $now = Carbon::now();
        $menus = Cache::remember('services_menus', 3600, function () {
            return Services::where('status', 'Active')
                ->whereIn('name', ['Weddings', 'Hotels', 'Transports'])
                ->get()
                ->keyBy('name');
        });
        $menu_wedding = $menus->get('Weddings');
        $menu_hotel = $menus->get('Hotels');
        $menu_transport = $menus->get('Transports');
        $promotions = Cache::remember('active_promotions', 3600, function () use ($now) {
            return Promotion::select('name', 'discounts', 'periode_start', 'periode_end')
                ->where('status', 'Active')
                ->where('periode_start', '<=', $now)
                ->where('periode_end', '>=', $now)
                ->get();
        });
        $hotels = Cache::remember('hotels_with_promos', 3600, function () use ($now) {
            return Hotels::with(['promos' => function ($query) use ($now) {
                $query->select('promotion_type', 'hotels_id')
                    ->where('status', 'Active')
                    ->where('book_periode_start', '<=', $now)
                    ->where('book_periode_end', '>=', $now)
                    ->latest();
            }])
            ->select('name', 'cover', 'map', 'code', 'region', 'id')
            ->whereHas('promos', function ($query) use ($now) {
                $query->where('status', 'Active')
                    ->where('book_periode_start', '<=', $now)
                    ->where('book_periode_end', '>=', $now);
            })
            ->latest()
            ->take(8)
            ->get();
        });
        $transports = DB::table('transports')
            ->select('cover', 'capacity', 'name', 'code')
            ->where('status', 'Active')
            ->latest()
            ->limit(4)
            ->get();

        $weddings = Weddings::with(['hotels' => function ($query) {
                $query->select('id', 'name', 'region', 'map','code');
            }])
            ->select('code', 'hotel_id', 'cover', 'name')
            ->where('status', 'Active')
            ->latest()
            ->limit(4)
            ->get();
        

        return view('frontend.dashboards.index', compact(
            'now',
            'hotels',
            'weddings',
            'transports',
            'promotions',
            'menu_wedding',
            'menu_hotel',
            'menu_transport'
        ));
    }
}
