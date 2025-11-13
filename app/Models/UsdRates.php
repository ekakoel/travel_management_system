<?php

namespace App\Models;

use App\Models\PaymentConfirmation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UsdRates extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'rate',
        'sell',
        'buy',
        'difference',
    ];
    public function receipts(){
        return $this->hasMany(PaymentConfirmation::class);
    }
}
