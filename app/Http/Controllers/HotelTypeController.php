<?php

namespace App\Http\Controllers;

use App\Models\HotelType;
use App\Http\Requests\StoreHotelTypeRequest;
use App\Http\Requests\UpdateHotelTypeRequest;

class HotelTypeController extends Controller
{
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
     * @param  \App\Http\Requests\StoreHotelTypeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHotelTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HotelType  $hotelType
     * @return \Illuminate\Http\Response
     */
    public function show(HotelType $hotelType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HotelType  $hotelType
     * @return \Illuminate\Http\Response
     */
    public function edit(HotelType $hotelType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateHotelTypeRequest  $request
     * @param  \App\Models\HotelType  $hotelType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateHotelTypeRequest $request, HotelType $hotelType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HotelType  $hotelType
     * @return \Illuminate\Http\Response
     */
    public function destroy(HotelType $hotelType)
    {
        //
    }
}
