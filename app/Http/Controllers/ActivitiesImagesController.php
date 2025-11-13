<?php

namespace App\Http\Controllers;

use App\Models\ActivitiesImages;
use App\Http\Requests\StoreActivitiesImagesRequest;
use App\Http\Requests\UpdateActivitiesImagesRequest;

class ActivitiesImagesController extends Controller
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
     * @param  \App\Http\Requests\StoreActivitiesImagesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreActivitiesImagesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ActivitiesImages  $activityImage
     * @return \Illuminate\Http\Response
     */
    public function show(ActivitiesImages $activityImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ActivitiesImages  $activityImage
     * @return \Illuminate\Http\Response
     */
    public function edit(ActivitiesImages $activityImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateActivitiesImagesRequest  $request
     * @param  \App\Models\ActivitiesImages  $activityImage
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateActivitiesImagesRequest $request, ActivitiesImages $activityImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ActivitiesImages  $activityImage
     * @return \Illuminate\Http\Response
     */
    public function destroy(ActivitiesImages $activityImage)
    {
        //
    }
}
