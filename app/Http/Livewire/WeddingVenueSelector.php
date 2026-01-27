<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Hotels;
use App\Models\WeddingVenues;



class WeddingVenueSelector extends Component
{
    public $selectedHotel;
    public $weddingVenues;
    public $selectedVenue;
    
    protected $rules = [
        'weddingVenues' => 'required|array',
    ];

    public function render()
    {
        $hotels = Hotels::all();
        return view('livewire.wedding-venue-selector', compact('hotels'));
    }

    public function updatedSelectedHotel($value)
    {
        if ($value) {
            $this->weddingVenues = WeddingVenues::where('hotels_id', $value)->get();
        } else {
            $this->weddingVenues = []; // Reset weddingVenues if no hotel is selected
        }
        
        $this->emit('refreshComponent'); // Emit event to refresh component
    }
}