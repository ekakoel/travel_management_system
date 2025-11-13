<?php

namespace App\Models;

use App\Models\Hotels;
use App\Models\Villas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contract extends Model
{
    use HasFactory;
    protected $fillable=[
        'hotels_id',
        'villa_id',
        'name',
        'file_name',
        'period_start',
        'period_end',
    ];

    public function hotels(){
        return $this->belongsTo(Hotels::class,'hotels_id');
    }
    public function villa(){
        return $this->belongsTo(Villas::class,'villa_id');
    }
}
