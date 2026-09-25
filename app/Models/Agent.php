<?php

namespace App\Models;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agent extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'company_name', 'company_type', 'pic_name', 'contact_name', 'contact_email', 'email', 'phone',
        'country', 'company_address', 'website',
        'business_license_number', 'position', 'preferred_contact', 'main_market', 'monthly_bali_clients',
        'interested_services', 'agreed_to_terms_at', 'agreed_to_contact_at',
        'business_license', 'tax_document',
        'company_letter', 'translation_documents',
        'status',
    ];

    protected $casts = [
        'translation_documents' => 'array',
        'interested_services' => 'array',
        'agreed_to_terms_at' => 'datetime',
        'agreed_to_contact_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(AgentDocument::class);
    }
    
    public function reservation(){
        return $this->hasMany(Reservation::class,'agn_id');
    }
}
