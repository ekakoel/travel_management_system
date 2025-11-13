<?php

namespace App\Models;
use App\Models\Hotels;
use App\Models\Weddings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeddingLunchVenues extends Model
{
    use HasFactory;
    protected $fillable = [
        'hotel_id',
        'name',
        'cover',
        'description',
        'terms_and_conditions',
        'min_capacity',
        'max_capacity',
        'markup',
        'publish_rate',
        'periode_start',
        'periode_end',
        'status',
    ];
    public function hotel(){
        return $this->belongsTo(Hotels::class,'hotel_id');
    }
    public function weddings(){
        return $this->hasMany(Weddings::class,'luch_venue_id');
    }
}
