<?php

namespace App\Models;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agent extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_name', 'pic_name', 'email', 'phone',
        'country', 'company_address', 'website',
        'business_license', 'tax_document',
        'company_letter', 'translation_documents',
        'status',
    ];

    protected $casts = [
        'translation_documents' => 'array',
    ];
    
    public function reservation(){
        return $this->hasMany(Reservation::class,'agn_id');
    }
}
