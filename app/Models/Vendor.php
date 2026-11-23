<?php

namespace App\Models;

use App\Models\VendorPackage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'location',
        'cover',
        'type',
        'contact_name',
        'phone',
        'email',
        'term',
        'description',
        'author_id',
        'status',
    ];

    public function packages(){
        return $this->hasMany(VendorPackage::class,'vendor_id');
    }
}
