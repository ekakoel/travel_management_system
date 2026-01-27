<?php

namespace App\Http\Controllers;

use App\Models\TransportType;
use App\Http\Requests\StoreTransportTypeRequest;
use App\Http\Requests\UpdateTransportTypeRequest;

class TransportTypeController extends Controller
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
     * @param  \App\Http\Requests\StoreTransportTypeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTransportTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TransportType  $transportType
     * @return \Illuminate\Http\Response
     */
    public function show(TransportType $transportType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TransportType  $transportType
     * @return \Illuminate\Http\Response
     */
    public function edit(TransportType $transportType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTransportTypeRequest  $request
     * @param  \App\Models\TransportType  $transportType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTransportTypeRequest $request, TransportType $transportType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TransportType  $transportType
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransportType $transportType)
    {
        //
    }
}
