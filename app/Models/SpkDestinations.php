<?php

namespace App\Models;

use App\Models\Spks;
use App\Models\SpksCheckins;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpkDestinations extends Model
{
    use HasFactory;

    protected $fillable = [
        'spk_id',
        'date',
        'destination_name',
        'destination_address',
        'latitude',
        'longitude',
        'checkin_latitude',
        'checkin_longitude',
        'checkin_map_link',
        'description',
        'visited_at',
        'status'
    ];

    public function spk()
    {
        return $this->belongsTo(Spks::class, 'spk_id');
    }

    public function checkins()
    {
        return $this->hasMany(SpksCheckins::class, 'checkin_id');
    }
}
