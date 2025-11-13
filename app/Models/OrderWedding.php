<?php

namespace App\Models;

use App\Models\User;
use App\Models\Guests;
use App\Models\Hotels;
use App\Models\Flights;
use App\Models\Weddings;
use App\Models\HotelRoom;
use App\Models\Reservation;
use App\Models\VendorPackage;
use App\Models\WeddingVenues;
use App\Models\WeddingInvitations;
use App\Models\WeddingDinnerVenues;
use App\Models\WeddingAccomodations;
use App\Models\WeddingDinnerPackages;
use App\Models\WeddingReceptionVenues;
use App\Models\WeddingPlannerTransport;
use Illuminate\Database\Eloquent\Model;
use App\Models\WeddingAdditionalServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderWedding extends Model
{
    use HasFactory;
    protected $fillable=[
        'orderno',
        'confirmation_number',
        'rsv_id',
        'service',
        'service_id',
        'type',
        'slot',
        'wedding_planner_id',
        'hotel_id',
        'checkin',
        'checkout',
        'duration',
        'wedding_date',
        'number_of_invitation',
        'brides_id',
        'basic_or_arrangement',
        'ceremony_venue_duration',
        'ceremony_venue_id',
        'ceremony_venue_price',
        'ceremony_venue_decoration_id',
        'ceremony_venue_decoration_price',
        'ceremony_venue_invitations',
        'reception_date_start',
        'reception_venue_id',
        'reception_venue_price',
        'reception_venue_decoration_id',
        'reception_venue_decoration_price',
        'reception_venue_invitations',
        'dinner_venue_id',
        'dinner_venue_date',
        'dinner_venue_price',
        'lunch_venue_id',
        'lunch_venue_date',
        'lunch_venue_price',
        'room_bride_id',
        'room_bride_price',
        'room_invitations_id',
        'accommodation_price',
        'extra_bed_price',
        'transport_id',
        'transport_type',
        'transport_price',
        'transport_invitations_price',
        'makeup_id',
        'makeup_price',
        'documentation_id',
        'documentation_price',
        'entertainment_id',
        'entertainment_price',
        'additional_services',
        'additional_services_price',
        'addser_price',
        'markup',
        'package_price',
        'final_price',
        'price_type',
        'remark',
        'agent_id',
        'verified_by',
        'verified_date',
        'handled_by',
        'handled_date',
        'confirm_with',
        'confirm_by',
        'confirm_address',
        'status',
    ];
    public function wedding(){
        return $this->belongsTo(Weddings::class,'service_id');
    }
    public function agent(){
        return $this->belongsTo(User::class,'agent_id');
    }
    public function hotel(){
        return $this->belongsTo(Hotels::class,'hotel_id');
    }
    public function suite_villa(){
        return $this->belongsTo(HotelRoom::class,'room_bride_id');
    }
    public function ceremony_venue(){
        return $this->belongsTo(WeddingVenues::class,'ceremony_venue_id');
    }
    public function ceremony_venue_decoration(){
        return $this->belongsTo(VendorPackage::class,'ceremony_venue_decoration_id');
    }
    public function reception_venue(){
        return $this->belongsTo(WeddingReceptionVenues::class,'reception_venue_id');
    }
    public function reception_venue_decoration(){
        return $this->belongsTo(VendorPackage::class,'reception_venue_decoration_id');
    }
    public function dinner_venue(){
        return $this->belongsTo(WeddingDinnerVenues::class,'dinner_venue_id');
    }
    public function lunch_venue(){
        return $this->belongsTo(WeddingLunchVenues::class,'lunch_venue_id');
    }

    public function suite_villa_invitations(){
        return $this->hasMany(WeddingAccomodations::class,'order_wedding_package_id');
    }
    public function invitations(){
        return $this->hasMany(WeddingInvitations::class,'order_wedding_id');
    }
    public function additional_services(){
        return $this->hasMany(WeddingAdditionalServices::class,'order_wedding_id');
    }
    public function flights(){
        return $this->hasMany(Flights::class,'order_wedding_id');
    }
    public function guests(){
        return $this->hasMany(Guests::class,'order_wedding_id');
    }
    public function transports(){
        return $this->hasMany(WeddingPlannerTransport::class,'order_wedding_id');
    }

    public function bride(){
        return $this->belongsTo(Brides::class,'brides_id');
    }
    public function reservation(){
        return $this->belongsTo(Reservation::class,'rsv_id');
    }
}
