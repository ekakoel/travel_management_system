<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BedType extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'description'];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($bedType) {
            if (empty($bedType->slug)) {
                $bedType->slug = Str::slug($bedType->name);
            }
        });
    }
}
