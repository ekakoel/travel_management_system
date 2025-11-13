<?php

namespace App\Models;

use App\Models\Guests;
use App\Models\Drivers;
use App\Models\Transports;
use App\Models\Reservation;
use App\Models\AirportShuttle;
use App\Models\SpkDestinations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Spks extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'type',
        'operator_id',
        'reservation_id',
        'driver_id',
        'transport_id',
        'plate_number',
        'spk_number',
        'number_of_guests',
        'spk_date',
        'send_report',
        'status'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function driver()
    {
        return $this->belongsTo(Drivers::class, 'driver_id');
    }

    public function transport()
    {
        return $this->belongsTo(Transports::class, 'transport_id');
    }

    public function destinations()
    {
        return $this->hasMany(SpkDestinations::class, 'spk_id')->orderBy('date', 'asc');
    }
    public function guests()
    {
        return $this->hasMany(Guests::class, 'spk_id');
    }
    public function airport_shuttles()
    {
        return $this->hasMany(AirportShuttle::class, 'spk_id');
    }
    
        public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
    
    public function getTotalDistanceAttribute()
    {
        $destinations = $this->destinations()->orderBy('date', 'asc')->get();

        $total = 0;

        for ($i = 0; $i < $destinations->count() - 1; $i++) {
            $total += $this->haversine(
                $destinations[$i]->latitude,
                $destinations[$i]->longitude,
                $destinations[$i+1]->latitude,
                $destinations[$i+1]->longitude
            );
        }

        return round($total, 2); // km
    }

    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }
}

