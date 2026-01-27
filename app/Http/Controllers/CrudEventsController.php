<?php

namespace App\Http\Controllers;

use App\Models\CrudEvents;
use App\Http\Requests\StoreCrudEventsRequest;
use App\Http\Requests\UpdateCrudEventsRequest;

class CrudEventsController extends Controller
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
     * @param  \App\Http\Requests\StoreCrudEventsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCrudEventsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CrudEvents  $crudEvents
     * @return \Illuminate\Http\Response
     */
    public function show(CrudEvents $crudEvents)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CrudEvents  $crudEvents
     * @return \Illuminate\Http\Response
     */
    public function edit(CrudEvents $crudEvents)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCrudEventsRequest  $request
     * @param  \App\Models\CrudEvents  $crudEvents
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCrudEventsRequest $request, CrudEvents $crudEvents)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CrudEvents  $crudEvents
     * @return \Illuminate\Http\Response
     */
    public function destroy(CrudEvents $crudEvents)
    {
        //
    }
}
