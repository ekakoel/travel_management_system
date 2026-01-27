<?php

namespace App\Http\Controllers;

use App\Models\OrderNote;
use App\Http\Requests\StoreOrderNoteRequest;
use App\Http\Requests\UpdateOrderNoteRequest;

class OrderNoteController extends Controller
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
     * @param  \App\Http\Requests\StoreOrderNoteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderNoteRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderNote  $orderNote
     * @return \Illuminate\Http\Response
     */
    public function show(OrderNote $orderNote)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrderNote  $orderNote
     * @return \Illuminate\Http\Response
     */
    public function edit(OrderNote $orderNote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderNoteRequest  $request
     * @param  \App\Models\OrderNote  $orderNote
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderNoteRequest $request, OrderNote $orderNote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderNote  $orderNote
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderNote $orderNote)
    {
        //
    }
}
