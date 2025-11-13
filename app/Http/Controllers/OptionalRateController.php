<?php

namespace App\Http\Controllers;

use App\Models\OptionalRate;
use App\Http\Requests\StoreOptionalRateRequest;
use App\Http\Requests\UpdateOptionalRateRequest;

class OptionalRateController extends Controller
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
     * @param  \App\Http\Requests\StoreOptionalRateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOptionalRateRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OptionalRate  $optionalRate
     * @return \Illuminate\Http\Response
     */
    public function show(OptionalRate $optionalRate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OptionalRate  $optionalRate
     * @return \Illuminate\Http\Response
     */
    public function edit(OptionalRate $optionalRate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOptionalRateRequest  $request
     * @param  \App\Models\OptionalRate  $optionalRate
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOptionalRateRequest $request, OptionalRate $optionalRate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OptionalRate  $optionalRate
     * @return \Illuminate\Http\Response
     */
    public function destroy(OptionalRate $optionalRate)
    {
        //
    }
}
