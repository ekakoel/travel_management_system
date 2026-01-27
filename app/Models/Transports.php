<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\AirportShuttle;
use App\Models\TransportPrice;
use App\Models\TransportsImages;
use App\Models\WeddingPlannerTransport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transports extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'type',
        'brand',
        'duration',
        'description',
        'include',
        'additional_info',
        'cancellation_policy',
        'contract_rate',
        'markup',
        'capacity',
        'status',
        'author_id',
        'cover',
    ];


    public function images(){
        return $this->hasMany(TransportsImages::class,'transports_id');
    }

    public function prices(){
        return $this->hasMany(TransportPrice::class,'transports_id');
    }
    public function wp_transports(){
        return $this->hasMany(WeddingPlannerTransport::class,'transports_id');
    }
    public function airport_shuttles(){
        return $this->hasMany(AirportShuttle::class,'transport_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->select(['name as text','id']);
    }


}
