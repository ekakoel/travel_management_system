<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'action',
        'service',
        'subservice',
        'subservice_id',
        'page',
        'user_ip',
        'note',
    ];
}
