<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;
    protected $fillable = [
        'cover',
        'author_id',
        'editor_id',
        'name',
        'description',
        'discounts',
        'periode_start',
        'periode_end',
        'status',
        'term',
        'discount_type',
        'discount_value',
        'discount_currency',
        'service_scope',
        'valid_from',
        'valid_until',
        'pricing_data_status',
    ];

    protected $casts = [
        'discount_value' => 'decimal:6',
        'valid_from' => 'immutable_datetime',
        'valid_until' => 'immutable_datetime',
    ];
}
