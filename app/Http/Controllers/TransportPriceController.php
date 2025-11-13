<?php

namespace App\Http\Controllers;

use App\Models\TransportPrice;
use App\Http\Requests\StoreTransportPriceRequest;
use App\Http\Requests\UpdateTransportPriceRequest;

class TransportPriceController extends Controller
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
     * @param  \App\Http\Requests\StoreTransportPriceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTransportPriceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TransportPrice  $transportPrice
     * @return \Illuminate\Http\Response
     */
    public function show(TransportPrice $transportPrice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TransportPrice  $transportPrice
     * @return \Illuminate\Http\Response
     */
    public function edit(TransportPrice $transportPrice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTransportPriceRequest  $request
     * @param  \App\Models\TransportPrice  $transportPrice
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTransportPriceRequest $request, TransportPrice $transportPrice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TransportPrice  $transportPrice
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransportPrice $transportPrice)
    {
        //
    }
}
