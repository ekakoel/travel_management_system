<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Transports;

class TransportsImages extends Model
{
    use HasFactory;
    protected $fillable=[
        'image',
        'transports_id',
    ];

    public function transports(){
        return $this->belongsTo(Hotels::class,'transports_id');
    }
}
