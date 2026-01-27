<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Activities;

class ActivityType extends Model
{
    use HasFactory;
    protected $fillable=[
        'type',
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->select(['type as text','id']);
    }
}
