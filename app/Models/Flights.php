<?php

namespace App\Models;

use App\Models\Guests;
use App\Models\OrderWedding;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Flights extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'group',
        'flight',
        'time',
        'guests',
        'guests_contact',
        'number_of_guests',
        'order_id',
        'order_wedding_id',
        'wedding_planner_id',
        'status',
    ];
    public function order_wedding(){
        return $this->belongsTo(OrderWedding::class,'order_wedding_id');
    }
}
