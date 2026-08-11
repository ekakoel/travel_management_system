<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use App\Models\Hotels;
use App\Models\HotelRoom;
use App\Services\Hotels\HotelPricingService;
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

    public function scopeNotExpired($query, CarbonInterface|string $date)
    {
        $today = $date instanceof CarbonInterface ? $date->toDateString() : $date;

        return $query->whereDate('end_date', '>=', $today);
    }

    public function taxRate($usdrates, $tax)
    {
        return app(HotelPricingService::class)->taxAmount($this->contract_rate, $this->markup, $usdrates, $tax);
    }

    public function contractRate($usdrates)
    {
        return app(HotelPricingService::class)->contractRateUsd($this->contract_rate, $usdrates);
    }

    public function calculatePrice($usdrates, $tax)
    {
        return app(HotelPricingService::class)->publishedRate($this->contract_rate, $this->markup, $usdrates, $tax);
    }
}
