<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportType extends Model
{
    use HasFactory;
    protected $fillable=[
        'type',
    ];

    public function transports()
    {
        return $this->hasMany(Transports::class, 'type', 'type');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->select(['type as text','id']);
    }
}
