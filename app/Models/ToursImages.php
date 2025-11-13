<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Tours;

class ToursImages extends Model
{
    use HasFactory;
    protected $fillable=[
        'image',
        'tour_id'
    ];

    public function tours(){
        return $this->belongsTo(Tours::class,'tour_id');
    }
}
