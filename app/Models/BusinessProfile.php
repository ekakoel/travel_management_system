<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessProfile extends Model
{
    use HasFactory;
    protected $fillable = [
        'profile_key',
        'name',
        'license',
        'tax_number',
        'address',
        'nickname',
        'tax_id',
        'type',
        'map',
        'phone',
        'phone_2',
        'phone_3',
        'email',
        'whatsapp',
        'logo',
        'logo_dark',
        'caption',
        'public_tagline',
        'public_tagline_traditional',
        'public_tagline_simplified',
        'public_description',
        'public_description_traditional',
        'public_description_simplified',
        'website',
        'instagram',
        'facebook',
        'twitter',
        'youtube',
        'linkedin',
    ];
}
