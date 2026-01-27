<?php

namespace App\Models;

use App\Models\Drivers;
use App\Models\SpksDestinations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpksCheckins extends Model
{
    use HasFactory;

    protected $fillable = ['spk_destination_id', 'driver_id', 'latitude', 'longitude', 'checkin_time'];

    public function destination()
    {
        return $this->belongsTo(SpksDestinations::class, 'spk_destination_id');
    }

    public function driver()
    {
        return $this->belongsTo(Drivers::class, 'driver_id');
    }
}
