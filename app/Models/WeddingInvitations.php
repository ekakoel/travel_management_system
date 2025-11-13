<?php

namespace App\Models;

use App\Models\Countries;
use App\Models\OrderWedding;
use App\Models\WeddingPlanner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeddingInvitations extends Model
{
    use HasFactory;
    protected $fillable = [
        'wedding_planner_id',
        'order_wedding_id',
        'sex',
        'name',
        'chinese_name',
        'country',
        'passport_no',
        'passport_img',
        'phone',
        'date_of_birth',
    ];
    public function wedding_planner(){
        return $this->belongsTo(WeddingPlanner::class,'wedding_planner_id');
    }
    public function order_wedding(){
        return $this->belongsTo(OrderWedding::class,'order_wedding_id');
    }
    public function countries(){
        return $this->belongsTo(Countries::class,'country');
    }
}
