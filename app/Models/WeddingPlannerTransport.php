<?php

namespace App\Models;
use App\Models\Guide;
use App\Models\Drivers;
use App\Models\Transports;
use App\Models\OrderWedding;
use App\Models\WeddingPlanner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeddingPlannerTransport extends Model
{
    use HasFactory;
    protected $fillable = [
        'wedding_planner_id',
        'order_wedding_id',
        'transport_id',
        'driver_id',
        'guide_id',
        'type',
        'desc_type',
        'date',
        'passenger',
        'number_of_guests',
        'duration',
        'distance',
        'remark',
        'price',
    ];
    public function wedding_planners(){
        return $this->belongsTo(WeddingPlanner::class,'wedding_planner_id');
    }
    public function transport(){
        return $this->belongsTo(Transports::class,'transport_id');
    }
    public function driver(){
        return $this->belongsTo(Drivers::class,'driver_id');
    }
    public function order_wedding(){
        return $this->belongsTo(OrderWedding::class,'driver_id');
    }
    public function guide(){
        return $this->belongsTo(Guide::class,'guide_id');
    }
}
