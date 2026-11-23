<?php

namespace App\Models;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorPackage extends Model
{
    use HasFactory;
    protected $fillable=[
        'cover',
        'venue',
        'service',
        'duration',
        'type',
        'time',
        'contract_rate',
        'markup',
        'publish_rate',
        'description',
        'capacity',
        'status',
        'vendor_id',
        'hotel_id',
        'author',
    ];

    public function vendors(){
        return $this->belongsTo(Vendor::class,'vendor_id');
    }
}
