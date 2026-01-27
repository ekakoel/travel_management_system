<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\Tours;
use App\Models\Category;
use App\Models\ToursImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourType extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'type_traditional',
        'type_simplified',
        'description',
        'description_traditional',
        'description_simplified',
    ];

    public function tours(){
        return $this->hasMany(Tours::class,'type_id');
    }
}
