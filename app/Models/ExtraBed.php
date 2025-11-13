<?php

namespace App\Models;

use App\Models\Hotels;
use App\Models\HotelRoom;
use App\Models\WeddingAccomodations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExtraBed extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'hotels_id',
        'type',
        'max_age',
        'min_age',
        'description',
        'contract_rate',
        'markup',
    ];
    public function hotels(){
        return $this->belongsTo(Hotels::class,'hotels_id');
    }
    public function rooms(){
        return $this->belongsTo(HotelRoom::class,'rooms_id');
    }
    public function wedding_accommodations(){
        return $this->hasMany(WeddingAccomodations::class,'extra_bed_id');
    }
    public function calculatePrice($usdrates, $tax)
    {
        $crate = $this->contract_rate;
        $room_usd = (ceil($crate / $usdrates->rate)) + $this->markup;
        $room_tax = ceil($room_usd * ($tax->tax / 100));
        return $room_usd + $room_tax;
    }
}
