<?php

namespace App\Http\Controllers;

use App\Models\HotelPackage;
use App\Http\Requests\StoreHotelPackageRequest;
use App\Http\Requests\UpdateHotelPackageRequest;

class HotelPackageController extends Controller
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
     * @param  \App\Http\Requests\StoreHotelPackageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHotelPackageRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HotelPackage  $hotelPackage
     * @return \Illuminate\Http\Response
     */
    public function show(HotelPackage $hotelPackage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HotelPackage  $hotelPackage
     * @return \Illuminate\Http\Response
     */
    public function edit(HotelPackage $hotelPackage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateHotelPackageRequest  $request
     * @param  \App\Models\HotelPackage  $hotelPackage
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateHotelPackageRequest $request, HotelPackage $hotelPackage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HotelPackage  $hotelPackage
     * @return \Illuminate\Http\Response
     */
    public function destroy(HotelPackage $hotelPackage)
    {
        //
    }
}
