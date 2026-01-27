<?php

namespace App\Http\Controllers;

use App\Models\AdditionalService;
use App\Http\Requests\StoreAdditionalServiceRequest;
use App\Http\Requests\UpdateAdditionalServiceRequest;

class AdditionalServiceController extends Controller
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
     * @param  \App\Http\Requests\StoreAdditionalServiceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdditionalServiceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AdditionalService  $additionalService
     * @return \Illuminate\Http\Response
     */
    public function show(AdditionalService $additionalService)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AdditionalService  $additionalService
     * @return \Illuminate\Http\Response
     */
    public function edit(AdditionalService $additionalService)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAdditionalServiceRequest  $request
     * @param  \App\Models\AdditionalService  $additionalService
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdditionalServiceRequest $request, AdditionalService $additionalService)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AdditionalService  $additionalService
     * @return \Illuminate\Http\Response
     */
    public function destroy(AdditionalService $additionalService)
    {
        //
    }
}
