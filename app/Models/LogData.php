<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogData extends Model
{
    use HasFactory;
    protected $fillable = [
        'service',
        'initial_data',
        'final_data',
        'action',
        'user_id',
    ];
}
