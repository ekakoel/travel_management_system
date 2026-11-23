<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\Contract;
use App\Models\ExtraBed;
use App\Models\HotelRoom;
use App\Models\HotelType;
use App\Models\HotelPrice;
use App\Models\HotelPromo;
use App\Models\HotelPackage;
use App\Models\HotelsImages;
use App\Models\OptionalRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hotels extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'region',
        'address',
        'airport_duration',
        'airport_distance',
        'contact_person',
        'phone',
        'description',
        'description_traditional',
        'description_simplified',
        'facility',
        'facility_traditional',
        'facility_simplified',
        'additional_info', 
        'additional_info_traditional', 
        'additional_info_simplified', 
        'entrance_fee', 
        'status',
        'cover',
        'author_id',
        'web',
        'min_stay',
        'max_stay',
        'check_in_time',
        'check_out_time',
        'map',
        'benefits',
        'benefits_traditional',
        'benefits_simplified',
        'optional_rate',
        'cancellation_policy',
        'cancellation_policy_traditional',
        'cancellation_policy_simplified',
        'max_stay',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function images(){
        return $this->hasMany(HotelsImages::class,'hotels_id');
    }

    public function prices(){
        return $this->hasMany(HotelPrice::class,'hotels_id');
    }
    public function rooms(){
        return $this->hasMany(HotelRoom::class,'hotels_id');
    }
    public function extrabeds(){
        return $this->hasMany(ExtraBed::class,'hotels_id');
    }
    public function optionalrates(){
        return $this->hasMany(OptionalRate::class,'hotels_id');
    }

    public function packages(){
        return $this->hasMany(HotelPackage::class,'hotels_id');
    }

    public function promos(){
        return $this->hasMany(HotelPromo::class,'hotels_id');
    }

    public function type(){
        return $this->hasMany(HotelType::class,'hotels_id');
    }
    public function contracts(){
        return $this->hasMany(Contract::class,'hotels_id');
    }
    public function flyers(){
        return $this->hasMany(FlyerGenerator::class,'hotels_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->select(['name as text','id']);
    }

}
