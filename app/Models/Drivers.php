<?php

namespace App\Models;

use App\Models\Review;
use App\Models\WeddingPlannerTransport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Drivers extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        'email',
        'license',
        'address',
        'country',
        'status',
    ];
    public function reservation(){
        return $this->belongsToMany(Reservation::class,'rsv_id');
    }
    public function wp_transports(){
        return $this->hasMany(WeddingPlannerTransport::class,'driver_id');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class,'driver_id');
    }
    public function averageRating()
    {
        return $this->reviews()
            ->selectRaw('
                AVG(driver_punctuality) as driver_punctuality,
                AVG(driver_driving_skills) as driver_driving_skills,
                AVG(driver_neatness) as driver_neatness
            ')
            ->first();
    }
    public static function topDrivers($limit = 5)
    {
        return self::select('drivers.*')
            ->addSelect([
                'avg_rating' => Review::selectRaw('AVG((driver_punctuality + driver_driving_skills + driver_neatness)/3)')
                    ->whereColumn('driver_id', 'drivers.id')
            ])
            ->orderByDesc('avg_rating')
            ->take($limit)
            ->get();
    }
    public function reviewSummary()
    {
        $count = $this->reviews()->count();
        $average = $this->reviews()
            ->selectRaw('AVG((driver_punctuality + driver_driving_skills + driver_neatness)/3) as global_rating')
            ->first();
        return [
            'count' => $count,
            'global_rating' => $average->global_rating ?? 0,
        ];
    }
}
