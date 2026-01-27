<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermAndCondition extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'name_id',
        'name_en',
        'name_zh',
        'policy_en',
        'policy_zh',
        'policy_id',
        'status',
    ];
}
