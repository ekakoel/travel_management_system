<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VillaRoomController extends Controller
{
    
    public function index()
    {
        $rooms = VillaRoom::with('villa')->latest()->paginate(10);
        return view('villa-rooms.index', compact('rooms'));
    }

    public function create()
    {
        $villas = Villas::all();
        return view('villa-rooms.create', compact('villas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'villa_id' => 'required|exists:villas,id',
            'room_type' => 'required|string|max:255',
            'bed_type' => 'nullable|string|max:255',
            'max_guest' => 'required|integer|min:1',
            'size' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'amenities' => 'nullable|string',
            'price' => 'nullable|numeric',
        ]);

        VillaRoom::create($request->all());

        return redirect()->route('villa-rooms.index')->with('success', 'Room added successfully!');
    }

    public function edit(VillaRoom $villaRoom)
    {
        $villas = Villas::all();
        return view('villa-rooms.edit', compact('villaRoom', 'villas'));
    }

    public function update(Request $request, VillaRoom $villaRoom)
    {
        $request->validate([
            'room_type' => 'required|string|max:255',
            'bed_type' => 'nullable|string|max:255',
            'max_guest' => 'required|integer|min:1',
            'size' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'amenities' => 'nullable|string',
            'price' => 'nullable|numeric',
        ]);

        $villaRoom->update($request->all());

        return redirect()->route('villa-rooms.index')->with('success', 'Room updated successfully!');
    }

    public function destroy(VillaRoom $villaRoom)
    {
        $villaRoom->delete();
        return redirect()->route('villa-rooms.index')->with('success', 'Room deleted.');
    }
}
