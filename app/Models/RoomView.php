<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RoomView extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'description'];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($roomView) {
            if (empty($roomView->slug)) {
                $roomView->slug = Str::slug($roomView->name);
            }
        });
    }
}
