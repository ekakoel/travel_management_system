<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterLink extends Model
{
    protected $fillable = [
        'group',
        'label',
        'label_traditional',
        'label_simplified',
        'route_name',
        'url',
        'icon',
        'sort_order',
        'open_new_tab',
        'status',
    ];

    protected $casts = [
        'open_new_tab' => 'boolean',
        'status' => 'boolean',
    ];
}
