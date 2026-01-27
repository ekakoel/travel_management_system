<?php

namespace App\Models;
use App\Models\Hotels;
use App\Models\Weddings;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeddingReceptionVenues extends Model
{
    use HasFactory;
    protected $fillable = [
        'hotel_id',
        'name',
        'cover',
        'description',
        'terms_and_conditions',
        'capacity',
        'periode_start',
        'periode_end',
        'markup',
        'price',
        'author_id',
        'status',
    ];
    public function hotel(){
        return $this->belongsTo(Hotels::class,'hotel_id');
    }
    public function weddings(){
        return $this->hasMany(Weddings::class,'reception_venue_id');
    }
    public function author(){
        return $this->belongsTo(User::class,'author_id');
    }
}
