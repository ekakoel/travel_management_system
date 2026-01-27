<?php

namespace App\Http\Controllers;

use App\Models\OptionalRateOrder;
use App\Http\Requests\StoreOptionalRateOrderRequest;
use App\Http\Requests\UpdateOptionalRateOrderRequest;

class OptionalRateOrderController extends Controller
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
     * @param  \App\Http\Requests\StoreOptionalRateOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOptionalRateOrderRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OptionalRateOrder  $optionalRateOrder
     * @return \Illuminate\Http\Response
     */
    public function show(OptionalRateOrder $optionalRateOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OptionalRateOrder  $optionalRateOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(OptionalRateOrder $optionalRateOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOptionalRateOrderRequest  $request
     * @param  \App\Models\OptionalRateOrder  $optionalRateOrder
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOptionalRateOrderRequest $request, OptionalRateOrder $optionalRateOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OptionalRateOrder  $optionalRateOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(OptionalRateOrder $optionalRateOrder)
    {
        //
    }
}
