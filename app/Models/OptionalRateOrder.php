<?php

namespace App\Models;

use App\Models\Reservation;
use App\Models\OptionalRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OptionalRateOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'rsv_id',
        'orders_id',
        'service',
        'optional_rate_id',
        'number_of_guest',
        'service_date',
        'price_pax',
        'price_total',
        'mandatory',
    ];
    public function reservation(){
        return $this->belongsTo(Reservation::class,'rsv_id');
    }
    public function order(){
        return $this->belongsTo(Orders::class,'orders_id');
    }
    public function optional_rate(){
        return $this->belongsTo(OptionalRate::class,'optional_rate_id');
    }
}
