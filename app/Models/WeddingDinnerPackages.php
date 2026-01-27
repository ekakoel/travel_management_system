<?php

namespace App\Models;

use App\Models\Hotels;
use App\Models\WeddingDinnerVenues;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeddingDinnerPackages extends Model
{
    use HasFactory;
    protected $fillable = [
        'dinner_venues_id',
        'hotels_id',
        'period_start',
        'period_end',
        'name',
        'number_of_guests',
        'include',
        'additional_info',
        'public_rate',
        'additional_guest_rate',
        'status',
    ];
    public function reception_venue(){
        return $this->belongsTo(WeddingDinnerVenues::class,'dinner_venues_id');
    }
    public function hotels(){
        return $this->belongsTo(Hotels::class,'hotels_id');
    }
}
