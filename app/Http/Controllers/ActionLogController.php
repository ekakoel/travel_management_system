<?php

namespace App\Http\Controllers;

use App\Models\ActionLog;
use App\Http\Requests\StoreActionLogRequest;
use App\Http\Requests\UpdateActionLogRequest;

class ActionLogController extends Controller
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
     * @param  \App\Http\Requests\StoreActionLogRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreActionLogRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ActionLog  $actionLog
     * @return \Illuminate\Http\Response
     */
    public function show(ActionLog $actionLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ActionLog  $actionLog
     * @return \Illuminate\Http\Response
     */
    public function edit(ActionLog $actionLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateActionLogRequest  $request
     * @param  \App\Models\ActionLog  $actionLog
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateActionLogRequest $request, ActionLog $actionLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ActionLog  $actionLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(ActionLog $actionLog)
    {
        //
    }
}
