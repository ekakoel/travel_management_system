<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'travel_agent',
        'arrival_date',
        'departure_date',
        'accommodation',
        'meals',
        'tour_sites',
        'transportation_cleanliness',
        'transportation_air_condition',
        'driver_id',
        'driver_name',
        'driver_punctuality',
        'driver_driving_skills',
        'driver_neatness',
        'guide_id',
        'guide_name',
        'attitude',
        'explanation',
        'knowledge',
        'time_control',
        'guide_neatness',
        'travel_mood',
        'customer_name',
        'customer_review',
        'status',
    ];


    public static function serviceStats()
    {
        return self::where('status', 'accepted')
        ->selectRaw('
            AVG(accommodation) as accommodation,
            AVG(meals) as meals,
            AVG(tour_sites) as tour_sites
        ')->first();
    }
    public static function transportStats()
    {
        return self::where('status', 'accepted')
        ->selectRaw('
            AVG(transportation_cleanliness) as transportation_cleanliness,
            AVG(transportation_air_condition) as transportation_air_condition
        ')->first();
    }
    public static function guideStats()
    {
        return self::where('status', 'accepted')
        ->selectRaw('
            AVG(attitude) as attitude,
            AVG(explanation) as explanation,
            AVG(knowledge) as knowledge,
            AVG(time_control) as time_control,
            AVG(guide_neatness) as guide_neatness
        ')->first();
    }
    public static function driverStats()
    {
        return self::where('status', 'accepted')
        ->selectRaw('
            AVG(driver_punctuality) as driver_punctuality,
            AVG(driver_driving_skills) as driver_driving_skills,
            AVG(driver_neatness) as driver_neatness
        ')->first();
    }
}
