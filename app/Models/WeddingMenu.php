<?php

namespace App\Models;

use App\Models\Hotels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeddingMenu extends Model
{
    use HasFactory;
    protected $fillable = [
        'hotels_id',
        'type',
        'package',
        'name',
        'duration',
        'include',
        'description',
        'agent_rate',
        'public_rate',
        'fee',
        'note',
        'status',
    ];
    public function hotels(){
        return $this->belongsTo(Hotels::class,'hotels_id');
    }
}
