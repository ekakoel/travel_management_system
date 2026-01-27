<?php

namespace App\Models;

use App\Models\Hotels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FlyerGenerator extends Model
{
    use HasFactory;
    use HasFactory;
    protected $fillable = [
        'hotel_id',
        'expired_date',
        'status',
    ];
    public function contracts(){
        return $this->belongsTo(Hotels::class,'order_id');
    }
}
