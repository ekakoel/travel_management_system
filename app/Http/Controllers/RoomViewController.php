<?php

namespace App\Http\Controllers;

use App\Models\RoomView;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRoomViewRequest;
use App\Http\Requests\UpdateRoomViewRequest;

class RoomViewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomViewRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(RoomView $roomView)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RoomView $roomView)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomViewRequest $request, RoomView $roomView)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoomView $roomView)
    {
        //
    }

    public function autocomplete(Request $request)
    {
        $query = $request->input('query');
        $views = RoomView::where('name', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name']);
    
        return response()->json(['views' => $views]);
    }
}
