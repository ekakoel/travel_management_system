<?php

namespace App\Http\Controllers;

use App\Models\BusinessProfile;
use App\Http\Requests\StoreBusinessProfileRequest;
use App\Http\Requests\UpdateBusinessProfileRequest;

class BusinessProfileController extends Controller
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
     * @param  \App\Http\Requests\StoreBusinessProfileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBusinessProfileRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BusinessProfile  $businessProfile
     * @return \Illuminate\Http\Response
     */
    public function show(BusinessProfile $businessProfile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BusinessProfile  $businessProfile
     * @return \Illuminate\Http\Response
     */
    public function edit(BusinessProfile $businessProfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBusinessProfileRequest  $request
     * @param  \App\Models\BusinessProfile  $businessProfile
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBusinessProfileRequest $request, BusinessProfile $businessProfile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BusinessProfile  $businessProfile
     * @return \Illuminate\Http\Response
     */
    public function destroy(BusinessProfile $businessProfile)
    {
        //
    }
}
