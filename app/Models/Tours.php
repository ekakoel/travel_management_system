<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\Category;
use App\Models\Partners;
use App\Models\TourType;
use App\Models\Itinerary;
use App\Models\TourPrices;
use App\Models\ToursImages;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tours extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'name_traditional',
        'name_simplified',
        'slug',
        'cover',
        'area',
        'area_traditional',
        'area_simplified',
        'type_id',
        'short_description',
        'short_description_traditional',
        'short_description_simplified',
        'description',
        'description_traditional',
        'description_simplified',
        'duration_days',
        'duration_nights',
        'itinerary',
        'itinerary_traditional',
        'itinerary_simplified',
        'include',
        'include_traditional',
        'include_simplified',
        'exclude',
        'exclude_traditional',
        'exclude_simplified',
        'additional_info', 
        'additional_info_traditional', 
        'additional_info_simplified', 
        'cancellation_policy',
        'cancellation_policy_traditional',
        'cancellation_policy_simplified',
        'status',
    ];

    public function images(){
        return $this->hasMany(ToursImages::class,'tour_id');
    }
    public function prices(){
        return $this->hasMany(TourPrices::class,'tour_id');
    }
    public function itineraries(){
        return $this->hasMany(Itinerary::class,'tour_id');
    }
    public function type(){
        return $this->belongsTo(TourType::class,'type_id');
    }

    // Generate slug otomatis
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name);
            }
        });
    }
}