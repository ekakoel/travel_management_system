<?php

namespace App\Http\Controllers;

use PDF;
use Carbon\Carbon;
use App\Models\Tax;
use App\Models\Tours;
use App\Models\Hotels;
use App\Models\UserLog;
use App\Models\UsdRates;
use App\Models\Attention;
use App\Models\HotelRoom;
use App\Models\HotelPrice;
use App\Models\HotelPromo;
use App\Models\HotelPackage;
use Illuminate\Http\Request;
use App\Models\DownloadDataHotel;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreDownloadDataHotelRequest;
use App\Http\Requests\UpdateDownloadDataHotelRequest;
// use Barryvdh\DomPDF\Facade as PDF;

class DownloadDataHotelController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function index()
    {
        $now = Carbon::now();
        $data_hotels=Hotels::where('status','Active')->get();
        $data_hotel_rooms = HotelRoom::where('status', "Active")->get();
        $attentions = Attention::where('page','download-data')->get();
        $hotel_prices = HotelPrice::where('end_date',">=",$now)->get();
        $hotel_promo = HotelPromo::where('status',"Active")
        ->where('book_periode_end',">=", $now)->get();
        $hotel_package = HotelPackage::where('status',"Active")
        ->where('stay_period_end',">=", $now)->get();
        return view('main.download', compact('data_hotels'),[
            "now" => $now,
            "data_hotels" => $data_hotels,
            "data_hotel_rooms" => $data_hotel_rooms,
            "attentions" => $attentions,
            "hotel_prices" => $hotel_prices,
            "hotel_promo" => $hotel_promo,
            "hotel_package" => $hotel_package,

            // "data_hotel_normal" => $data_hotel_normal,
            // "data_hotel_promo" => $data_hotel_promo,
            // "data_hotel_package" => $data_hotel_package,

        ]);
    }

    
    public function down_data_hotel()
    {
        $now = Carbon::now();
        $data_hotels=Hotels::where('status','Active')->get();
        $data_hotel_rooms = HotelRoom::where('status', "Active")->get();
        $attentions = Attention::where('page','download-data')->get();
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $hr_prices = HotelPrice::all();
        return view('main.downloadhotel', compact('data_hotels'),[
            "now"=>$now,
            "hr_prices"=>$hr_prices,
            "tax" => $tax,
            "usdrates" =>$usdrates,
            "data_hotels" => $data_hotels,
            "data_hotel_rooms" => $data_hotel_rooms,
            "attentions" => $attentions,
            // "data_hotel_normal" => $data_hotel_normal,
            // "data_hotel_promo" => $data_hotel_promo,
            // "data_hotel_package" => $data_hotel_package,

        ]);
    }

    public function down_data_hotel_test()
    {
        $now = Carbon::now();
        $data_hotels=Hotels::where('status','Active')->get();
        $data_hotel_rooms = HotelRoom::where('status', "Active")->get();
        $attentions = Attention::where('page','download-data')->get();
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $hr_prices = HotelPrice::all();
        return view('main.download-test', compact('data_hotels'),[
            "now"=>$now,
            "hr_prices"=>$hr_prices,
            "tax" => $tax,
            "usdrates" =>$usdrates,
            "data_hotels" => $data_hotels,
            "data_hotel_rooms" => $data_hotel_rooms,
            "attentions" => $attentions,
        ]);
    }
    public function down_data_hotel_package()
    {
        $now = Carbon::now();
        $packages = HotelPackage::where('status','Active')
        ->where('stay_period_end',">",  $now)
        ->orderBy('hotels_id', 'asc')
        ->get();
        $hotels = Hotels::where('status', "Active")->get();
        $rooms = HotelRoom::where('status', "Active")->get();
        $attentions = Attention::where('page','download-data')->get();
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $hr_prices = HotelPrice::all();
        return view('main.download-hotel-package', compact('packages'),[
            "now"=>$now,
            "rooms"=>$rooms,
            "hr_prices"=>$hr_prices,
            "tax" => $tax,
            "usdrates" =>$usdrates,
            "packages" => $packages,
            "hotels" => $hotels,
            "attentions" => $attentions,
        ]);
    }
    public function down_data_hotel_promo()
    {
        $now = Carbon::now();
        $promos = HotelPromo::where('status','Active')
        ->where('book_periode_end',">",  $now)
        ->orderBy('hotels_id', 'asc')
        ->get();
        $hotels = Hotels::where('status', "Active")->get();
        $rooms = HotelRoom::where('status', "Active")->get();
        $attentions = Attention::where('page','download-data')->get();
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $hr_prices = HotelPrice::all();
        return view('main.download-hotel-promo', compact('promos'),[
            "now"=>$now,
            "rooms"=>$rooms,
            "hr_prices"=>$hr_prices,
            "tax" => $tax,
            "usdrates" =>$usdrates,
            "promos" => $promos,
            "hotels" => $hotels,
            "attentions" => $attentions,
        ]);
    }
    public function down_data_tour()
    {
        $now = Carbon::now();
        $data_tours=Tours::where('status','Active')->get();
        $attentions = Attention::where('page','download-data')->get();
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        return view('main.download-data-tour', compact('data_tours'),[
            "now"=>$now,
            "tax" => $tax,
            "usdrates" =>$usdrates,
            "data_tours" => $data_tours,
            "attentions" => $attentions,
            // "data_hotel_normal" => $data_hotel_normal,
            // "data_hotel_promo" => $data_hotel_promo,
            // "data_hotel_package" => $data_hotel_package,

        ]);
    }
    
    public function view_download_hotel()
    {
        
        $now = Carbon::now();
        $data_hotels=Hotels::where('status','Active')->get();
        $data_hotel_rooms = HotelRoom::where('status', "Active")->get();
        $attentions = Attention::where('page','download-data')->get();
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $hr_prices = HotelPrice::all();
        return view('main.downloadhotel', compact('data_hotels'),[
            
            "now"=>$now,
            "hr_prices"=>$hr_prices,
            "tax" => $tax,
            "usdrates" =>$usdrates,
            "data_hotels" => $data_hotels,
            "data_hotel_rooms" => $data_hotel_rooms,
            "attentions" => $attentions,
            // "data_hotel_normal" => $data_hotel_normal,
            // "data_hotel_promo" => $data_hotel_promo,
            // "data_hotel_package" => $data_hotel_package,

        ]);
    }

    public function generatePDF()
    {
        $now = Carbon::now();
        $data_hotels=Hotels::where('status','Active')->get();
        $data_hotel_rooms = HotelRoom::where('status', "Active")->get();
        $attentions = Attention::where('page','download-data')->get();
        $usdrates = UsdRates::where('name','USD')->first();
        $tax = Tax::where('id',1)->first();
        $hr_prices = HotelPrice::all();
        $data = [
            "now"=>$now,
            "hr_prices"=>$hr_prices,
            "tax" => $tax,
            "usdrates" =>$usdrates,
            "data_hotels" => $data_hotels,
            "data_hotel_rooms" => $data_hotel_rooms,
            "attentions" => $attentions,
        ]; 
            
        $pdf = PDF::loadView('main.downloadhotel', $data)->setPaper('a4', 'landscape');
     
        return $pdf->download('data_hotel_prices.pdf');
    }

 // Function Add Contract =========================================================================================>
    public function action_log_download_hotel(Request $request){
        // USER LOG
        
        $action = "Download Hotel Pricelist";
        $service = "Download";
        $subservice = "Hotel Pricelist";
        $page = "download-data-hotel";
        $note = "user : ".Auth::user()->name." Download data hotel price";
        $user_log =new UserLog([
            "action"=>$action,
            "service"=>$service,
            "subservice"=>$subservice,
            "subservice_id"=>1,
            "page"=>$page,
            "user_id"=>$request->author,
            "user_ip"=>$request->getClientIp(),
            "note" =>$note, 
        ]);
        $user_log->save();
        return redirect("/download-data-hotel");
    }



    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDownloadDataHotelRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDownloadDataHotelRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DownloadDataHotel  $downloadDataHotel
     * @return \Illuminate\Http\Response
     */
    public function show(DownloadDataHotel $downloadDataHotel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DownloadDataHotel  $downloadDataHotel
     * @return \Illuminate\Http\Response
     */
    public function edit(DownloadDataHotel $downloadDataHotel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDownloadDataHotelRequest  $request
     * @param  \App\Models\DownloadDataHotel  $downloadDataHotel
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDownloadDataHotelRequest $request, DownloadDataHotel $downloadDataHotel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DownloadDataHotel  $downloadDataHotel
     * @return \Illuminate\Http\Response
     */
    public function destroy(DownloadDataHotel $downloadDataHotel)
    {
        //
    }
}
