<?php

namespace App\Models;

use App\Models\Spks;
use App\Models\Guide;
use App\Models\Guests;
use App\Models\Orders;
use App\Models\User;
use App\Models\Drivers;
use App\Models\Partners;
use App\Models\InvoiceAdmin;
use App\Models\RestaurantRsv;
use App\Models\AdditionalService;
use App\Models\OptionalRateOrder;
use App\Models\IncludeReservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'rsv_no',
        'service',
        'optional_rate_order_id',
        'inv_id',
        'partner_id',
        'agn_id',
        'adm_id',
        'checkin',
        'checkout',
        'guide_id',
        'guests_id',
        'driver_id',
        'pickup_name',
        'customer_name',
        'phone',
        'pickup_date',
        'dropoff_date',
        'pickup_location',
        'dropoff_location',
        'arrival_flight',
        'arrival_time',
        'departure_flight',
        'departure_time',
        'orders',
        'additional_info',
        'status',
        // Optional legacy compatibility only. Tour Package confirmation
        // delivery is audited through order_logs, not this non-canonical field.
        'send',
    ];
    public function orders(){
        return $this->hasOne(Orders::class,'rsv_id'); //good
    }
    public function additionalservices(){
        return $this->hasMany(AdditionalService::class,'rsv_id'); //good
    }

    public function restaurants(){
        return $this->hasMany(RestaurantRsv::class,'rsv_id'); //good
    }
    public function includes(){
        return $this->hasMany(IncludeReservation::class,'rsv_id'); //good
    }
    public function excludes(){
        return $this->hasMany(ExcludeReservation::class,'rsv_id'); //good
    }
    public function remarks(){
        return $this->hasMany(RemarkReservation::class,'rsv_id'); //good
    }

    public function optional_rate_order(){
        return $this->hasMany(OptionalRateOrder::class,'rsv_id'); // good
    }
    
    public function driver(){
        return $this->belongsTo(Drivers::class,'driver_id'); // good
    }

    public function invoice(){
        return $this->hasOne(InvoiceAdmin::class,'rsv_id'); //good
    }

    public function partner(){
        return $this->belongsTo(Partners::class,'partner_id'); //good
    }
    public function agent(){
        return $this->belongsTo(User::class,'agn_id'); //good
    }
    public function guests(){
        return $this->hasMany(Guests::class,'rsv_id'); //good
    }
    public function guide(){
        return $this->belongsTo(Guide::class,'guide_id'); // good
    }
    public function spks(){
        return $this->hasMany(Spks::class,'reservation_id');
    }
}
