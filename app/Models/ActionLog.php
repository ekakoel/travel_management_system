<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'action',
        'service',
        'service_id',
        'page',
        'action_note',
        'user_ip',
        'initial_state',
        'final_state',
    ];
}
