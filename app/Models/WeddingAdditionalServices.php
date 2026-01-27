<?php

namespace App\Models;

use App\Models\Orders;
use App\Models\OrderWedding;
use App\Models\VendorPackage;
use App\Models\WeddingPlanner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeddingAdditionalServices extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'duration',
        'order_wedding_id',
        'order_id',
        'wedding_planner_id',
        'type',
        'service',
        'quantity',
        'note',
        'remark',
        'price',
        'status',
    ];
    public function order_wedding(){
        return $this->belongsTo(OrderWedding::class,'order_wedding_id');
    }
    public function order(){
        return $this->belongsTo(Orders::class,'order_id');
    }
    public function wedding_planner(){
        return $this->belongsTo(WeddingPlanner::class,'wedding_planner_id');
    }
}
