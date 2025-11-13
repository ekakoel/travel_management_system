<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Tours;
use App\Models\Orders;
use App\Models\TourType;
use App\Models\UsdRates;
use App\Models\Attention;
use App\Models\Promotion;
use App\Models\TourPrices;
use App\Models\BookingCode;
use App\Models\ToursImages;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\BusinessProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoretoursRequest;
use App\Http\Requests\UpdatetoursRequest;

class ToursController extends Controller

{   
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function index()
    {   
        $tours=Tours::where('status','active')->paginate(12)->withQueryString();
        $promotions = Promotion::where('status',"Active")->get();
        // $toursType = TourType::all();
        $toursType = TourType::whereHas('tours')->get();
        $typeField = match (config('app.locale')) {
            'zh' => 'type_traditional',
            'zh-CN' => 'type_simplified',
            default => 'type',
        };
        $tourNameField = match (config('app.locale')) {
            'zh' => 'name_traditional',
            'zh-CN' => 'name_simplified',
            default => 'name',
        };
        return view('frontend.tours.index', compact('tours'),[
            "promotions" => $promotions,
            "toursType" => $toursType,
            "typeField" => $typeField,
            "tourNameField" => $tourNameField,
        ]);
    }
    public function loadMore(Request $request)
    {
        $tours = $this->getToursQuery($request)->paginate(12);
        $typeField = match (config('app.locale')) {
            'zh' => 'type_traditional',
            'zh-CN' => 'type_simplified',
            default => 'type',
        };
        $tourNameField = match (config('app.locale')) {
            'zh' => 'name_traditional',
            'zh-CN' => 'name_simplified',
            default => 'name',
        };
        $html = view('frontend.tours.partials.tour-list', compact('tours','typeField','tourNameField'))->render();

        return response()->json([
            'html' => $html,
            'hasMore' => $tours->hasMorePages()
        ]);
    }

    private function getToursQuery(Request $request)
    {
        $toursQuery = Tours::where('status', 'Active')
            ->with(['images','prices']);

        if ($request->filled('tour_type')) {
            $toursQuery->where('type_id', $request->tour_type);
        }
        return $toursQuery;
    }
    // Search Tours =========================================================================================>
    public function search_tour(Request $request){
        $now = Carbon::now();
        $user_id = Auth::user()->id;
        $taxes = Tax::where('id',1)->first();
        $usdrates = UsdRates::where('id',1)->first();
        $tour_location = $request->tour_location;
        $tour_type = $request->tour_type;
        $type=TourType::all();
        $orders = Orders::where('user_id', $user_id)->get();
        $bcode = $request->bookingcode;
        $tours = Tours::where('status', '=','Active')
            ->where('location','LIKE','%'.$tour_location.'%')
            ->where('type_id','LIKE', '%'.$tour_type.'%')
            ->paginate(12)->withQueryString();
        $promotions = Promotion::where('status', 'Active')->get();
        $promotion_price = $promotions->sum('discounts');
        $bcode = $request->bookingcode;
        if (isset($bcode)) {
            try {
                $bk_code = BookingCode::where('code', $bcode)
                    ->where('status', 'Active')
                    ->firstOrFail();
                if ($bk_code->used < $bk_code->amount) {
                    $usedcode = $orders->where('bookingcode', $bk_code->code)->first();
                    if (isset($usedcode)) {
                        $bookingcode_status = "Used";
                        $bookingcode = "";
                    } elseif ($bk_code->expired_date >= $now) {
                        $bookingcode_status = "Valid";
                        $bookingcode = $bk_code;
                    } else {
                        $bookingcode_status = "Expired";
                        $bookingcode = "";
                    }
                } else {
                    $bookingcode_status = "Expired";
                    $bookingcode = "";
                }
            } catch (ModelNotFoundException $e) {
                $bookingcode_status = 'Invalid';
                $bookingcode = "";
            }
        }else{
            $bookingcode_status = "";
            $bookingcode = "";
        }
        return view('main.toursearch', compact('tours'),[
            'tour_location'=>$tour_location,
            'tour_type'=>$tour_type,
            'type'=>$type,
            'promotion_price'=>$promotion_price,
            'bookingcode'=>$bookingcode,
            'bookingcode_status'=>$bookingcode_status,
            'usdrates'=>$usdrates,
            'taxes'=>$taxes,
            'promotions'=>$promotions,
        ]);
    }
    public function view_tour_detail($slug)
    {
        $user_id = Auth::id();
        $now = Carbon::now();
        $tax = Cache::remember('tax_1', 3600, fn() => Tax::find(1));
        $usdrates = Cache::remember('usd_rate', 3600, fn() => UsdRates::where('name', 'USD')->first());
        $business = Cache::remember('business_profile', 3600, fn() => BusinessProfile::find(1));

        $tour = Tours::with(['images','prices'])->where('slug',$slug)->first();
        $tour->prices->transform(function ($price) use ($usdrates, $tax) {
            $price->calculated_price = $price->calculatePrice($usdrates, $tax);
            return $price;
        });
        $prices = $tour->prices()->where('status', 'Active')->get()->map(function ($p) use ($usdrates, $tax) {
            return [
                'min_qty' => $p->min_qty,
                'max_qty' => $p->max_qty,
                'price' => $p->calculatePrice($usdrates, $tax),
            ];
        });

        $orders = Orders::all();
        $ordernotours = count($orders) + 1;
        $attentions = Attention::where('page','tour-detail')->get();
        $agents = Auth::user()->where('status','Active')->get();
        $neartours = Tours::where('status',"Active")
        ->where('slug','!=',$slug)
        ->where('type_id',$tour->type_id)
        ->take(4)
        ->get();

        $promotions = Promotion::where('status',"Active")->get();
        if (isset($promotions)){
            $pr = count($promotions);
            $promotion_price = 0;
            $count_promotions = count($promotions);
            for ($i=0; $i < $pr; $i++) { 
                $promotion_price = $promotion_price + $promotions[$i]->discounts;
            }
            $promotion_price = $promotion_price;
        }else{
            $promotion_price = 0;
            $count_promotions = 0;
        }
        if (session('bookingcode')) {
            $bookingcode = session('bookingcode');
            $bookingcode_disc = session('bookingcode.disc');
        }else{
            $bookingcode = null;
            $bookingcode_disc = 0;
        }
        $langType = match (config('app.locale')) {
            'zh' => 'type_traditional',
            'zh-CN' => 'type_simplified',
            default => 'type',
        };
        $langName = match (config('app.locale')) {
            'zh' => 'name_traditional',
            'zh-CN' => 'name_simplified',
            default => 'name',
        };
        $langShortDescription = match (config('app.locale')) {
            'zh' => 'short_description_traditional',
            'zh-CN' => 'short_description_simplified',
            default => 'short_description',
        };
        $langDescription = match (config('app.locale')) {
            'zh' => 'description_traditional',
            'zh-CN' => 'description_simplified',
            default => 'description',
        };
        $langItinerary = match (config('app.locale')) {
            'zh' => 'itinerary_traditional',
            'zh-CN' => 'itinerary_simplified',
            default => 'itinerary',
        };
        $langInclude = match (config('app.locale')) {
            'zh' => 'include_traditional',
            'zh-CN' => 'include_simplified',
            default => 'include',
        };
        $langExclude = match (config('app.locale')) {
            'zh' => 'exclude_traditional',
            'zh-CN' => 'exclude_simplified',
            default => 'exclude',
        };
        $langAdditionalInfo = match (config('app.locale')) {
            'zh' => 'additional_info_traditional',
            'zh-CN' => 'additional_info_simplified',
            default => 'additional_info',
        };
        $langCancellationPolicy = match (config('app.locale')) {
            'zh' => 'cancellation_policy_traditional',
            'zh-CN' => 'cancellation_policy_simplified',
            default => 'cancellation_policy',
        };
        return view('frontend.tours.detail',[
            'tax'=>$tax,
            'usdrates'=>$usdrates,
            'agents'=>$agents,
            'attentions'=>$attentions,
            'ordernotours' => $ordernotours,
            'tour'=>$tour,
            'neartours'=>$neartours,
            'now'=>$now,
            'business'=>$business,
            'bookingcode'=>$bookingcode,
            'bookingcode_disc'=>$bookingcode_disc,
            'promotions'=>$promotions,
            'promotion_price'=>$promotion_price,
            'count_promotions'=>$count_promotions,
            'langType'=>$langType,
            'langName'=>$langName,
            'langShortDescription'=>$langShortDescription,
            'langDescription'=>$langDescription,
            'langItinerary'=>$langItinerary,
            'langInclude'=>$langInclude,
            'langExclude'=>$langExclude,
            'langAdditionalInfo'=>$langAdditionalInfo,
            'langCancellationPolicy'=>$langCancellationPolicy,
            'prices'=>$prices,


        ]);
    }
    public function tour_check_code(Request $request){
        $now = Carbon::now();
        $tour = Tours::where('id',$request->tour_id)->first();
        $bcode = $request->bookingcode;
        $user_id = Auth::user()->id;
        $orders = Orders::where('user_id', $user_id)->get();
        $bk_code = BookingCode::where('code', $bcode)->where('status', 'Active')->first();
        if (isset($bk_code)) {
            if ($bk_code->used < $bk_code->amount) {
                if (isset($orders)) {
                    $usedcode = $orders->where('bookingcode', $bk_code->code)->first();
                    if (isset($usedcode)) {
                        $bookingcode_status = "Used";
                        $bookingcode = null;
                    }else{
                        if ($bk_code->expired_date >= $now) {
                            $bookingcode_status = "Valid";
                            $bookingcode = $bk_code;
                        }else{
                            $bookingcode_status = "Expired";
                            $bookingcode = null ;
                        }
                    }
                }else{
                    if ($bk_code->expired_date >= $now) {
                        $bookingcode_status = "Valid";
                        $bookingcode = $bk_code;
                    }else{
                        $bookingcode_status = "Expired";
                        $bookingcode = null ;
                    }
                }
            }else{
                $bookingcode_status = "Expired";
                $bookingcode = null ;
            }
        }else{
            $bookingcode_status = 'Invalid';
            $bookingcode = null;
        }
        if (isset($bookingcode)) {
            return redirect("/tour-$tour->code-$bookingcode->code");
        }else{
            return redirect("/tour-$tour->code")->with('danger', $bookingcode_status.' Code');
        }
    }

    public function view_tour_detail_bookingcode($code,$bcode)
    {
        $business = BusinessProfile::where('id','=',1)->first();
        $taxes = Tax::where('id',1)->first();
        $now = Carbon::now();
        $tour = Tours::where('code',$code)->first();
        $orders = Orders::all();
        $ordernotours = count($orders) + 1;
        $attentions = Attention::where('page','tour-detail')->get();
        $usdrates = UsdRates::where('name','USD')->first();
        $agents = Auth::user()->where('status','Active')->get();
        $neartours = Tours::where('status',"Active")
        ->where('code','!=',$code)
        ->get();
        $tour_prices = TourPrices::where('tour_id',$tour->id)
        ->where('status',"Active")->get();
        $promotions = Promotion::where('status',"Active")->get();
        if (isset($promotions)) {
            $count_promotions = count($promotions);
        }else{
            $count_promotions = 0;
        }
        if (isset($bcode)) {
            $user_id = Auth::user()->id;
            $orders = Orders::where('user_id', $user_id)->get();
            $bk_code = BookingCode::where('code', $bcode)->where('status', 'Active')->first();
            if (isset($bk_code)) {
                if ($bk_code->used < $bk_code->amount) {
                    if (isset($orders)) {
                        $usedcode = $orders->where('bookingcode', $bk_code->code)->first();
                        if (isset($usedcode)) {
                            $bookingcode_status = "Used";
                            $bookingcode = null;
                        }else{
                            if ($bk_code->expired_date >= $now) {
                                $bookingcode_status = "Valid";
                                $bookingcode = $bk_code;
                            }else{
                                $bookingcode_status = "Expired";
                                $bookingcode = null ;
                            }
                        }
                    }else{
                        if ($bk_code->expired_date >= $now) {
                            $bookingcode_status = "Valid";
                            $bookingcode = $bk_code;
                        }else{
                            $bookingcode_status = "Expired";
                            $bookingcode = null ;
                        }
                    }
                }else{
                    $bookingcode_status = "Expired";
                    $bookingcode = null ;
                }
            }else{
                $bookingcode_status = 'Invalid';
                $bookingcode = null;
            }
        }else{
            $bookingcode_status = null;
            $bookingcode = null;
        }

        if (isset($promotions)){
            $pr = count($promotions);
            $promotion_price = 0;
            for ($i=0; $i < $pr; $i++) { 
                $promotion_price = $promotion_price + $promotions[$i]->discounts;
            }
        }else{
            $promotion_price = 0;
        }

        $price_non_tax = (ceil($tour->contract_rate / $usdrates->rate))+$tour->markup;
        $tax = ceil(($taxes->tax/100) * $price_non_tax);
        $normal_price = ($price_non_tax + $tax);
        $qty = TourPrices::max('max_qty');
        if (isset($bookingcode->code) or isset($promotions)) {
            if (isset($bookingcode->code)) {
                $price_per_pax = $normal_price;
                
                if (isset($promotions)) {
                    $final_price = $normal_price - $bookingcode->discounts - $promotion_price;
                }else{
                    $final_price = $normal_price - $bookingcode->discounts;
                }
            }else{
                $price_per_pax = $normal_price ;
                $final_price = $normal_price  - $promotion_price;
            }
        }else {
            $price_per_pax = $normal_price;
            $final_price = $normal_price;
        }
        if (isset($bookingcode)) {
            return view('main.tourdetail',[
                'taxes'=>$taxes,
                'usdrates'=>$usdrates,
                'agents'=>$agents,
                'attentions'=>$attentions,
                'ordernotours' => $ordernotours,
                'tour'=>$tour,
                'neartours'=>$neartours,
                'now'=>$now,
                'business'=>$business,
                'bookingcode'=>$bookingcode,
                'bookingcode_status'=>$bookingcode_status,
                'promotions'=>$promotions,
                'promotion_price'=>$promotion_price,
                'count_promotions'=>$count_promotions,
                'tour_prices'=>$tour_prices,
                'qty'=>$qty,
                'fprice'=>$final_price,
            ]);
        }else{
            return redirect("/tour-$code")->with('danger','The booking code that you entered has been used!');
        }
    }

    
}
