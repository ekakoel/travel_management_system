<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imgactivities extends Model
{
    use HasFactory;
    protected $table = 'imgactivities';
    protected $fillable = [
     'url', 'activity_id'
    ];
  public function activities()
  {
    return $this->belongsTo('App\Activities', 'activity_id');
  }

    
}
