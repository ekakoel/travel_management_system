<?php

namespace App\Models;

use App\Models\Activities;
use App\Models\Transports;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Partners extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'Draft';
    public const STATUS_ACTIVE = 'Active';
    public const STATUS_REMOVED = 'Removed';

    protected $fillable = [
        'name',
        'address',
        'location', 
        'map',
        'cover',
        'type',
        'status',
        'phone',
        'contact_person',
        'author_id',
        'description',
    ];

    public function scopeNotRemoved($query)
    {
        return $query->where('status', '!=', self::STATUS_REMOVED);
    }

    public function coverStoragePath(): ?string
    {
        if (! $this->cover) {
            return null;
        }

        if (Str::startsWith($this->cover, ['http://', 'https://'])) {
            return null;
        }

        $cover = ltrim($this->cover, '/');

        if (Str::startsWith($cover, 'storage/')) {
            return Str::after($cover, 'storage/');
        }

        if (Str::startsWith($cover, 'partners/covers/')) {
            return $cover;
        }

        return 'partners/covers/' . $cover;
    }

    public function coverUrl(): ?string
    {
        if (Str::startsWith((string) $this->cover, ['http://', 'https://'])) {
            return $this->cover;
        }

        $path = $this->coverStoragePath();

        return $path ? asset('storage/' . $path) : null;
    }

    public function activity()
    {
        return $this->hasMany(Activities::class, 'partners_id');
    }

    public function transports()
    {
        return $this->hasMany(Transports::class, 'partner_id');
    }
}
