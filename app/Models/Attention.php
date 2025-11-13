<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attention extends Model
{
    use HasFactory;
    protected $fillable = [
        'page',
        'name',
        'attention_zh',
        'attention_en',
    ];
    public function tags()
    {
        return $this->belongsToMany(Tag::class)->select(['name as text','id']);
    }
}
