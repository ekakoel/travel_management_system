<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'value_traditional',
        'value_simplified',
        'status',
    ];
}
