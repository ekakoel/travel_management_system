<?php

namespace App\Models;

use App\Models\Review;
use App\Models\Reservation;
use App\Models\WeddingPlannerTransport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guide extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'sex',
        'language',
        'phone',
        'email',
        'address',
        'country',
    ];
    public function reservation(){
        return $this->belongsToMany(Reservation::class,'guide_id');
    }
    public function wp_transports(){
        return $this->hasMany(WeddingPlannerTransport::class,'guide_id');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class,'guide_id');
    }
    public function averageRating()
    {
        return $this->reviews()
            ->selectRaw('
                AVG(attitude) as attitude,
                AVG(explanation) as explanation,
                AVG(knowledge) as knowledge,
                AVG(time_control) as time_control,
                AVG(guide_neatness) as guide_neatness
            ')
            ->first();
    }
    public static function topGuides($limit = 5)
    {
        return self::select('guides.*')
            ->addSelect([
                'avg_rating' => Review::selectRaw('AVG((attitude + knowledge + explanation + time_control + guide_neatness)/5)')
                    ->whereColumn('guide_id', 'guides.id')
            ])
            ->orderByDesc('avg_rating')
            ->take($limit)
            ->get();
    }
    public function reviewSummary()
    {
        $count = $this->reviews()->count();
        $average = $this->reviews()
            ->selectRaw('AVG((attitude + knowledge + explanation + time_control + guide_neatness)/5) as global_rating')
            ->first();
        return [
            'count' => $count,
            'global_rating' => $average->global_rating ?? 0,
        ];
    }
}
