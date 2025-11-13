<?php

namespace App\Models;

use App\Models\Villas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VillaImages extends Model
{
    use HasFactory;
    protected $fillable=[
        'villa_id',
        'image_path',
        'caption',
        'alt_text',
    ];

    public function villa(){
        return $this->belongsTo(Villas::class,'villa_id');
    }
}
