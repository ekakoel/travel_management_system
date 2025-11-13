<?php

namespace App\Http\Controllers;

use App\Models\Hotels;
use App\Models\HotelRoom;
use App\Models\HotelPrice;
use App\Models\HotelPromo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Requests\StoreHotelPromoRequest;
use App\Http\Requests\UpdateHotelPromoRequest;

class HotelPromoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    public function index($id, $checkin, $checkout)
    {
        $hotelid=$id;
        $now = Carbon::now();
        $hotel = Hotels::findOrFail($id);
        $hotelrooms = HotelRoom::where('hotels_id','=',$id)
        ->get();
        $datevalidate=date('Y-m-d', strtotime("-1 days", strtotime($checkout)));

        $in=Carbon::parse($checkin);
        $out=Carbon::parse($checkout);
        $duration = $in->diffInDays($out);
        $data = HotelPromo::where('hotels_id','=',$id);
        $lowerprice = $data->where('price','>',0)->min('price');
        $rooms = HotelRoom::where('hotels_id','=',$id)
        ->get();
        $promo = HotelPromo::where('hotels_id',$id)
        ->where('periode_start','>=',$checkin)
        ->where('book_periode_start', '>=', $now)
        ->get();
        return view('main.hotelpromo', compact('hotel'),[
            'hotelid'=>$hotelid,
            'promo'=>$promo,
            'rooms'=>$rooms,
            'datevalidate'=>$datevalidate,
            'checkin'=>$checkin,
            'checkout'=>$checkout,
            'duration'=>$duration,
            'now'=>$now,
            'hotel'=>$hotel,
            'lowerprice'=>$lowerprice,
            'hotelrooms'=>$hotelrooms,
        ]);
    }

// Detail Hotel =========================================================================================>
    public function hotelpromo(Request $request)
        {
            $validated = $request->validate([
                'checkin' => 'required|date|before:checkout',
                'checkout' => 'required|date|after:checkin',
                'hotelid' => 'required',
            ]);

            $now = Carbon::now();
            $checkin = $request->get('checkin');
            $checkout = $request->get('checkout');
            $hotelid = $request->get('hotelid');
            $datein=date('Y-m-d', strtotime($checkin));
            $dateout=date('Y-m-d', strtotime($checkout));
            $datevalidate=date('Y-m-d', strtotime("-1 days", strtotime($checkout)));
            $in=Carbon::parse($checkin);
            $out=Carbon::parse($checkout);
            $duration = $in->diffInDays($out);
            $now = Carbon::now();
            $hotel = Hotels::findOrFail($hotelid);
            $hotelrooms = HotelRoom::where('hotels_id','=',$hotelid)->get();
            $in=Carbon::parse($checkin);
            $out=Carbon::parse($checkout);
            $duration = $in->diffInDays($out);
            $data = HotelPromo::where('hotels_id','=',$hotelid);
            $lowerprice = $data->where('price','>',0)->min('price');
            $rooms = HotelRoom::where('hotels_id','=',$hotelid)
            ->get();
            $promo = HotelPromo::where('hotels_id',$hotelid)
            ->where('periode_start','>=',$checkout)
            ->where('book_periode_start', '>=', $now)
            ->get();
            return view('main.hotelpromo', compact('hotel'),[
                'promo'=>$promo,
                'rooms'=>$rooms,
                'datevalidate'=>$datevalidate,
                    'checkin'=>$checkin,
                    'checkout'=>$checkout,
                    'duration'=>$duration,
                    'now'=>$now,
                    'hotel'=>$hotel,
                    'lowerprice'=>$lowerprice,
                    'hotelrooms'=>$hotelrooms,
                ]);
            }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreHotelPromoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHotelPromoRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HotelPromo  $hotelPromo
     * @return \Illuminate\Http\Response
     */
    public function show(HotelPromo $hotelPromo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HotelPromo  $hotelPromo
     * @return \Illuminate\Http\Response
     */
    public function edit(HotelPromo $hotelPromo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateHotelPromoRequest  $request
     * @param  \App\Models\HotelPromo  $hotelPromo
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateHotelPromoRequest $request, HotelPromo $hotelPromo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HotelPromo  $hotelPromo
     * @return \Illuminate\Http\Response
     */
    public function destroy(HotelPromo $hotelPromo)
    {
        //
    }
}
