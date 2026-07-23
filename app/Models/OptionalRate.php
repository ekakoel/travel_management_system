<?php

namespace App\Models;

use App\Models\Hotels;
use App\Models\Villas;
use App\Models\OptionalRateOrder;
use App\Services\Hotels\HotelPricingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OptionalRate extends Model
{
    use HasFactory;
    protected $fillable = [
        'hotels_id',
        'villas_id',
        'name',
        'service',
        'service_id',
        'type',
        'mandatory',
        'active_date',
        'must_buy_start',
        'must_buy_end',
        'mandatory_start',
        'mandatory_end',
        'contract_rate',
        'markup',
        'description',
        'description_traditional',
        'description_simplified',
    ];
    public function hotels(){
        return $this->belongsTo(Hotels::class,'hotels_id');
    }
    public function villas(){
        return $this->belongsTo(Villas::class,'villas_id');
    }
    public function optional_rate_orders(){
        return $this->hasMany(OptionalRateOrder::class);
    }
    public function calculatePrice($usdrates, $tax)
    {
        return app(HotelPricingService::class)->publishedRate($this->contract_rate, $this->markup, $usdrates, $tax);
    }

    public function getMandatoryStartAttribute()
    {
        return $this->must_buy_start;
    }

    public function getMandatoryEndAttribute()
    {
        return $this->must_buy_end;
    }

    public function scopeMustBuy($query, $checkin, $checkout)
    {
        return $query->whereBetween('active_date', [$checkin, $checkout]);
    }
}
