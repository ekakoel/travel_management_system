<?php

namespace App\Http\Controllers;

use App\Models\Markup;
use App\Http\Requests\StoreMarkupRequest;
use App\Http\Requests\UpdateMarkupRequest;

class MarkupController extends Controller
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
     * @param  \App\Http\Requests\StoreMarkupRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMarkupRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Markup  $markup
     * @return \Illuminate\Http\Response
     */
    public function show(Markup $markup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Markup  $markup
     * @return \Illuminate\Http\Response
     */
    public function edit(Markup $markup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMarkupRequest  $request
     * @param  \App\Models\Markup  $markup
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMarkupRequest $request, Markup $markup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Markup  $markup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Markup $markup)
    {
        //
    }
}
