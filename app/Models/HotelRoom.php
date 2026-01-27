<?php

namespace App\Models;

use App\Models\Hotels;
use App\Models\ExtraBed;
use App\Models\Weddings;
use App\Models\HotelPackage;
use App\Models\OrderWedding;
use App\Models\RoomFacilities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HotelRoom extends Model
{
    use HasFactory;
    protected $fillable=[
        'cover',
        'rooms',
        'hotels_id',
        'capacity_adult',
        'capacity_child',
        'view',
        'beds',
        'size',
        'amenities',
        'amenities_traditional',
        'amenities_simplified',
        'additional_info',
        'additional_info_traditional',
        'additional_info_simplified',
        'status',
        'include',
    ];

    public function hotels(){
        return $this->belongsTo(Hotels::class,'hotels_id');
    }
    public function prices(){
        return $this->hasMany(HotelPrice::class,'rooms_id');
    }
    public function packages(){
        return $this->hasMany(HotelPackage::class,'rooms_id');
    }
    public function promos(){
        return $this->hasMany(HotelPromo::class,'rooms_id');
    }
    public function facilities(){
        return $this->hasMany(RoomFacilities::class,'rooms_id');
    }
    public function wedding_order(){
        return $this->hasMany(OrderWedding::class,'room_bride_id');
    }
    public function extrabeds(){
        return $this->hasMany(ExtraBed::class,'rooms_id');
    }
    public function wedding_packages(){
        return $this->hasMany(Weddings::class,'suite_and_villas_id');
    }
}
