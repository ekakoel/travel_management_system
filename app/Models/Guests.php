<?php

namespace App\Models;

use App\Models\Spks;
use App\Models\Orders;
use App\Models\Flights;
use App\Models\Reservation;
use App\Models\OrderWedding;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guests extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_passport_img',
        'name',
        'spk_id',
        'identification_type',
        'identification_no',
        'rsv_id',
        'wedding_planner_id',
        'order_id',
        'order_wedding_id',
        'flight_id',
        'name_mandarin',
        'phone',
        'date_of_birth',
        'sex',
        'age',
    ];
    public function reservation(){
        return $this->belongsToMany(Reservation::class,'rsv_id');
    }
    public function order(){
        return $this->belongsTo(Orders::class,'order_id');
    }
    public function order_wedding(){
        return $this->belongsTo(OrderWedding::class,'order_wedding_id');
    }
    public function flights(){
        return $this->hasMany(Flights::class,'guests_id');
    }
    public function spk(){
        return $this->belongsTo(Spks::class,'spk_id');
    }
}
