<?php

namespace App\Models;

use App\Models\Spks;
use App\Models\Orders;
use App\Models\Transports;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AirportShuttle extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'flight_number',
        'number_of_guests',
        'spk_id',
        'order_id',
        'transport_id',
        'price_id',
        'src',
        'dst',
        'duration',
        'distance',
        'price',
        'order_wedding_id',
        'nav',
    ];
    public function transport()
    {
        return $this->belongsTo(Transports::class,'transport_id');
    }
    public function order()
    {
        return $this->belongsTo(Orders::class,'order_id');
    }
    public function spk()
    {
        return $this->belongsTo(Spks::class,'spk_id');
    }

}
