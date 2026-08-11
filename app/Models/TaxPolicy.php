<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'service',
        'name',
        'percentage_scaled',
        'percentage_scale',
        'calculation_type',
        'taxable_base',
        'status',
        'effective_from',
        'effective_until',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'percentage_scaled' => 'integer',
        'percentage_scale' => 'integer',
        'effective_from' => 'immutable_datetime',
        'effective_until' => 'immutable_datetime',
        'approved_at' => 'immutable_datetime',
    ];
}
