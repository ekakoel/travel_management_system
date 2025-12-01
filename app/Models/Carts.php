<?php

namespace App\Models;

use App\Models\User;
use App\Models\Hotels;
use App\Models\HotelRoom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Carts extends Model
{
    use HasFactory;
    protected $fillable = [
        'users_id',
        'hotels_id',
        'hotel_rooms_id',
        'checkin',
        'checkout',
        'guests',
        'quantity',
        'price',
        'total',
    ];

    // Relasi opsional
    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }

    public function hotel() {
        return $this->belongsTo(Hotels::class,'hotel_id');
    }

    public function room() {
        return $this->belongsTo(HotelRoom::class,'room_id');
    }
}
