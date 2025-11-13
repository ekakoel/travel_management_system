<?php

namespace App\Http\Controllers;

use App\Models\TransportBrand;
use App\Http\Requests\StoreTransportBrandRequest;
use App\Http\Requests\UpdateTransportBrandRequest;

class TransportBrandController extends Controller
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
     * @param  \App\Http\Requests\StoreTransportBrandRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTransportBrandRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TransportBrand  $transportBrand
     * @return \Illuminate\Http\Response
     */
    public function show(TransportBrand $transportBrand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TransportBrand  $transportBrand
     * @return \Illuminate\Http\Response
     */
    public function edit(TransportBrand $transportBrand)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTransportBrandRequest  $request
     * @param  \App\Models\TransportBrand  $transportBrand
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTransportBrandRequest $request, TransportBrand $transportBrand)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TransportBrand  $transportBrand
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransportBrand $transportBrand)
    {
        //
    }
}
