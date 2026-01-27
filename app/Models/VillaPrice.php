<?php

namespace App\Models;

use App\Models\Villas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VillaPrice extends Model
{
    use HasFactory;
    protected $fillable = [
        'villa_id',
        'start_date',
        'end_date',
        'markup',
        'contract_rate',
        'kick_back',
        'benefits',
        'benefits_traditional',
        'benefits_simplified',
        'additional_info',
        'additional_info_traditional',
        'additional_info_simplified',
        'cancellation_policy',
        'cancellation_policy_traditional',
        'cancellation_policy_simplified',
        'author',
        'status',
    ];

    public function villa(){
        return $this->belongsTo(Villas::class,'villa_id');
    }
    
    public function calculatePrice($usdrates, $tax)
    {
        $villa_cr = $this->contract_rate;
        $villa_usd = (ceil($villa_cr / $usdrates->rate)) + $this->markup;
        $villa_tax = ceil($villa_usd * ($tax->tax / 100));
        return $villa_usd + $villa_tax;
    }
}
