<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DataItineraries;
use App\Http\Controllers\Controller;

class DataItinerariesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }
    
    public function index()
    {
        $itineraries = DataItineraries::latest()->paginate(10);
        return view('admin.itineraries.index', compact('itineraries'));
    }

    public function create()
    {
        return view('admin.itineraries.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_traditional' => 'nullable|string|max:255',
            'title_simplified' => 'nullable|string|max:255',
            'code' => 'required|string|max:100|unique:data_itineraries,code',
            'itinerary' => 'required',
            'itinerary_traditional' => 'nullable',
            'itinerary_simplified' => 'nullable',
        ]);

        DataItineraries::create($validated);

        return redirect()
            ->route('itineraries.index')
            ->with('success', 'Itinerary berhasil ditambahkan');
    }

    public function show(DataItineraries $itinerary)
    {
        return view('admin.itineraries.show', compact('itinerary'));
    }

    public function edit(DataItineraries $itinerary)
    {
        return view('admin.itineraries.edit', compact('itinerary'));
    }

    public function update(Request $request, DataItineraries $itinerary)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_traditional' => 'nullable|string|max:255',
            'title_simplified' => 'nullable|string|max:255',
            'code' => 'required|string|max:100|unique:data_itineraries,code,' . $itinerary->id,
            'itinerary' => 'required',
            'itinerary_traditional' => 'nullable',
            'itinerary_simplified' => 'nullable',
        ]);

        $itinerary->update($validated);

        return redirect()
            ->route('itineraries.index')
            ->with('success', 'Itinerary berhasil diperbarui');
    }

    public function destroy(DataItineraries $itinerary)
    {
        $itinerary->delete();

        return redirect()
            ->route('itineraries.index')
            ->with('success', 'Itinerary berhasil dihapus');
    }
}
