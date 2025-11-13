<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Activities;

class ActivitiesImages extends Model
{
    use HasFactory;
    protected $fillable=[
        'image',
        'activities_id',
    ];

    public function activities(){
        return $this->belongsTo(Activities::class,'activities_id');
    }
}
