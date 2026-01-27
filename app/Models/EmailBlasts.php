<?php

namespace App\Models;

use App\Models\Hotels;
use App\Models\HotelPromo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailBlasts extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'hotel_id',
        'promo_id',
        'suggestion',
        'content',
        'benefits',
    ];
    public function hotel(){
        return $this->belongsTo(Hotels::class,'hotel_id');
    }
    public function promo(){
        return $this->hasMany(HotelPromo::class,'promo_id');
    }
}
