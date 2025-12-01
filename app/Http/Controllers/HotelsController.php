<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Hotels;
use App\Models\Orders;
use App\Models\BedType;
use App\Models\UserLog;
use App\Models\ExtraBed;
use App\Models\UsdRates;
use App\Models\Attention;
use App\Models\HotelRoom;
use App\Models\HotelType;
use App\Models\Promotion;
use App\Models\HotelPrice;
use App\Models\HotelPromo;
use App\Models\Transports;
use App\Models\BookingCode;
use Illuminate\Support\Str;
use App\Models\HotelPackage;
use App\Models\HotelsImages;
use App\Models\OptionalRate;
use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use App\Models\OptionalRateOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Requests\StorehotelsRequest;
use App\Http\Requests\UpdatehotelsRequest;

class HotelsController extends Controller
{   
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    public function index(Request $request)
    {
        $now = Carbon::now();
        $query = Hotels::query();

        // filter by name
        if ($request->filled('hotel_name')) {
            $query->where('name', 'LIKE', '%' . $request->hotel_name . '%');
        }

        // filter by region
        if ($request->filled('hotel_region')) {
            $query->where('region', 'LIKE', '%' . $request->hotel_region . '%');
        }

        $hotels = $query->paginate(18); // 8 per page

        if ($request->ajax()) {
            return response()->json([
                'html' => view('frontend.hotels.partials.list', compact('hotels'))->render(),
                'hasMore' => $hotels->hasMorePages(),
            ]);
        }

        $promotions = Promotion::select('name', 'discounts', 'periode_start', 'periode_end')
            ->where('status', "Active")
            ->where('periode_start', '<=', $now)
            ->where('periode_end', '>=', $now)
            ->get();

        $hotels = $this->getHotelsQuery($request)->paginate(12);
        return view('frontend.hotels.index', compact('hotels', 'promotions'));
        // return view('main.hotels', compact('hotels', 'promotions'));
    }
    public function hotel_promotions(Request $request)
    {
        $now = now();

        // Query hanya hotel yang punya promo aktif dan valid booking
        $query = Hotels::whereHas('promos', function ($q) use ($now) {
            $q->active($now)->validForBooking($now);
        });
        // $hotelPromotions = HotelPromo::all();
        // Filter by name
        if ($request->filled('hotel_name')) {
            $query->where('name', 'LIKE', '%' . $request->hotel_name . '%');
        }

        // Filter by region
        if ($request->filled('hotel_region')) {
            $query->where('region', 'LIKE', '%' . $request->hotel_region . '%');
        }

        // Ambil data hotel dengan relasi promos (hanya promos aktif & valid)
        $hotels = $query->with(['promos' => function ($q) use ($now) {
            $q->active($now)->validForBooking($now);
        }])
        ->paginate(12);

        $promoImages = [
            'Hot Deal' => 'hot_deal_promo.png',
            'Best Choice' => 'best_choice_promo.png',
            'Best Price' => 'best_price_promo.png',
            'Special Offer' => 'special_offer_promo.png',
        ];
        // Jika request AJAX (load more / search)
        if ($request->ajax()) {
            return response()->json([
                'html' => view('frontend.hotels.partials.hotel-promo-list', compact(['hotels', 'promoImages']))->render(),
                'hasMore' => $hotels->hasMorePages(),
            ]);
        }

        return view('frontend.hotels.hotel-promotions', compact('hotels','promoImages'));
    }
    
    public function index_two(Request $request)
    {
        $now = Carbon::now();
        $query = Hotels::query();

        // filter by name
        if ($request->filled('hotel_name')) {
            $query->where('name', 'LIKE', '%' . $request->hotel_name . '%');
        }

        // filter by region
        if ($request->filled('hotel_region')) {
            $query->where('region', 'LIKE', '%' . $request->hotel_region . '%');
        }

        $hotels = $query->paginate(8); // 8 per page

        if ($request->ajax()) {
            return response()->json([
                'html' => view('frontend.hotels.partials.list', compact('hotels'))->render(),
                'hasMore' => $hotels->hasMorePages(),
            ]);
        }

        $promotions = Promotion::select('name', 'discounts', 'periode_start', 'periode_end')
            ->where('status', "Active")
            ->where('periode_start', '<=', $now)
            ->where('periode_end', '>=', $now)
            ->get();

        $hotels = $this->getHotelsQuery($request)->paginate(12);
        // return view('frontend.hotels.index', compact('hotels', 'promotions'));
        return view('main.hotels', compact('hotels', 'promotions'));
    }

    public function autocomplete(Request $request)
    {
        $query = $request->input('query');
        $hotels = Hotels::where('name', 'LIKE', "%{$query}%")
            ->where('status', 'Active')
            ->limit(5)
            ->get(['id', 'name']);
    
        return response()->json(['hotels' => $hotels]);
    }


    public function autocompleteRegion(Request $request)
    {
        $query = $request->input('query');
        $regions = Hotels::where('region', 'LIKE', "%{$query}%")
            ->where('status', 'Active')
            ->select('region')
            ->distinct()
            ->limit(5)
            ->get(['id', 'region']);

        return response()->json(['regions' => $regions]);
    }

    
    public function loadMore(Request $request)
    {
        $hotels = $this->getHotelsQuery($request)->paginate(12);
        $html = view('partials.hotel-list', compact('hotels'))->render();

        return response()->json([
            'html' => $html,
            'hasMore' => $hotels->hasMorePages()
        ]);
    }
    public function hotelPromoLoadMore(Request $request)
    {
        $hotels = $this->getHotelsQuery($request)->paginate(12);
        $html = view('frontend.hotels.partials.hotel-promo-list', compact('hotels'))->render();

        return response()->json([
            'html' => $html,
            'hasMore' => $hotels->hasMorePages()
        ]);
    }

    public function front_end_load_more(Request $request)
    {
        $hotels = $this->getHotelsQuery($request)->paginate(12);
        $html = view('frontend.partials.hotel-list', compact('hotels'))->render();

        return response()->json([
            'html' => $html,
            'hasMore' => $hotels->hasMorePages()
        ]);
    }
    public function hotel_promo_load_more(Request $request)
    {
        $promoImages = [
            'Hot Deal' => 'hot_deal_promo.png',
            'Best Choice' => 'best_choice_promo.png',
            'Best Price' => 'best_price_promo.png',
            'Special Offer' => 'special_offer_promo.png',
        ];
        $hotels = $this->getHotelPromotionsQuery($request)->paginate(12);
        $html = view('frontend.hotels.partials.hotel-promo-list', compact(['hotels', 'promoImages']))->render();

        return response()->json([
            'html' => $html,
            'hasMore' => $hotels->hasMorePages()
        ]);
    }

    private function getHotelsQuery(Request $request)
    {
        $now = Carbon::now();
        $hotelsQuery = Hotels::select('code', 'name', 'region', 'map', 'cover', 'id')
            ->where('status', 'active')
            ->with(['promos' => function ($query) use ($now) {
                $query->select('promotion_type', 'hotels_id')
                    ->active($now)
                    ->where('book_periode_start', '<=', $now)
                    ->where('book_periode_end', '>=', $now)
                    ->latest();
            }]);

        if ($request->filled('hotel_name')) {
            $hotelsQuery->where('name', 'like', '%' . $request->input('hotel_name') . '%');
        }
        if ($request->filled('hotel_region')) {
            $hotelsQuery->where('region', 'like', '%' . $request->input('hotel_region') . '%');
        }

        return $hotelsQuery;
    }
    private function getHotelPromotionsQuery(Request $request)
    {
        $now = Carbon::now();
        $hotelsQuery = Hotels::where('status', 'Active')
            ->whereHas('promos', function ($q) use ($now) {
                $q->active($now)->validForBooking($now);
            }
        );

        if ($request->filled('hotel_name')) {
            $hotelsQuery->where('name', 'like', '%' . $request->input('hotel_name') . '%');
        }
        if ($request->filled('hotel_region')) {
            $hotelsQuery->where('region', 'like', '%' . $request->input('hotel_region') . '%');
        }

        return $hotelsQuery;
    }
    

    // HOTEL SEARCH ====================================================================================> OK
    public function search_hotel(Request $request)
    {
        if (!$request->hotel_name && !$request->hotel_region) {
            return redirect("/hotels");
        }
        $now = Carbon::now();
        $hotels = Hotels::query()
            ->select('code', 'name', 'region', 'map', 'cover', 'id')
            ->where('status', 'active')
            ->when($request->hotel_name, function ($query, $hotel_name) {
                $query->where('name', 'LIKE', "%$hotel_name%");
            })
            ->when($request->hotel_region, function ($query, $hotel_region) {
                $query->where('region', 'LIKE', "%$hotel_region%");
            })
            ->with(['promos' => function ($query) use ($now) {
                $query->select('promotion_type', 'hotels_id')
                    ->where('book_periode_start', '<=', $now)
                    ->where('book_periode_end', '>=', $now)
                    ->where('status', 'Active');
            }])
            ->latest()
            ->get();
        $promotions = Promotion::query()
            ->select('name', 'discounts', 'periode_start', 'periode_end')
            ->where('status', 'Active')
            ->whereBetween('periode_start', [$now->startOfDay(), $now->endOfDay()])
            ->get();
        return view('main.hotelsearch', compact('hotels', 'promotions'));
    }

    // Detail Hotel ====================================================================================> OK
    public function hoteldetail(Request $request, $code)
    {
        $now = Carbon::now();
        $ages = session('booking_dates.children_ages', []);
        if ($ages) {
            $childAges = json_decode($ages, true) ?: [];
        }else{
            $childAges = [];
        }
        $duration = session('booking_dates.duration') ?? 0;
        $checkin = session('booking_dates.checkin') ?? $now;
        $checkout = date('Y-m-d', strtotime($checkin . " +{$duration} days"));
        $number_of_rooms = session('booking_dates.nor') ?? 0;
        $number_of_guests = session('booking_dates.nog') ?? 0;
        $number_of_adults = session('booking_dates.noa') ?? 0;
        $number_of_childs = session('booking_dates.noc') ?? 0;
        $hotel = Hotels::with([
            'rooms'=> function ($rm){
                $rm->where('status','Active');
            },
            'promos' => function ($query) use ($now) {
                $query->where('status', 'Active')
                ->where('book_periode_start', '<=', $now)
                ->where('book_periode_end', '>=', $now)
                ->latest();
        }])->where('code', $code)->first();
        if (!$hotel) {
            return redirect("/hotels")->with('error','The hotel was not found!');
        }
        $business = BusinessProfile::findOrFail(1);
        $usdrates = UsdRates::where('name', 'USD')->first();
        $hotels = Hotels::select('code','name','region','cover','id')
            ->with([
                'promos' => function ($query) use ($now) {
                    $query->where('status', 'Active')
                    ->where('book_periode_start', '<=', $now)
                    ->where('book_periode_end', '>=', $now);
            }])->where('status', 'Active')
                ->where('region', $hotel->region)
                ->where('id', '!=', $hotel->id)
                ->take(4)
                ->get();
        $promotions = Promotion::select('name','discounts','periode_start','periode_end')
            ->where('status', "Active")
            ->where('periode_start','<=',$now)
            ->where('periode_end','>=',$now)
            ->get();
        $bookingcode = session('bookingcode');
        $promoImages = [
            'Hot Deal' => 'hot_deal_promo.png',
            'Best Choice' => 'best_choice_promo.png',
            'Best Price' => 'best_price_promo.png',
            'Special Offer' => 'special_offer_promo.png',
        ];
        // dd(session('booking_dates'));
        return view('frontend.hotels.detail', compact(
            'hotel',
            'business',
            'usdrates',
            'now',
            'hotels',
            'promotions',
            'bookingcode',
            'promoImages',
            'childAges',
            'checkin',
            'checkout',
            'duration',
            'number_of_rooms',
            'number_of_guests',
            'number_of_adults',
            'number_of_childs',
        ));
        // return view('main.hoteldetail', compact('hotel', 'business', 'usdrates', 'now', 'nearhotels','promotions','bookingcode','promoImages'));
    }

    public function updateDate(Request $request){
        // Validasi input (opsional tapi direkomendasikan)
        $request->validate([
            'checkin' => 'required|date',
            'checkout' => 'required|date|after_or_equal:checkin',
        ]);

        // Ambil data dari request
        $checkin = $request->input('checkin');
        $checkout = $request->input('checkout');
        $nog = session('booking_dates.nog') ?? 0;
        $nor = session('booking_dates.nor') ?? 0;
        $noa = session('booking_dates.noa') ?? 0;
        $noc = session('booking_dates.noc') ?? 0;
        $children_ages = session('booking_dates.children_ages') ?? [];
        // Hitung durasi (jumlah malam)
        $checkInDate = \Carbon\Carbon::parse($checkin);
        $checkOutDate = \Carbon\Carbon::parse($checkout);
        $duration = $checkOutDate->diffInDays($checkInDate);

        // Simpan ke session
        session([
            'booking_dates' => [
                'checkin' => $checkin,
                'checkout' => $checkout,
                'duration' => $duration,
                'nog' => $nog,
                'nor' => $nor,
                'noa' => $noa,
                'noc' => $noc,
                'children_ages' => $children_ages,
            ]
        ]);

        // Kirim response JSON
        return response()->json([
            'message' => 'Tanggal berhasil diperbarui.',
            'data' => session('booking_dates')
        ]);
    }
    public function updateGuesRoom(Request $request){
        // Validasi input (opsional tapi direkomendasikan)
        $request->validate([
            'nor' => 'required',
            'nog' => 'required',
            'noa' => 'required',
            'noc' => 'required',
        ]);

        // Ambil data dari request
        $checkin = session('booking_dates.checkin') ?? 0;
        $checkout = session('booking_dates.checkout') ?? 0;
        $duration = session('booking_dates.duration') ?? 0;
        $children_ages = session('booking_dates.children_ages') ?? [];
        $number_of_rooms = $request->input('nor');
        $number_of_guests = $request->input('nog');
        $number_of_adults = $request->input('noa');
        $number_of_childrens = $request->input('noc');

        // Simpan ke session
        session([
            'booking_dates' => [
                'checkin' => $checkin,
                'checkout' => $checkout,
                'duration' => $duration,
                'nor' => $number_of_rooms,
                'nog' => $number_of_guests,
                'noa' => $number_of_adults,
                'noc' => $number_of_childrens,
                'children_ages' => $children_ages,
            ]
        ]);

        // Kirim response JSON
        return response()->json([
            'message' => 'Berhasil menyimpan data kamar dan tamu',
            'data' => session('booking_dates')
        ]);
    }
    public function updateChildrenAges(Request $request){
        // Validasi input (opsional tapi direkomendasikan)
        $request->validate([
            'children_ages' => 'required',
        ]);
        $childrenAges = json_encode($request->input('children_ages'))??null;
        // Ambil data dari request
        $checkin = session('booking_dates.checkin') ?? 0;
        $checkout = session('booking_dates.checkout') ?? 0;
        $duration = session('booking_dates.duration') ?? 0;
        $children_ages = $childrenAges;
        $number_of_rooms = session('booking_dates.nor') ?? 0;
        $number_of_guests = session('booking_dates.nog') ?? 0;
        $number_of_adults = session('booking_dates.noa') ?? 0;
        $number_of_childrens = session('booking_dates.noc') ?? 0;

        // Simpan ke session
        session([
            'booking_dates' => [
                'checkin' => $checkin,
                'checkout' => $checkout,
                'duration' => $duration,
                'nor' => $number_of_rooms,
                'nog' => $number_of_guests,
                'noa' => $number_of_adults,
                'noc' => $number_of_childrens,
                'children_ages' => $children_ages,
            ]
        ]);

        // Kirim response JSON
        return response()->json([
            'message' => 'Berhasil menyimpan data umur anak',
            'data' => session('booking_dates')
        ]);
    }
    


    // Detail Hotel BOOKING CODE ===========================================================================>
    public function hoteldetail_bookingcode($code,$bcode){
        $promotions = Promotion::where('status',"Active")->get();
        $user_id = Auth::user()->id;
        $ordercode = Orders::where('user_id', $user_id)
            ->where('bookingcode',$bcode)
            ->where('status','!=','Rejected')->first();
        $ascode = Orders::where('bookingcode',$bcode)
            ->where('status','!=','Rejected')->get();
        $cusdcode = count($ascode);
        $business = BusinessProfile::where('id','=',1)->first();
        $now = Carbon::now();
        $hotels = Hotels::with('rooms')->get();
        $hotel = Hotels::where("code",$code)->first();
        if ($hotel) {
            $nearhotels = Hotels::where('status',"Active")->where('region',"like",'%'.$hotel->region.'%')->take(4)->get();
        }else{
            $nearhotels = NULL;

        }
        if ($hotel) {
            $hotelrooms = HotelRoom::where('hotels_id','=',$hotel->id)
            ->where('status', 'Active')
            ->get();
        }else {
            return redirect("/hotels");
        }
        $data = HotelPrice::where('hotels_id','=',$hotel->id)->get();
        $lowerprice = $data->where('contract_rate','>',0)->min('contract_rate');
        $optional_rate = OptionalRate::where('service',"Hotel")
        ->where('service_id',$hotel->id)->get();
        $usdrates = UsdRates::where('name','USD')->first();
        $hotel_promotions = HotelPromo::where('hotels_id',$hotel->id)
        ->where('book_periode_start','<=',$now)
        ->where('book_periode_end','>=',$now)
        ->where('status','Active')
        ->where('periode_start','>=',$now)
        ->orderBy('created_at','desc')
        ->get();
        if ($ordercode) {
            $bookingcode = $bcode;
            $bookingcode_status = "Used";
            return redirect("/hotel-$code")->with('danger','The booking code that you entered has been used!');
        }else{
            $bkingcode = BookingCode::where('code', $bcode)
            ->where('expired_date','>',$now)
            ->where('amount','>',$cusdcode)
            ->first();
            if ($bkingcode) {
                $bookingcode_status = "Valid";
                $bookingcode = $bkingcode;
                return view('main.hoteldetail', compact('hotel'),[
                    'business'=>$business,
                    'optional_rate'=>$optional_rate,
                    'usdrates'=>$usdrates,
                    'hotels'=> $hotels,
                    'now'=>$now,
                    'hotel'=>$hotel,
                    'nearhotels'=>$nearhotels,
                    'lowerprice'=>$lowerprice,
                    'hotelrooms'=>$hotelrooms,
                    'bookingcode'=>$bookingcode,
                    'bookingcode_status'=>$bookingcode_status,
                    'promotions'=>$promotions,
                    'hotel_promotions'=>$hotel_promotions,
                ]);
            }else{
                return redirect("/hotel-$code")->with('danger','Booking code is invalid!');
            }
        }
    }





    public function showPromos(Request $request){
        // Contoh data yang diperlukan
        $checkin = '2023-06-01';
        $duration = 5;
        $usdrates = USDExchangeRate::first();
        $tax = Tax::first();
        $now = now();
        $orderno = 1; // Contoh nomor order
        $promos = Promo::with('rooms.prices')->get();
        $bookingcode = BookingCode::where('code', $request->bookingcode)->first();
    
        $promoDetails = $this->calculatePromoDetails($promos, $checkin, $duration, $usdrates, $tax, $bookingcode);
    
        return view('promos', compact('promoDetails', 'checkin', 'duration', 'usdrates', 'tax', 'now', 'orderno', 'bookingcode'));
    }
    
    private function calculatePromoDetails($promos, $checkin, $duration, $usdrates, $tax, $bookingcode){
        $promoDetails = [];
    
        foreach ($promos as $promo) {
            $promoInfo = [];
            $promo_id_list = [];
            $chek_n_p = [];
            $promo_id = [];
            $promo_check_duration = [];
            
            for ($i = 0; $i < $duration; $i++) {
                $date_in_i = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($checkin)));
                if ($promo->periode_start <= $date_in_i && $promo->periode_end >= $date_in_i) {
                    $promo_check_duration[] = 1;
                    $promo_id[] = $promo->id;
                } else {
                    $promo_check_duration[] = 0;
                    $promo_id[] = 0;
                }
            }
    
            if (array_sum($promo_check_duration) >= $promo->minimum_stay) {
                $new_promos = $promos->where('id', '!=', $promo->id)->where('rooms_id', $promo->rooms_id);
                foreach ($new_promos as $new_promo) {
                    $new_promo_check_duration = [];
                    for ($j = array_sum($promo_check_duration); $j < $duration; $j++) {
                        $date_in_j = date('Y-m-d', strtotime('+' . $j . ' days', strtotime($checkin)));
                        $new_promo_check_duration[] = ($new_promo->periode_start <= $date_in_j && $new_promo->periode_end >= $date_in_j) ? 1 : 0;
                    }
                    if (array_sum($new_promo_check_duration) >= $new_promo->minimum_stay) {
                        for ($k = 0; $k < $duration; $k++) {
                            $date_in_k = date('Y-m-d', strtotime('+' . $k . ' days', strtotime($checkin)));
                            if ($promo_id[$k] == 0 && $new_promo->periode_start <= $date_in_k && $new_promo->periode_end >= $date_in_k) {
                                $promo_id[$k] = $new_promo->id;
                            }
                        }
                    }
                }
            }
    
            foreach ($promo_id as $id) {
                $promoDetails[] = $this->getPromoDetail($id, $promo, $checkin, $duration, $usdrates, $tax, $bookingcode);
            }
        }
    
        return $promoDetails;
    }
    
    private function getPromoDetail($promo_id, $promo, $checkin, $duration, $usdrates, $tax, $bookingcode)
    {
        $promoDetail = [];
        $pr_final_price = [];
        for ($q = 0; $q < $duration; $q++) {
            $on_date = date("Y-m-d", strtotime("+" . $q . " days", strtotime($checkin)));
            $promo_show = Promo::find($promo_id);
            if ($promo_show) {
                $promo_c_rate = $promo_show->contract_rate;
                $promo_usd = ceil($promo_c_rate / $usdrates->rate) + $promo_show->markup;
                $promo_tax = ceil($promo_usd * ($tax->tax / 100));
                $prp = $promo_usd + $promo_tax;
                $pr_final_price[] = $prp;
                $promoDetail['price'][] = [
                    'date' => date('m/d', strtotime($on_date)),
                    'price' => $prp,
                    'type' => $promo_show->promotion_type
                ];
            } else {
                $promo_off = $promo->rooms->prices->where('start_date', '<=', $on_date)->where('end_date', '>=', $on_date)->first();
                if ($promo_off) {
                    $promo_normal_c_rate = $promo_off->contract_rate;
                    $promo_normal_usd = ceil($promo_normal_c_rate / $usdrates->rate) + $promo_off->markup;
                    $promo_normal_tax = ceil($promo_normal_usd * ($tax->tax / 100));
                    $promo_normal_price = $promo_normal_usd + $promo_normal_tax;
                    $pr_final_price[] = $promo_normal_price;
                    $promoDetail['price'][] = [
                        'date' => date('m/d', strtotime($on_date)),
                        'price' => $promo_normal_price,
                        'type' => 'Normal'
                    ];
                } else {
                    $promoDetail['price'][] = [
                        'date' => date('m/d', strtotime($on_date)),
                        'price' => 0,
                        'type' => 'Normal'
                    ];
                }
            }
        }
    
        $pr_final_p = array_sum($pr_final_price);
        $promo_final_price = $bookingcode ? $pr_final_p - $bookingcode->discounts : $pr_final_p;
    
        $promoDetail['room'] = $promo->rooms->rooms;
        $promoDetail['promo_final_price'] = $promo_final_price;
        $promoDetail['pr_final_p'] = $pr_final_p;
        $promoDetail['promo_id_list'] = $promo_id;
    
        return $promoDetail;
    }
    
// Hotel Price =========================================================================================>
    public function hotel_price(Request $request, $code)
    {
        $now = now()->format('Y-m-d');
        $tax = Cache::remember('tax', 3600, function () {
            return Tax::select('name', 'tax')->where('name', 'tax')->first();
        });
        $business = Cache::remember('business_profile', 3600, function () {
            return BusinessProfile::select('id', 'name', 'address')->first();
        });
        $usdrates = Cache::remember('usd_rates', 3600, function () {
            return UsdRates::select('name', 'rate')->where('name', 'USD')->first();
        });
        $hotel = Hotels::with(['rooms' => function ($query) {
            $query->where('status', 'Active');
        }])->where('code', $code)->first();

        $ages = session('booking_dates.children_ages', []);
        if ($ages) {
            $childAges = json_decode($ages, true) ?: [];
        }else{
            $childAges = [];
        }
        $duration = session('booking_dates.duration') ?? 0;

        
        $childrenAges = $request->children_ages;
        $children_ages = $childrenAges;
        $number_of_rooms = session('booking_dates.nor') ?? 0;
        $number_of_guests = session('booking_dates.nog') ?? 0;
        $number_of_adults = session('booking_dates.noa') ?? 0;
        $number_of_childs = session('booking_dates.noc') ?? 0;
        $checkin = session('booking_dates.checkin') ?? 0;
        $checkout = session('booking_dates.checkout') ?? 0;
        if ($duration < $hotel->min_stay) {
            return back()->with('error',__('messages.This hotel requires a minimum stay of').$hotel->min_stay." ".__('messages.nights'));
        }elseif ($number_of_rooms < 1) {
            return back()->with('error',__('messages.Please enter the number of rooms and guests before checking the room rates!'));
        }
        
        $adult_guests = $request->adult_guests;
        $children_guests = $request->children_guests;
        $number_of_room = $request->number_of_room;

        

        // dd(session('booking_dates'));
        
        
        // $nearhotels = Hotels::with([
        //     'promos' => function ($query) use ($now) {
        //         $query->where('status', 'Active')
        //             ->where('book_periode_start', '<=', $now)
        //             ->where('book_periode_end', '>=', $now);
        //         }])->where('status', 'Active')
        //     ->where('region', $hotel->region)
        //     ->where('id', '!=', $hotel->id)
        //     ->take(8)
        //     ->get(['id','code','cover', 'name', 'region']);

        $promotions = Promotion::where('status', 'Active')
            ->where('periode_start','<=',$now)
            ->where('periode_end','>=',$now)
            ->get();
        $promotion_name = $promotions->pluck('name')->implode(', ');
        $promotion_price = $promotions->sum('discounts');
        $promoImages = [
            'Hot Deal' => 'hot_deal_promo.png',
            'Best Choice' => 'best_choice_promo.png',
            'Best Price' => 'best_price_promo.png',
            'Special Offer' => 'special_offer_promo.png',
        ];
        $hotels = Hotels::select('code','name','region','cover','id')
            ->with([
                'promos' => function ($query) use ($now) {
                    $query->where('status', 'Active')
                    ->where('book_periode_start', '<=', $now)
                    ->where('book_periode_end', '>=', $now);
            }])->where('status', 'Active')
                ->where('region', $hotel->region)
                ->where('id', '!=', $hotel->id)
                ->take(4)
                ->get();

        if ($duration < $hotel->min_stay && $number_of_room < 1 && $adult_guests < 1) {
            return view('frontend.hotels.detail', compact('hotel','hotels', 'business', 'usdrates', 'now', 'promotions','promoImages'))
            ->with('error', __('messages.Minimum stay') . " {$hotel->min_stay} " . __('messages.nights')
            );
        }
        $promo_colors = [
            "Special Offer" => "bg-blue",
            "Best Choice"   => "bg-green",
            "Best Price"    => "bg-orange",
            "Hot Deal"      => "bg-red",
            "Normal"        => "bg-gray",
        ];
        $displayedPromos = [];
        $room_prices = HotelPrice::
            with([
                'rooms'=> function ($query) {
                        $query->where('status', 'Active'); 
                    },
                'hotels',
            ])
            ->where('hotels_id', $hotel->id)
            ->get();

        $packages = HotelPackage::with(['hotels', 'room'])
            ->where('hotels_id', $hotel->id)
            ->where('status', 'Active')
            ->forDuration($duration)
            ->validForStay($checkin)
            ->get()
            ->map(function ($package) use ($usdrates, $tax, $duration, $checkin) {
                $package->parsed_checkin = $checkin;
                $package->calculated_price = $package->calculatePrice($usdrates, $tax);
                $package->price_per_day = $package->calculated_price / $duration;
                return $package;
            });
        $user_id = Auth::id();
        $order_select = Orders::where('user_id', $user_id)
            ->where('servicename', $hotel->name)
            ->where('checkin', '>', $now)
            ->get();
        $orders = Orders::where('user_id', $user_id)->get(['id', 'final_price']);
        $orderno = Orders::count() + 1;
        
        $hotel_promotions = HotelPromo::active($now)
            ->validForBooking($now)
            ->where('hotels_id', $hotel->id)
            ->get();

        $processedPromos = [];
        $normalPriceData = [];
        foreach ($hotel->rooms as $room) {
            $totalKickBack = 0;
            $totalNormalRoomPrice = 0;
            $promoDetails = $this->processPromo($room, $room_prices, $hotel_promotions, $checkin, $duration, $usdrates, $tax);
            if ($promoDetails) {
                $processedPromos[] = $promoDetails;
            }

            $normalPricePerDate = [];
            $has_normal = [];
            for ($k=0; $k < $duration; $k++) { 
                $normal_price_temporary = 0;
                $normal_temp_date = date('Y-m-d',strtotime('+'.$k.'days',strtotime($checkin)));
                $roomPrice = $room_prices->where('rooms_id',$room->id)
                    ->where('start_date','<=',$normal_temp_date)
                    ->where('end_date','>=',$normal_temp_date)
                    ->first();
                if ($roomPrice) {
                    if ($roomPrice->kick_back) {
                        $kick_back = $roomPrice->kick_back;
                    }else{
                        $kick_back = 0;
                    }
                    $normal_price_temporary = $roomPrice->calculatePrice($usdrates, $tax);

                }else{
                    $has_normal[]= 0;
                    $normal_price_temporary = 0;
                    $kick_back = 0;
                }
                $normalPricePerDate[] = [
                    'normal_date' => $normal_temp_date,
                    'normal_price' => $normal_price_temporary,
                    'normal_kick_back' => $kick_back,
                ];
                $totalNormalRoomPrice += $normal_price_temporary;
                $totalKickBack += $kick_back;
            }
            if (!in_array(0,$has_normal)) {
                $normalPriceData[] = [
                    'cover' => $room->cover,
                    'normal_room_id' => $room->id,
                    'normal_prices' => $normalPricePerDate,
                    'normal_room' => $room,
                    'total_price' => $totalNormalRoomPrice,
                    'total_kick_b' => $totalKickBack,
                    'include' => $room->include,
                ];
            }
        }
        $transports = Transports::select('id','name','brand','capacity')->where('status',"Active")->orderBy('capacity', 'DESC')->get();
        
        return view('frontend.hotels.price', [
            'tax' => $tax,
            'usdrates' => $usdrates,
            'business' => $business,
            'now' => $now,
            'hotel' => $hotel,
            'hotels' => $hotels,
            'number_of_rooms' => $number_of_rooms,
            'number_of_guests' => $number_of_guests,
            'number_of_adults' => $number_of_adults,
            'number_of_childs' => $number_of_childs,
            'childAges' => $childAges,
            
            // 'nearhotels' => $nearhotels,
            'room_prices' => $room_prices,
            'packages' => $packages,
            'order_select' => $order_select,
            'orders' => $orders,
            'orderno' => $orderno,
            'promotions' => $promotions,
            'promotion_name' => $promotion_name,
            'promotion_price' => $promotion_price,
            'duration' => $duration,
            'checkin' => $checkin,
            'checkout' => $checkout,
            'processedPromos' => $processedPromos,
            'hotel_promotions' => $hotel_promotions,
            'normalPriceData' => $normalPriceData,
            'totalNormalRoomPrice' => $totalNormalRoomPrice,
            'totalKickBack' => $totalKickBack,
            'promo_colors' => $promo_colors,
            'displayedPromos' => $displayedPromos,
            'transports' => $transports,
            'promoImages' => $promoImages,
        ]);
    }

    private function processPromo($room, $room_prices, $hotel_promotions, $checkin, $duration, $usdrates, $tax)
    {
        $on_dates = [];
        $promo_type = [];
        $hotel_promo = [];
        $promo_id_list = [];
        $type_price_list = [];
        $type_price = [];
        $price_list = [];
        $promo_include = [];
        $current_date = strtotime($checkin);
        $promo_off = [];
        $check_availability = 0;

        $valid_promotions = $hotel_promotions->filter(function ($promo) use ($checkin, $duration) {
            return $this->calculatePromoDuration($promo, $checkin, $duration) > 0 
                && $promo->minimum_stay <= $duration;
        });

        $remaining_duration = $duration;

        for ($i = 0; $i < $duration; $i++) {
            $date_in = date('Y-m-d', $current_date + ($i * 86400));
            $has_promo = false;

            foreach ($valid_promotions as $hotel_promotion) {
                if ($hotel_promotion->rooms_id === $room->id) {
                    if ($hotel_promotion->periode_start <= $date_in && $hotel_promotion->periode_end >= $date_in) {
                        $promo_days_left = $this->calculatePromoDuration($hotel_promotion, $date_in, $remaining_duration);
                        if ($promo_days_left >= $hotel_promotion->minimum_stay) {
                            for ($j = 0; $j < $promo_days_left; $j++) {
                                if ($i + $j < $duration) {
                                    $current_date_in = date('Y-m-d', $current_date + (($i + $j) * 86400));
                                    $on_dates[] = $current_date_in;
                                    $promo_type[] = $hotel_promotion->promotion_type;
                                    $type_price[] = 1;
                                    $hotel_promo[] = $hotel_promotion;
                                    $promo_id_list[] = $hotel_promotion->id;
                                    $type_price_list[] = $hotel_promotion->id;
                                    $price_list[] = $hotel_promotion->calculatePrice($usdrates, $tax);
                                    $promo_include[] = $hotel_promotion->include;
                                    $promo_off[] = 1;
                                    $check_availability++;
                                    $remaining_duration--;
                                }
                            }
                            $has_promo = true;
                            $i += $promo_days_left - 1;
                            break;
                        }
                    }
                }
            }
            if (!$has_promo) {
                $normalPrice = HotelPrice::where('rooms_id', $room->id)
                            ->where('start_date', '<=', $date_in)
                            ->where('end_date', '>=', $date_in)
                            ->first();
                if ($normalPrice) {
                    $type_price_list[] = $normalPrice->id;
                }else {
                    $type_price_list[] = 0;
                }
                $normal_price = $this->getNormalPrice($room, $date_in, $usdrates, $tax);
                $on_dates[] = $date_in;
                $promo_type[] = 'Normal';
                $type_price[] = 0;
                $hotel_promo[] = NULL;
                $promo_id_list[] = NULL;
                $promo_include[] = NULL;
                $promo_off[] = 0;
                $price_list[] = $normal_price;
                $remaining_duration--;
            }
        }
        $total_promo_off = array_sum($promo_off);
        if (in_array(0,$price_list)) {
            return NULL;
        }else{
            if ($total_promo_off > 0) {
                return [
                    'room' => $room,
                    'on_dates' => $on_dates,
                    'promo_type' => $promo_type,
                    'hotel_promo' => $hotel_promo,
                    'promo_id_list' => $promo_id_list,
                    'type_price_list' => $type_price_list,
                    'type_price' => $type_price,
                    'price_list' => $price_list,
                    'total_price' => array_sum($price_list),
                    'check_availability' => $check_availability,
                    'total_promo_off' => $total_promo_off,
                    'promo_include' => $promo_include,
                ];
            } else {
                return NULL;
            }
        }
    }

    private function calculatePromoDuration($hotel_promotion, $checkin, $duration)
    {
        $promo_check_duration = 0;
        for ($j = 0; $j < $duration; $j++) {
            $check_date = date('Y-m-d', strtotime("+$j days", strtotime($checkin)));
            if ($hotel_promotion->periode_start <= $check_date && $hotel_promotion->periode_end >= $check_date) {
                $promo_check_duration++;
            }
        }
        return $promo_check_duration;
    }

    private function getNormalPrice($room, $date_in, $usdrates, $tax)
    {
        $roomList = HotelPrice::where('rooms_id', $room->id)
                                ->where('start_date', '<=', $date_in)
                                ->where('end_date', '>=', $date_in)
                                ->first();
        return $roomList ? $roomList->calculatePrice($usdrates, $tax) : 0;
    }
    private function parseCheckInOut($checkincout)
    {
        [$check_in, $check_out] = explode(' - ', $checkincout);
        return [
            date('Y-m-d', strtotime($check_in)),
            date('Y-m-d', strtotime($check_out))
        ];
    }

    // Hotel Price Booking code =========================================================================================>
    public function hotel_price_bookingcode(Request $request, $code,$bcode){
        $checkincout = $request->checkincout;
        $check_in = substr($request->checkincout, 0, 10);
        $check_out = substr($request->checkincout, 13, 23);
        $checkin = date('Y-m-d', strtotime($check_in));
        $checkout = date('Y-m-d', strtotime($check_out));
        $in=Carbon::parse($checkin);
        $out=Carbon::parse($checkout);
        $duration = $in->diffInDays($out);
        $tax = Tax::where('name',"Tax")->first();
        $order = Orders::all();
        $orderno = count($order) + 1;
        $business = BusinessProfile::where('id','=',1)->first();
        $now = date('Y-m-d');
        $datevalidate=date('Y-m-d', strtotime("-1 days", strtotime($checkout)));
        $out_package=date('Y-m-d', strtotime("+".$duration."days", strtotime($checkin)));
        $hotel = Hotels::where("code",$code)->first();
        $nearhotels = Hotels::where('status',"Active")->where('region', $hotel->region)->where('id','!=',$hotel->id)->take(4)->get();
        $optional_rate = OptionalRate::where('service','Hotel')
            ->where('service_id',$hotel->id)->get();
        $hotelrooms = HotelRoom::where('hotels_id','=',$hotel->id)->where('status','Active')->get();
        $usdrates = UsdRates::where('name','USD')->first();
        $room_price = HotelPrice::where('hotels_id',$hotel->id)
        ->get();
        $promos = HotelPromo::where('hotels_id',$hotel->id)
            ->where('status','Active')
            ->where('book_periode_start','<',$now)
            ->where('book_periode_end','>',$now)
            ->where('minimum_stay','<=',$duration)
            ->get();
        $packages = HotelPackage::where('hotels_id',$hotel->id)
            ->where('status','Active')
            ->where('duration','<=',$duration)
            ->where('stay_period_start','<=',$checkin)
            ->where('stay_period_end','>=',$checkin)
            ->get();
        $order_select = Orders::where('user_id', Auth::user()->id)
            ->where('servicename',$hotel->name)
            ->where('checkin','>',$now)->get();
        // $bookingcode = $request->bookingcode;
        $user_id = Auth::user()->id;
        $orders = Orders::where('user_id', $user_id)->get();
        $promotions = Promotion::where('status',"Active")->get();
        $promotion_price = $promotions->sum('discounts');
        $bk_code = BookingCode::where('code', $bcode)->where('status', 'Active')->first();
        $hotel_promotions = HotelPromo::where('hotels_id',$hotel->id)
        ->where('book_periode_start','<=',$now)
        ->where('book_periode_end','>=',$now)
        ->where('status','Active')
        ->get();
        if (isset($bcode)) {
            $bk_code = BookingCode::where('code', $bcode)->where('status', 'Active')->first();
            if (isset($bk_code)) {
                if ($bk_code->used < $bk_code->amount) {
                    if (isset($orders)) {
                        $usedcode = $orders->where('bookingcode', $bk_code->code)->first();
                        if (isset($usedcode)) {
                            $bookingcode_status = "Used";
                            $bookingcode = NULL;
                        }else{
                            if ($bk_code->expired_date >= $now) {
                                $bookingcode_status = "Valid";
                                $bookingcode = $bk_code;
                            }else{
                                $bookingcode_status = "Expired";
                                $bookingcode = NULL ;
                            }
                        }
                    }else{
                        if ($bk_code->expired_date >= $now) {
                            $bookingcode_status = "Valid";
                            $bookingcode = $bk_code;
                        }else{
                            $bookingcode_status = "Expired";
                            $bookingcode = NULL ;
                        }
                    }
                }else{
                    $bookingcode_status = "Expired";
                    $bookingcode = NULL ;
                }
            }else{
                $bookingcode_status = 'Invalid';
                $bookingcode = NULL;
            }
        }else{
            $bookingcode_status = NULL;
            $bookingcode = NULL;
        }
        return view('main.hotelavailability', compact('hotel'),[
            'tax'=>$tax,
            'day'=>$now,
            'packages'=>$packages,
            'promos'=>$promos,
            'order_select'=>$order_select,
            'optional_rate'=>$optional_rate,
            'orderno'=>$orderno,
            'business'=>$business,
            'now'=>$now,
            'room_price'=>$room_price,
            'in'=>$in,
            'out'=>$out,
            'usdrates'=>$usdrates,
            'duration'=>$duration,
            'hotelrooms'=>$hotelrooms,
            'hotel'=>$hotel,
            'checkin'=>$checkin,
            'checkout'=>$checkout,
            'checkincout'=>$checkincout,
            'bookingcode'=>$bookingcode,
            'bookingcode_status'=>$bookingcode_status,
            'promotions'=>$promotions,
            'promotion_price'=>$promotion_price,
            'orders'=>$orders,
            'nearhotels'=>$nearhotels,
            'hotel_promotions'=>$hotel_promotions,
        ]);
    }


// Function add optional rate to Order ======================================================================================= ==>
    public function func_add_optional_service_order(Request $request){
        $service_date = date("Y-m-d", strtotime($request->service_date));
        $hotels_id= $request->hotels_id;
        $code = $request->code;
        $checkin = $request->checkin;
        $checkout = $request->checkout;
        $order =new OptionalRateOrder([
            
            "order_id"=>$request->order_id,
            "type"=>$request->type,
            "name"=>$request->name,
            "qty"=>$request->qty,
            "price_unit"=>$request->price_unit,
            "description" =>$request->description,
            "note"=>$request->note,
            "status"=>$request->status,
            "author"=>$request->author,
            "service_date"=>$service_date,
            "optional_rate_id"=>$request->optional_rate_id,
        ]);
        $order->save();
        // USER LOG
        $note = "Add Optional Rate Order";
        $user_log =new UserLog([
            "action"=>$request->action,
            "service"=>$request->service,
            "subservice"=>$request->subservice,
            "subservice_id"=>$request->subservice_id,
            "page"=>$request->page,
            "user_id"=>$request->author,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        // @dd($order);
        return redirect("/orders")->with('success','Your order has been submited, and we will validate your order',[

       
            'hotels_id'=> $hotels_id,
            'code'=> $code,
            'checkin'=>$checkin,
            'checkout'=>$checkout,
        ])->with('success', 'Package added successfully');
    }

}