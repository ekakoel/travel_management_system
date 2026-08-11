<?php

namespace App\Models;

use App\Models\User;
use App\Models\Guide;
use App\Models\Guests;
use App\Models\Drivers;
use App\Models\OrderNote;
use App\Models\Reservation;
use App\Models\FlyerGenerator;
use App\Models\OrderHotelPromo;
use App\Models\OptionalRateOrder;
use App\Models\OrderHotelPromoDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Orders extends Model
{
    use HasFactory;

    public const ACCOMMODATION_SERVICES = [
        'Hotel',
        'Hotel Promo',
        'Hotel Package',
    ];

    public const PUBLIC_TRANSPORT_SERVICE = 'Transport';
    public const PUBLIC_TOUR_SERVICE = 'Tour Package';
    public const PUBLIC_ACTIVITY_SERVICE = 'Activity';

    protected static function booted()
    {
        static::creating(function ($order) {
            if ($order->confirmation_order === null) {
                $order->confirmation_order = '';
            }
        });
    }

    protected $fillable = [
        'orderno',
        'confirmation_order',
        'user_id',
        'name',
        'servicename',
        'email',
        'service',
        'service_type',
        'service_id',
        'subservice',
        'subservice_id',
        'extra_time',
        'price_id',
        'src',
        'dst',
        'checkin',
        'checkout',
        'duration',
        'capacity',
        'benefits',
        'booking_code',
        'include',
        'include_traditional',
        'include_simplified',
        'exclude',
        'exclude_traditional',
        'exclude_simplified',
        'destinations',
        'additional_info',
        'number_of_guests',
        'number_of_guests_room',
        'request_quotation',
        'number_of_room',
        'guest_detail',
        'extra_bed',
        'extra_bed_id',
        'extra_bed_price',
        'extra_bed_total_price',
        'wedding_order_id',
        'wedding_date',
        'bride_name',
        'groom_name',
        'special_day',
        'special_date',
        'price_pax',
        'normal_price',
        'optional_price',
        'kick_back',
        'kick_back_per_pax',
        'price_total',
        'alasan_discounts',
        'discounts',
        'bookingcode',
        'bookingcode_disc',
        'promotion',
        'promotion_disc',
        'additional_service_date',
        'additional_service',
        'additional_service_qty',
        'additional_service_price',
        'additional_service_total_price',
        'airport_shuttle_price',
        'order_tax',
        'final_price',
        'pricing_version',
        'pricing_snapshot_id',
        'base_currency',
        'display_currency',
        'final_total_idr',
        'final_total_usd_minor',
        'pricing_calculated_at',
        'submission_token_hash',
        'usd_rate',
        'cny_rate',
        'twd_rate',
        'package_name',
        'promo_id',
        'promo_name',
        'book_period_start',
        'book_period_end',
        'period_start',
        'period_end',
        'status',
        'note',
        'arrival_flight',
        'arrival_time',
        'airport_shuttle_in',
        'departure_flight',
        'departure_time',
        'airport_shuttle_out',
        'travel_date',
        'location',
        'tour_type',
        'itinerary',
        'msg',
        'sales_agent',
        'notification',
        'cancellation_policy',
        'cancellation_policy_traditional',
        'cancellation_policy_simplified',
        'verified_by',
        'handled_by',
        'handled_date',
        'completed_at',
        'completed_by',
        'driver_id',
        'guide_id',
        'pickup_name',
        'pickup_phone',
        'pickup_location',
        'pickup_date',
        'dropoff_date',
        'dropoff_location',
        'rsv_id',
    ];

    protected $casts = [
        'final_total_idr' => 'integer',
        'final_total_usd_minor' => 'integer',
        'pricing_calculated_at' => 'immutable_datetime',
    ];

    public function pricingSnapshots()
    {
        return $this->hasMany(OrderPricingSnapshot::class, 'order_id');
    }

    public function activePricingSnapshot()
    {
        return $this->belongsTo(OrderPricingSnapshot::class, 'pricing_snapshot_id');
    }
    public function reservations(){
        return $this->belongsTo(Reservation::class,'rsv_id');
    }
    public function order_notes(){
        return $this->hasMany(OrderNote::class, 'order_id');
    }
    

    public function airport_shuttles()
    {
        return $this->hasMany(AirportShuttle::class,'order_id');
    }
    public function optional_rate_orders()
    {
        return $this->hasMany(OptionalRateOrder::class,'order_id');
    }
    public function guests()
    {
        return $this->hasMany(Guests::class, 'order_id');
    }
    public function reservation_guests()
    {
        return $this->hasMany(Guests::class, 'rsv_id', 'rsv_id');
    }
    public function guide()
    {
        return $this->belongsTo(Guide::class, 'guide_id');
    }
    public function driver()
    {
        return $this->belongsTo(Drivers::class, 'driver_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeAccommodationService(Builder $query): Builder
    {
        return $query->whereIn('service', self::ACCOMMODATION_SERVICES);
    }

    public function scopeOwnedBy(Builder $query, int $userId): Builder
    {
        return $query->where('sales_agent', $userId);
    }
}
