<?php

namespace App\Http\Livewire;

use App\Models\Hotels;
use Livewire\Component;

class WeddingPlanner extends Component
{
    public $brideName;
    public $groomName;
    public $weddingDate;
    public $weddingLocation;
    public $hotels;

    public function mount()
    {
        $this->hotels = Hotels::all();
    }

    public function submitForm()
    {
        $validatedData = $this->validate([
            'brideName' => 'required|string|max:255',
            'groomName' => 'required|string|max:255',
            'weddingDate' => 'required|date',
            'weddingLocation' => 'required',
        ]);
        WeddingPlanner::create($validatedData);
        $this->reset(['brideName', 'groomName', 'weddingDate','weddingLocation']);
        return redirect()->to('/wedding-planner')->with('success', 'Form submitted successfully!');
    }

    public function render()
    {
        return view('livewire.wedding-planner');
    }
}
