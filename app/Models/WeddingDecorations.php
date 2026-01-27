<?php

namespace App\Models;

use App\Models\Hotels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeddingDecorations extends Model
{
    use HasFactory;
    protected $fillable = [
        'hotel_id',
        'vendor_id',
        'venue',
        'cover',
        'name',
        'duration',
        'capacity',
        'description',
        'terms_and_conditions',
        'price',
        'status',
        'author_id',
    ];
    public function hotel(){
        return $this->belongsTo(Hotels::class,'hotel_id');
    }
}
