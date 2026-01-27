<?php

namespace App\Http\Controllers;

use App\Models\LogData;
use App\Http\Requests\StoreLogDataRequest;
use App\Http\Requests\UpdateLogDataRequest;

class LogDataController extends Controller
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
     * @param  \App\Http\Requests\StoreLogDataRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLogDataRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LogData  $logData
     * @return \Illuminate\Http\Response
     */
    public function show(LogData $logData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LogData  $logData
     * @return \Illuminate\Http\Response
     */
    public function edit(LogData $logData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLogDataRequest  $request
     * @param  \App\Models\LogData  $logData
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLogDataRequest $request, LogData $logData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LogData  $logData
     * @return \Illuminate\Http\Response
     */
    public function destroy(LogData $logData)
    {
        //
    }
}
