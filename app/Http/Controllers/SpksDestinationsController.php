<?php

namespace App\Http\Controllers;

use App\Models\Spks;
use Illuminate\Http\Request;
use App\Models\SpkDestinations;
use App\Http\Requests\StoreSpksDestinationsRequest;
use App\Http\Requests\UpdateSpksDestinationsRequest;

class SpksDestinationsController extends Controller
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
    public function store(Request $request, $spkId)
    {
        $request->validate([
            'destination_name' => 'required|string|max:255',
            'order' => 'required|integer',
        ]);

        $spk = Spks::findOrFail($spkId);

        $destination = new SpksDestinations([
            'spk_id' => $spk->id,
            'destination_name' => $request->destination_name,
            'destination_address' => $request->destination_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => 'pending',
        ]);

        $destination->save();

        return response()->json([
            'success' => true,
            'message' => 'Destination added successfully!',
            'destination' => $destination,
        ]);
    }

    public function driver_create_destination(Request $request, $id)
    {
        $spk = Spks::findOrFail($id);
        $validated = $request->validate([
            'destination_name' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:500',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        // dd($validated,$spk);
        SpkDestinations::create([
            'date' => now(),
            'spk_id' => $id,
            'destination_name' => $validated['destination_name'],
            'driver_id' => $spk->driver_id,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'checkin_latitude' => $validated['latitude'],
            'checkin_longitude' => $validated['longitude'],
            'description' => $validated['description'],
            'visited_at' => now(),
            'status' => 'Visited',
            'checkin_map_link' => "https://maps.google.com/?q={$validated['latitude']},{$validated['longitude']}"
            
        ]);

        return back()->with('success', 'Check-in berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(SpksDestinations $sPKDestinations)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SpksDestinations $sPKDestinations)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpksDestinationsRequest $request, SpksDestinations $sPKDestinations)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SpksDestinations $sPKDestinations)
    {
        //
    }
}
