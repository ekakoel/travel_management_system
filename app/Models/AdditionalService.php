<?php

namespace App\Models;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdditionalService extends Model
{
    use HasFactory;
    protected $fillable = [
        'rsv_id',
        'tgl',
        'service',
        'type',
        'location',
        'qty',
        'price',
        'total_price',
        'loc_name',
        'admin_id',
        'note',
    ];
    public function reservation(){
        return $this->belongsTo(Reservation::class,'rsv_id');
    }
}
