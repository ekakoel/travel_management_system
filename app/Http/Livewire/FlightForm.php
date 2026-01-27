<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\WeddingPlanner;

class FlightForm extends Component
{
    public $weddingPlannerId;
    public $arrivalFlight;
    public $arrivalTime;
    public $departureFlight;
    public $departureTime;
    
    public function mount($weddingPlannerId, $arrivalFlight, $arrivalTime, $departureFlight, $departureTime)
    {
        $this->weddingPlannerId = $weddingPlannerId;
        $this->arrivalFlight = $arrivalFlight;
        $this->arrivalTime = $arrivalTime;
        $this->departureFlight = $departureFlight;
        $this->departureTime = $departureTime;
    }

    public function render()
    {
        $wedding_planner_flight = WeddingPlanner::findOrFail($this->weddingPlannerId);
        return view('livewire.flight-form',[
            'wedding_planner_flight' => $wedding_planner_flight,
            'weddingPlannerId' => $this->weddingPlannerId,
            'arrivalFlight' => $this->arrivalFlight,
            'arrivalTime' => $this->arrivalTime,
            'departureFlight' => $this->departureFlight,
            'departureTime' => $this->departureTime,
        ]);
    }

    public function submitForm()
    {
        $wedding_planner = WeddingPlanner::findOrFail($this->weddingPlannerId);
        $wedding_planner->update([
            'arrival_flight' => $this->arrivalFlight,
            'arrival_time' => $this->arrivalTime,
            'departure_flight' => $this->departureFlight,
            'departure_time' => $this->departureTime,
        ]);
        $this->reset();
    }
}
