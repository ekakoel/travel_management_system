<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_hash',
        'user_id',
        'method',
        'path',
        'url',
        'route_name',
        'page_title',
        'area',
        'country_code',
        'country_name',
        'referrer_host',
        'device_type',
        'browser',
        'platform',
        'ip_hash',
        'user_agent_hash',
        'visit_date',
        'occurred_at',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'occurred_at' => 'datetime',
    ];
}
