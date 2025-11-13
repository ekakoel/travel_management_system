<?php

namespace App\Http\Controllers;

use App\Models\TransportsImages;
use App\Http\Requests\StoreTransportsImagesRequest;
use App\Http\Requests\UpdateTransportsImagesRequest;

class TransportsImagesController extends Controller
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
     * @param  \App\Http\Requests\StoreTransportsImagesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTransportsImagesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TransportsImages  $transportsImages
     * @return \Illuminate\Http\Response
     */
    public function show(TransportsImages $transportsImages)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TransportsImages  $transportsImages
     * @return \Illuminate\Http\Response
     */
    public function edit(TransportsImages $transportsImages)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTransportsImagesRequest  $request
     * @param  \App\Models\TransportsImages  $transportsImages
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTransportsImagesRequest $request, TransportsImages $transportsImages)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TransportsImages  $transportsImages
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransportsImages $transportsImages)
    {
        //
    }
}
