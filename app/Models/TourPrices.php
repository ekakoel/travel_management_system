<?php

namespace App\Models;

use App\Models\Tours;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourPrices extends Model
{
    use HasFactory;
    protected $fillable=[
        'tour_id',
        'min_qty',
        'max_qty',
        'contract_rate',
        'markup',
        'expired_date',
        'status',
    ];

    public function tours(){
        return $this->belongsTo(Tours::class,'tour_id');
    }
    
    public function calculatePrice($usdrates, $tax)
    {
        $room_c_rate = $this->contract_rate;
        $room_usd = (ceil($room_c_rate / $usdrates->rate)) + $this->markup;
        $room_tax = ceil($room_usd * ($tax->tax / 100));
        return $room_usd + $room_tax;
    }
}
