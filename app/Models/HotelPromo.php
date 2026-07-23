<?php

namespace App\Models;

use App\Models\Hotels;
use App\Models\HotelRoom;
use App\Services\Hotels\HotelPricingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HotelPromo extends Model
{
    use HasFactory;
    protected $fillable = [
        'promotion_type',
        'quotes',
        'hotels_id',
        'rooms_id',
        'name',
        'book_periode_start',
        'book_periode_end',
        'periode_start',
        'periode_end',
        'contract_rate',
        'minimum_stay',
        'markup',
        'booking_code',
        'benefits',
        'benefits_traditional',
        'benefits_simplified',
        'email_status',
        'send_to_specific_email',
        'specific_email',
        'status',
        'include',
        'include_traditional',
        'include_simplified',
        'author',
        'additional_info',
        'additional_info_traditional',
        'additional_info_simplified',
    ];
    
    // protected static function booted()
    // {
    //     static::retrieved(function ($promo) {
    //         if ($promo->book_periode_end < now()->toDateString() && $promo->status !== 'Expired') {
    //             $promo->update(['status' => 'Expired']);
    //         }
    //     });
    // }

    public function hotels(){
        return $this->belongsTo(Hotels::class,'hotels_id');
    }

    public function rooms(){
        return $this->belongsTo(HotelRoom::class,'rooms_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeValidForBooking($query, $now)
    {
        return $query->whereDate('book_periode_start', '<=', $now)
            ->whereDate('book_periode_end', '>=', $now);
    }
    public function scopeValidForStay($query, $checkin)
    {
        return $query->where('periode_start', '<=', $checkin)
                    ->where('periode_end', '>=', $checkin);
    }
    public function calculatePrice($usdrates, $tax)
    {
        return app(HotelPricingService::class)->publishedRate($this->contract_rate, $this->markup, $usdrates, $tax);
    }

    public function calculateTax($usdrates, $tax)
    {
        return app(HotelPricingService::class)->taxAmount($this->contract_rate, $this->markup, $usdrates, $tax);
    }
}
