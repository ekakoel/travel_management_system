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

class Orders extends Model
{
    use HasFactory;
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
        'exclude',
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
        'verified_by',
        'handled_by',
        'handled_date',
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
    public function reservations(){
        return $this->belongsTo(Reservation::class,'rsv_id');
    }
    public function order_notes(){
        return $this->hasMany(OrderNote::class);
    }
    

    public function airport_shuttles()
    {
        return $this->hasMany(AirportShuttle::class,'order_id');
    }
    public function optional_rate_orders()
    {
        return $this->hasMany(OptionalRateOrder::class);
    }
    public function guests()
    {
        return $this->hasMany(Guests::class);
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
}
