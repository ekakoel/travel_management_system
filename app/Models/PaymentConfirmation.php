<?php

namespace App\Models;

use App\Models\UsdRates;
use App\Models\InvoiceAdmin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentConfirmation extends Model
{
    use HasFactory;
    protected $fillable=[
        'kurs_id',
        'receipt_img',
        'inv_id',
        'payment_date',
        'note',
        'amount',
        'status',
    ];
    public function invoice(){
        return $this->belongsTo(InvoiceAdmin::class,'inv_id');
    }
    public function kurs(){
        return $this->belongsTo(UsdRates::class,'kurs_id');
    }
}
