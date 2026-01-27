<?php

namespace App\Models;

use App\Models\User;
use App\Models\Hotels;
use App\Models\Vendor;
use App\Models\HotelRoom;
use App\Models\Transports;
use App\Models\VendorPackage;
use App\Models\WeddingVenues;
use App\Models\AdditionalService;
use App\Models\WeddingLunchVenues;
use App\Models\WeddingDinnerVenues;
use App\Models\WeddingDinnerPackages;
use App\Models\WeddingReceptionVenues;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Weddings extends Model
{
    use HasFactory;
    protected $fillable = [
        "code",
        "hotel_id",
        "cover",
        "name",
        "duration",
        "capacity",
        "suites_and_villas_id",
        "ceremony_venue_id",
        "ceremony_venue_decoration_id",
        "reception_venue_id",
        "reception_venue_decoration_id",
        "lunch_venue_id",
        "dinner_venue_id",
        "transport_id",
        "additional_service_id",
        "include",
        "cancellation_policy",
        "period_start",
        "period_end",
        "payment_process",
        "week_day_price",
        "holiday_price",
        "terms_and_conditions",
        "slot",
        "status",
        "author_id",
    ];
    public function hotels(){
        return $this->belongsTo(Hotels::class,'hotel_id');
    }
    public function ceremony_venue(){
        return $this->belongsTo(WeddingVenues::class,'ceremony_venue_id');
    }
    public function reception_venue(){
        return $this->belongsTo(WeddingReceptionVenues::class,'reception_venue_id');
    }
    public function vendor_ceremony_venue_decoration(){
        return $this->belongsTo(VendorPackage::class,'ceremony_venue_decoration_id');
    }
    public function vendor_reception_venue_decoration(){
        return $this->belongsTo(VendorPackage::class,'reception_venue_decoration_id');
    }
    public function lunch_venue(){
        return $this->belongsTo(WeddingLunchVenues::class,'lunch_venue_id');
    }
    public function dinner_venue(){
        return $this->belongsTo(WeddingDinnerVenues::class,'dinner_venue_id');
    }
    public function transport(){
        return $this->belongsTo(Transports::class,'transport_id');
    }
    public function suite_and_villa(){
        return $this->belongsTo(HotelRoom::class,'suites_and_villas_id');
    }
    public function author(){
        return $this->belongsTo(User::class,'suites_and_villas_id');
    }

}
