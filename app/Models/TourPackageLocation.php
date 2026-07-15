<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPackageLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'itinerary_id',
        'location_reference_id',
        'day_number',
        'visit_order',
        'visit_time',
        'destination_name',
        'location_type',
        'google_maps_url',
        'marker_image',
        'latitude',
        'longitude',
        'description',
        'is_active',
    ];

    protected $casts = [
        'day_number' => 'integer',
        'visit_order' => 'integer',
        'visit_time' => 'datetime:H:i',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
    ];

    public function tour()
    {
        return $this->belongsTo(Tours::class, 'tour_id');
    }

    public function itinerary()
    {
        return $this->belongsTo(Itineraries::class, 'itinerary_id');
    }

    public function reference()
    {
        return $this->belongsTo(TourLocationReference::class, 'location_reference_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('day_number')->orderBy('visit_order')->orderBy('id');
    }
}
