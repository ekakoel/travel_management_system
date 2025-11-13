<?php

namespace App\Http\Controllers;

use App\Models\TourType;
use App\Http\Requests\StoreTourTypeRequest;
use App\Http\Requests\UpdateTourTypeRequest;

class TourTypeController extends Controller
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
     * @param  \App\Http\Requests\StoreTourTypeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTourTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TourType  $tourType
     * @return \Illuminate\Http\Response
     */
    public function show(TourType $tourType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TourType  $tourType
     * @return \Illuminate\Http\Response
     */
    public function edit(TourType $tourType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTourTypeRequest  $request
     * @param  \App\Models\TourType  $tourType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTourTypeRequest $request, TourType $tourType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TourType  $tourType
     * @return \Illuminate\Http\Response
     */
    public function destroy(TourType $tourType)
    {
        //
    }
}
