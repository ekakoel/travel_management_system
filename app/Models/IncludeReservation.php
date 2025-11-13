<?php

namespace App\Models;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IncludeReservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'rsv_id',
        'include',
    ];
    public function reservation(){
        return $this->belongsTo(Reservation::class,'rsv_id');
    }
}
