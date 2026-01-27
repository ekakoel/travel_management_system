<?php

namespace App\Models;

use App\Models\Brides;
use App\Models\Hotels;
use App\Models\WeddingInvitations;
use App\Models\WeddingAccomodations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeddingPlanner extends Model
{
    use HasFactory;
    protected $fillable = [
        'wedding_planner_no',
        'orderno',
        'type',
        'bride_id',
        'wedding_date',
        'number_of_invitations',
        'wedding_venue_id',
        'ceremonial_venue_id',
        'slot',
        'dinner_venue_id',
        'dinner_venue_time_start',
        'dinner_venue_time_end',
        'wedding_package_id',
        'dinner_package_id',
        'optional_service',
        'checkin',
        'checkout',
        'duration',
        'arrival_flight',
        'arrival_time',
        'airport_shuttle_in_id',
        'departure_flight',
        'departure_time',
        'airport_shuttle_out_id',
        'driver_id',
        'guide_id',
        'agent_id',
        'handled_by',
        'person_in_charge_id',
        'bank_account_id',
        'remark',
        'status',
    ];

    public function wedding_accomodations(){
        return $this->hasMany(WeddingAccomodations::class,'wedding_planner_id');
    }
    public function invitations(){
        return $this->hasMany(WeddingInvitations::class,'wedding_planner_id');
    }
    public function hotel(){
        return $this->belongsTo(Hotels::class,'wedding_venue_id');
    }
    public function bride(){
        return $this->belongsTo(Brides::class,'bride_id');
    }
    public function transports(){
        return $this->hasMany(WeddingPlannerTransport::class,'wedding_planner_id');
    }

}
