<?php

namespace App\Http\Controllers;

use App\Models\HotelRoom;
use App\Http\Requests\StoreHotelRoomRequest;
use App\Http\Requests\UpdateHotelRoomRequest;

class HotelRoomController extends Controller
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
     * @param  \App\Http\Requests\StoreHotelRoomRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHotelRoomRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HotelRoom  $hotelRoom
     * @return \Illuminate\Http\Response
     */
    public function show(HotelRoom $hotelRoom)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HotelRoom  $hotelRoom
     * @return \Illuminate\Http\Response
     */
    public function edit(HotelRoom $hotelRoom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateHotelRoomRequest  $request
     * @param  \App\Models\HotelRoom  $hotelRoom
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateHotelRoomRequest $request, HotelRoom $hotelRoom)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HotelRoom  $hotelRoom
     * @return \Illuminate\Http\Response
     */
    public function destroy(HotelRoom $hotelRoom)
    {
        //
    }
}
