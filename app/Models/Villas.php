<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Contract;
use App\Models\VillaRooms;
use App\Models\VillaImages;
use App\Models\OptionalRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Villas extends Model
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
        'cover',
        'web',
        'min_stay',
        'check_in_time',
        'check_out_time',
        'map',
        'description',
        'description_traditional',
        'description_simplified',
        'facility',
        'facility_traditional',
        'facility_simplified',
        'additional_info',
        'additional_info_traditional',
        'additional_info_simplified',
        'cancellation_policy',
        'cancellation_policy_traditional',
        'cancellation_policy_simplified',
        'author_id',
        'status',
    ];
    public function prices(){
        return $this->hasMany(VillaPrice::class,'villa_id');
    }
    public function rooms()
    {
        return $this->hasMany(VillaRooms::class, 'villa_id');
    }
    public function galleries(){
        return $this->hasMany(VillaImages::class,'villa_id');
    }
    public function optionalrates(){
        return $this->hasMany(OptionalRate::class,'villas_id');
    }
    public function contracts(){
        return $this->hasMany(Contract::class,'villa_id');
    }
    public function stay_period()
    {
        return $this->hasOne(VillaPrice::class, 'villa_id')
            ->where('end_date', '>', Carbon::today())
            ->selectRaw('villa_id, MIN(start_date) as min_start_date, MAX(end_date) as max_end_date')
            ->groupBy('villa_id');
    }
}
