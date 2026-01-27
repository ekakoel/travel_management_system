<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxDoku extends Model
{
    use HasFactory;
    protected $fillable = [
        'tax_name',
        'tax_rate',
        'description',
    ];
}
