<?php

namespace App\Models;

use App\Models\User;
use App\Models\Tours;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Itineraries extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'tour_id',
        'day_number',
        'time',
        'title',
        'title_traditional',
        'title_simplified',
        'description',
        'description_traditional',
        'description_simplified',
        'location',
        'sort_order',
        'status',
    ];
    public function tour(){
        return $this->belongsTo(Tours::class,'tour_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
