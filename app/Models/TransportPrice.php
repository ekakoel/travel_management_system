<?php

namespace App\Models;

use App\Models\Transports;
use App\Models\TransportPrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransportPrice extends Model
{
    use HasFactory;
    protected $fillable=[
        'transports_id',
        'type',
        'src',
        'dst',
        'duration',
        'contract_rate',
        'markup',
        'extra_time',
        'additional_info',
        'author_id',
    ];

    public function transport(){
        return $this->belongsTo(Transports::class,'transports_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function calculatePrice($usdrates, $tax)
    {
        $contract_rate = $this->contract_rate;
        $usd = (ceil($contract_rate / $usdrates->rate)) + $this->markup;
        $tax_rate = ceil($usd * ($tax->tax / 100));
        return $usd + $tax_rate;
    }
}
