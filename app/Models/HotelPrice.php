<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Hotels;
use App\Models\HotelRoom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HotelPrice extends Model
{
    use HasFactory;
    protected $fillable = [
        'hotels_id',
        'rooms_id',
        'start_date',
        'end_date',
        'markup',
        'kick_back',
        'contract_rate',
        'author',
    ];

    public function hotels(){
        return $this->belongsTo(Hotels::class,'hotels_id');
    }

    public function rooms(){
        return $this->belongsTo(HotelRoom::class,'rooms_id');
    }

    public function scopeActive($query,$now)
    {
        return $query->whereNotNull('end_date')
                 ->where('end_date', '!=', '')
                 ->where('end_date','>',$now);
    }

    public function taxRate($usdrates, $tax)
    {
        $r_c_rate = $this->contract_rate;
        $r_usd = (ceil($r_c_rate / $usdrates->rate)) + $this->markup;
        $r_tax = ceil($r_usd * ($tax->tax / 100));
        return $r_tax;
    }

    public function contractRate($usdrates)
    {
        $crate = $this->contract_rate;
        $contract_rate = (ceil($crate / $usdrates->rate));
        return $contract_rate;
    }
    public function calculatePrice($usdrates, $tax)
    {
        $room_c_rate = $this->contract_rate;
        $room_usd = (ceil($room_c_rate / $usdrates->rate)) + $this->markup;
        $room_tax = ceil($room_usd * ($tax->tax / 100));
        return $room_usd + $room_tax;
    }
}
