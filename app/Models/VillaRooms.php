<?php

namespace App\Models;

use App\Models\Villas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VillaRooms extends Model
{
    use HasFactory;
     protected $fillable = [
        'villa_id',
        'name',
        'cover',
        'room_type',
        'bed_type',
        'guest_adult',
        'guest_child',
        'size',
        'description',
        'description_traditional',
        'description_simplified',
        'amenities',
        'amenities_traditional',
        'amenities_simplified',
        'view',
        'status',
    ];

    public function villa()
    {
        return $this->belongsTo(Villas::class, 'villa_id');
    }
    public static function totalGuestsByVilla($villaId)
    {
        return self::where('villa_id', $villaId)
            ->selectRaw('SUM(guest_adult) as total_adult, SUM(guest_child) as total_child')
            ->first();
    }
}
