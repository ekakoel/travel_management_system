<?php

namespace App\Models;

use App\Models\Hotels;
use App\Models\Weddings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeddingVenues extends Model
{
    use HasFactory;
    protected $fillable = [
        'hotels_id',
        'cover',
        'name',
        'slot',
        'periode_start',
        'periode_end',
        'basic_price',
        'arrangement_price',
        'capacity',
        'description',
        'term_and_condition',
        'author',
        'status',
    ];
    public function hotels(){
        return $this->belongsTo(Hotels::class,'hotels_id');
    }
    public function weddings(){
        return $this->hasMany(Weddings::class,'wedding_venue_id');
    }
    public function orderWeddings(){
        return $this->hasMany(Weddings::class,'ceremony_venue_id');
    }
}
