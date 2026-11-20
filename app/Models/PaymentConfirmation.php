<?php

namespace App\Models;

use App\Models\UsdRates;
use App\Models\InvoiceAdmin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentConfirmation extends Model
{
    use HasFactory;

    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_DOKU = 'doku';

    protected $fillable=[
        'kurs_id',
        'receipt_img',
        'inv_id',
        'payment_date',
        'note',
        'amount',
        'status',
        'source',
        'gateway_reference',
    ];

    public function invoice(){
        return $this->belongsTo(InvoiceAdmin::class,'inv_id');
    }
    public function kurs(){
        return $this->belongsTo(UsdRates::class,'kurs_id');
    }
    public function dokuPayment(){
        return $this->hasOne(DokuPayment::class,'payment_confirmation_id');
    }
    public function isDoku(): bool
    {
        return $this->source === self::SOURCE_DOKU;
    }
}
