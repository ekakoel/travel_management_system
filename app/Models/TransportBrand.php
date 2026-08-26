<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportBrand extends Model
{
    use HasFactory;
    protected $fillable = [
        'brand',
    ];

    public function transports()
    {
        return $this->hasMany(Transports::class, 'brand', 'brand');
    }
}
