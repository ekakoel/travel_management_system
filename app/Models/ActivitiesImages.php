<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Activities;
use Illuminate\Support\Str;

class ActivitiesImages extends Model
{
    use HasFactory;
    protected $fillable=[
        'image',
        'activities_id',
    ];

    public function activity(){
        return $this->belongsTo(Activities::class,'activities_id');
    }

    public function imageStoragePath(): ?string
    {
        if (! $this->image) {
            return null;
        }

        $image = ltrim($this->image, '/');

        if (Str::startsWith($image, ['http://', 'https://'])) {
            return null;
        }

        if (Str::startsWith($image, 'storage/')) {
            return Str::after($image, 'storage/');
        }

        if (Str::startsWith($image, 'activities/')) {
            return $image;
        }

        return 'activities/activities-images/'.$image;
    }

    public function imageUrl(): ?string
    {
        if (Str::startsWith((string) $this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        $path = $this->imageStoragePath();

        return $path ? asset('storage/'.$path) : null;
    }
}
