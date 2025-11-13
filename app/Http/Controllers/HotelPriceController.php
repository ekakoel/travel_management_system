<?php

namespace App\Http\Controllers;

use App\Models\HotelPrice;
use App\Http\Requests\StoreHotelPriceRequest;
use App\Http\Requests\UpdateHotelPriceRequest;

class HotelPriceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreHotelPriceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHotelPriceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HotelPrice  $hotelPrice
     * @return \Illuminate\Http\Response
     */
    public function show(HotelPrice $hotelPrice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HotelPrice  $hotelPrice
     * @return \Illuminate\Http\Response
     */
    public function edit(HotelPrice $hotelPrice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateHotelPriceRequest  $request
     * @param  \App\Models\HotelPrice  $hotelPrice
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateHotelPriceRequest $request, HotelPrice $hotelPrice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HotelPrice  $hotelPrice
     * @return \Illuminate\Http\Response
     */
    public function destroy(HotelPrice $hotelPrice)
    {
        //
    }
}
