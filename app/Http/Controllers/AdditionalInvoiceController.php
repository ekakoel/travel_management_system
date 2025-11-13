<?php

namespace App\Http\Controllers;

use App\Models\AdditionalInvoice;
use App\Http\Requests\StoreAdditionalInvoiceRequest;
use App\Http\Requests\UpdateAdditionalInvoiceRequest;

class AdditionalInvoiceController extends Controller
{
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
     * @param  \App\Http\Requests\StoreAdditionalInvoiceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdditionalInvoiceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AdditionalInvoice  $additionalInvoice
     * @return \Illuminate\Http\Response
     */
    public function show(AdditionalInvoice $additionalInvoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AdditionalInvoice  $additionalInvoice
     * @return \Illuminate\Http\Response
     */
    public function edit(AdditionalInvoice $additionalInvoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAdditionalInvoiceRequest  $request
     * @param  \App\Models\AdditionalInvoice  $additionalInvoice
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdditionalInvoiceRequest $request, AdditionalInvoice $additionalInvoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AdditionalInvoice  $additionalInvoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(AdditionalInvoice $additionalInvoice)
    {
        //
    }
}
