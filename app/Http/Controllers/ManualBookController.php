<?php

namespace App\Http\Controllers;

use App\Models\ManualBook;
use App\Http\Requests\StoreManualBookRequest;
use App\Http\Requests\UpdateManualBookRequest;

class ManualBookController extends Controller
{
    public function index()
    {
        $manual_book = ManualBook::all();
        return view('main.manual-book',[
            'manual_book'=>$manual_book,
        ]);
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
     * @param  \App\Http\Requests\StoreManualBookRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreManualBookRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ManualBook  $manualBook
     * @return \Illuminate\Http\Response
     */
    public function show(ManualBook $manualBook)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ManualBook  $manualBook
     * @return \Illuminate\Http\Response
     */
    public function edit(ManualBook $manualBook)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateManualBookRequest  $request
     * @param  \App\Models\ManualBook  $manualBook
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateManualBookRequest $request, ManualBook $manualBook)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ManualBook  $manualBook
     * @return \Illuminate\Http\Response
     */
    public function destroy(ManualBook $manualBook)
    {
        //
    }
}
