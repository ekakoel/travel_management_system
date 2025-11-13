<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Hotels;

class HotelsImages extends Model
{
    use HasFactory;
    protected $fillable=[
        'image',
        'hotels_id',
    ];

    public function hotels(){
        return $this->belongsTo(Hotels::class,'hotels_id');
    }
}
