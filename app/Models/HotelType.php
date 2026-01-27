<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Hotels;

class HotelType extends Model
{
    use HasFactory;
    protected $fillable = [
        'hotels_id',
        'type',
    ];

    public function hotels(){
        return $this->belongsTo(Hotels::class,'hotels_id');
    }
}
