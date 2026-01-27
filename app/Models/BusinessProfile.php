<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'license',
        'tax_number',
        'address',
        'nickname',
        'tax_id',
        'type',
        'map',
        'phone',
        'logo',
        'caption',
        'website',
        'instagram',
        'facebook',
        'twitter',
    ];
}
