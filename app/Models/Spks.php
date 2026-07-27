<?php

namespace App\Models;

use App\Models\Guests;
use App\Models\Drivers;
use App\Models\Transports;
use App\Models\Reservation;
use App\Models\AirportShuttle;
use App\Models\SpkDestinations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class Spks extends Model
{
    use HasFactory;

    protected $table = 'spks';

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
        'public_token',
        'spk_date',
        'send_report',
        'status',
    ];

    protected $casts = [
        'spk_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Spks $spk) {
            if (blank($spk->public_token)) {
                $spk->public_token =
                    static::generateUniquePublicToken();
            }
        });
    }

    public static function generateUniquePublicToken(): string
    {
        do {
            $token = strtoupper(Str::random(8));
        } while (
            static::query()
                ->where('public_token', $token)
                ->exists()
        );

        return $token;
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(
            Reservation::class,
            'reservation_id'
        );
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(
            Drivers::class,
            'driver_id'
        );
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(
            Transports::class,
            'transport_id'
        );
    }

    // public function driver()
    // {
    //     return $this->belongsTo(Drivers::class, 'driver_id');
    // }

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
        if (!Schema::hasColumn('guests', 'spk_id')) {
            return $this->hasMany(Guests::class, 'rsv_id', 'reservation_id');
        }

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
        $destinations = $this->destinations()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

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
