<?php

namespace App\Http\Controllers;

use App\Models\BedType;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBedTypeRequest;
use App\Http\Requests\UpdateBedTypeRequest;

class BedTypeController extends Controller
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
    public function store(StoreBedTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(BedType $bedType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BedType $bedType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBedTypeRequest $request, BedType $bedType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BedType $bedType)
    {
        //
    }
    public function autocomplete(Request $request)
    {
        $query = $request->input('query');
        $beds = BedType::where('name', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name']);
    
        return response()->json(['beds' => $beds]);
    }
}
