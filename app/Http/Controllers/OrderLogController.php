<?php

namespace App\Http\Controllers;

use App\Models\OrderLog;
use App\Http\Requests\StoreOrderLogRequest;
use App\Http\Requests\UpdateOrderLogRequest;

class OrderLogController extends Controller
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
     * @param  \App\Http\Requests\StoreOrderLogRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderLogRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderLog  $orderLog
     * @return \Illuminate\Http\Response
     */
    public function show(OrderLog $orderLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrderLog  $orderLog
     * @return \Illuminate\Http\Response
     */
    public function edit(OrderLog $orderLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderLogRequest  $request
     * @param  \App\Models\OrderLog  $orderLog
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderLogRequest $request, OrderLog $orderLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderLog  $orderLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderLog $orderLog)
    {
        //
    }
}
