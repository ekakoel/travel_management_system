<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TourLocationReference extends Model
{
    use HasFactory;

    protected $fillable = [
        'destination_name',
        'location_type',
        'google_maps_url',
        'marker_image',
        'latitude',
        'longitude',
        'description',
        'description_traditional',
        'description_simplified',
        'lookup_key',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public static function lookupKey(string $name, string $type, float $latitude, float $longitude): string
    {
        return Str::of($name)->lower()->squish()->toString()
            . '|' . ($type ?: 'Attraction')
            . '|' . round($latitude, 7)
            . '|' . round($longitude, 7);
    }

    public function tourLocations()
    {
        return $this->hasMany(TourPackageLocation::class, 'location_reference_id');
    }
}
