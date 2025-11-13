<?php

namespace App\Models;

use App\Models\Hotels;
use App\Models\ExtraBed;
use App\Models\HotelRoom;
use App\Models\OrderWedding;
use App\Models\ExtraBedOrder;
use App\Models\WeddingPlanner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeddingAccomodations extends Model
{
    use HasFactory;
    protected $fillable = [
        'wedding_planner_id',
        'order_wedding_package_id',
        'room_for',
        'hotels_id',
        'rooms_id',
        'checkin',
        'checkout',
        'duration',
        'guest_detail',
        'number_of_guests',
        'extra_bed_id',
        'remark',
        'note',
        'status',
        'public_rate',
    ];
    public function wedding_planner(){
        return $this->belongsTo(WeddingPlanner::class,'wedding_planner_id');
    }
    public function order_wedding_package(){
        return $this->belongsTo(OrderWedding::class,'order_wedding_package_id');
    }
    public function room(){
        return $this->belongsTo(HotelRoom::class,'rooms_id');
    }
    public function extra_bed_order(){
        return $this->belongsTo(ExtraBedOrder::class,'extra_bed_id');
    }
    public function extra_bed(){
        return $this->belongsTo(ExtraBed::class,'extra_bed_id');
    }
    public function hotel(){
        return $this->belongsTo(Hotels::class,'hotels_id');
    }
}
