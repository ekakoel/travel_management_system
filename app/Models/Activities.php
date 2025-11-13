<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\ActivityType;
use App\Models\ActivitiesImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activities extends Model
{
    use HasFactory;
    protected $fillable = [
        'partners_id',
        'partner',
        'name',
        'code',
        'type', 
        'location',
        'map',
        'description',
        'itinerary',
        'duration',
        'include',
        'additional_info', 
        'contract_rate',
        'cancellation_policy',
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
