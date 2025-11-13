<?php

namespace App\Models;

use App\Models\Tours;
use App\Models\Activities;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Partners extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'location', 
        'map',
        'cover',
        'type',
        'status',
        'phone',
        'contact_person',
        'author_id',
        'description',
    ];

    public function activity(){
        return $this->hasMany(Activities::class,'partners_id');
    }
    public function tours(){
        return $this->hasMany(Tours::class,'partners_id');
    }
    
}
