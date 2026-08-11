<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\ActivitiesImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activities extends Model
{
    use HasFactory;
    protected $fillable = [
        'partners_id',
        'name',
        'code',
        'type', 
        'location',
        'map',
        'description',
        'description_traditional',
        'description_simplified',
        'itinerary',
        'itinerary_traditional',
        'itinerary_simplified',
        'duration',
        'include',
        'include_traditional',
        'include_simplified',
        'additional_info', 
        'additional_info_traditional', 
        'additional_info_simplified', 
        'contract_rate',
        'cancellation_policy',
        'cancellation_policy_traditional',
        'cancellation_policy_simplified',
        'markup',
        'qty',
        'min_pax',
        'status',
        'validity',
        'author_id',
        'cover',
    ];

    public function images(){
        return $this->hasMany(ActivitiesImages::class,'activities_id');
    }

    public function scopePublished(Builder $query, $asOf = null): Builder
    {
        $asOf ??= now();

        return $query
            ->where('status', 'Active')
            ->whereNotNull('validity')
            ->whereDate('validity', '>=', $asOf);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function partners()
    {
        return $this->belongsTo(Partners::class,'partners_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->select(['name as text','id']);
    }
}
