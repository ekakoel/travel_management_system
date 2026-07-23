<?php

namespace App\Models;

use App\Models\Hotels;
use App\Models\HotelRoom;
use App\Services\Hotels\HotelPricingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HotelPackage extends Model
{
    use HasFactory;
    protected $fillable = [
        'hotels_id',
        'rooms_id',
        'name',
        'duration',
        'stay_period_start',
        'stay_period_end',
        'contract_rate',
        'markup',
        'booking_code',
        'benefits',
        'benefits_traditional',
        'benefits_simplified',
        'include',
        'include_traditional',
        'include_simplified',
        'additional_info',
        'additional_info_traditional',
        'additional_info_simplified',
        'author',
        'status',
    ];

    public function hotels(){
        return $this->belongsTo(Hotels::class,'hotels_id');
    }

    public function room(){
        return $this->belongsTo(HotelRoom::class,'rooms_id');
    }
    public function scopeActive($query, $now)
    {
        return $query->where('status', 'Active')
            ->where('stay_period_end', '>=', $now);
    }
    public function scopeValidForStay($query, $checkin)
    {
        return $query->where('stay_period_start', '<=', $checkin)
                    ->where('stay_period_end', '>=', $checkin);
    }
    public function scopeForDuration($query, $duration)
    {
        return $query->where('duration', $duration);
    }
    public function calculatePrice($usdrates, $tax)
    {
        return app(HotelPricingService::class)->packagePublishedRate($this, $usdrates, $tax);
    }

    public function calculateTax($usdrates, $tax)
    {
        return app(HotelPricingService::class)->taxAmount(
            $this->contract_rate,
            $this->markup,
            $usdrates,
            $tax,
            max((int) $this->duration, 1)
        );
    }

}
