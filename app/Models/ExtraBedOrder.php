<?php

namespace App\Models;

use App\Models\ExtraBed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExtraBedOrder extends Model
{
    use HasFactory;
    protected $fillable=[
        'order_id',
        'order_wedding_id',
        'rooms_id',
        'extra_bed_id',
        'duration',
        'price_pax',
        'total_price',
    ];
    public function extra_bed(){
        return $this->belongsTo(ExtraBed::class,'extra_bed_id');
    }
}
