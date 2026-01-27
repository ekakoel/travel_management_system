<?php

namespace App\Models;

use App\Models\Reservation;
use App\Models\OrderWedding;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdditionalService extends Model
{
    use HasFactory;
    protected $fillable = [
        'rsv_id',
        'order_wedding_id',
        'tgl',
        'service',
        'type',
        'location',
        'qty',
        'price',
        'total_price',
        'loc_name',
        'admin_id',
        'note',
    ];
    public function reservation(){
        return $this->belongsTo(Reservation::class,'rsv_id');
    }
    public function order_wedding(){
        return $this->belongsTo(OrderWedding::class,'order_wedding_id');
    }
}
