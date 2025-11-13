<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'order_wedding_id',
        'action',
        'url',
        'method',
        'agent',
        'admin',
    ];
}
