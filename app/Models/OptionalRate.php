<?php

namespace App\Models;

use App\Models\Hotels;
use App\Models\Villas;
use App\Models\OptionalRateOrder;
use App\Services\Hotels\HotelPricingService;
use Carbon\CarbonInterface;
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

    public function scopeNotExpired($query, CarbonInterface|string $date)
    {
        $today = $date instanceof CarbonInterface ? $date->toDateString() : $date;

        return $query->where(function ($query) use ($today) {
            $query->whereDate('must_buy_end', '>=', $today)
                ->orWhere(function ($query) use ($today) {
                    $query->where(function ($query) {
                        $query->whereNull('must_buy_end')->orWhere('must_buy_end', '');
                    })->where(function ($query) use ($today) {
                        $query->whereNull('active_date')
                            ->orWhere('active_date', '')
                            ->orWhereDate('active_date', '>=', $today);
                    });
                });
        });
    }
}
