<?php

namespace App\Http\Controllers;

use App\Models\Brides;
use App\Http\Requests\StoreBridesRequest;
use App\Http\Requests\UpdateBridesRequest;

class BridesController extends Controller
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
     * @param  \App\Http\Requests\StoreBridesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBridesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Brides  $brides
     * @return \Illuminate\Http\Response
     */
    public function show(Brides $brides)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Brides  $brides
     * @return \Illuminate\Http\Response
     */
    public function edit(Brides $brides)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBridesRequest  $request
     * @param  \App\Models\Brides  $brides
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBridesRequest $request, Brides $brides)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Brides  $brides
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brides $brides)
    {
        //
    }
}
