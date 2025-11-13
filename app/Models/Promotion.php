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
    ];
}
