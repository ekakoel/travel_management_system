<?php

namespace App\Http\Controllers;

use App\Models\RoomFacilities;
use App\Http\Requests\StoreRoomFacilitiesRequest;
use App\Http\Requests\UpdateRoomFacilitiesRequest;

class RoomFacilitiesController extends Controller
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
     * @param  \App\Http\Requests\StoreRoomFacilitiesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoomFacilitiesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RoomFacilities  $roomFacilities
     * @return \Illuminate\Http\Response
     */
    public function show(RoomFacilities $roomFacilities)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RoomFacilities  $roomFacilities
     * @return \Illuminate\Http\Response
     */
    public function edit(RoomFacilities $roomFacilities)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRoomFacilitiesRequest  $request
     * @param  \App\Models\RoomFacilities  $roomFacilities
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoomFacilitiesRequest $request, RoomFacilities $roomFacilities)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RoomFacilities  $roomFacilities
     * @return \Illuminate\Http\Response
     */
    public function destroy(RoomFacilities $roomFacilities)
    {
        //
    }
}
