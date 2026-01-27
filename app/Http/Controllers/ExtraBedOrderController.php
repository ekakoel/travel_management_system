<?php

namespace App\Http\Controllers;

use App\Models\ExtraBedOrder;
use App\Http\Requests\StoreExtraBedOrderRequest;
use App\Http\Requests\UpdateExtraBedOrderRequest;

class ExtraBedOrderController extends Controller
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
     * @param  \App\Http\Requests\StoreExtraBedOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreExtraBedOrderRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExtraBedOrder  $extraBedOrder
     * @return \Illuminate\Http\Response
     */
    public function show(ExtraBedOrder $extraBedOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExtraBedOrder  $extraBedOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(ExtraBedOrder $extraBedOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateExtraBedOrderRequest  $request
     * @param  \App\Models\ExtraBedOrder  $extraBedOrder
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExtraBedOrderRequest $request, ExtraBedOrder $extraBedOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExtraBedOrder  $extraBedOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExtraBedOrder $extraBedOrder)
    {
        //
    }
}
