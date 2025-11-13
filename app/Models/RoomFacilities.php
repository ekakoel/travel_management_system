<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\HotelRoom;

class RoomFacilities extends Model
{
    use HasFactory;
    protected $fillable=[
        'rooms_id',
        'wifi',
        'single_bed',
        'double_bed',
        'extra_bed',
        'air_conditioning',
        'pool',
        'tv_channel',
        'water_heater',
        'bathtub',
    ];

    public function rooms(){
        return $this->belongsTo(HotelRoom::class,'rooms_id');
    }
}
