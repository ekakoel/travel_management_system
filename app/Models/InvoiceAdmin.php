<?php

namespace App\Models;

use App\Models\UsdRates;
use App\Models\BankAccount;
use App\Models\Reservation;
use App\Models\Transactions;
use App\Models\AdditionalInvoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceAdmin extends Model
{
    use HasFactory;
    protected $fillable = [
        'admin_code',
        'created_by',
        'agent_id',
        'inv_no',
        'rsv_id',
        'inv_date',
        'due_date',
        'total_usd',
        'total_idr',
        'total_cny',
        'total_twd',
        'rate_usd',
        'sell_usd',
        'rate_twd',
        'sell_twd',
        'rate_cny',
        'sell_cny',
        'balance',
        'bank_id',
        'currency_id',
    ];

    public function reservations(){
        return $this->belongsTo(Reservation::class,'inv_id');
    }
    public function additionalinv(){
        return $this->hasMany(AdditionalInvoice::class,'inv_id'); //good
    }
    public function payment(){
        return $this->hasMany(PaymentConfirmation::class,'inv_id'); //good
    }
    public function currency(){
        return $this->belongsTo(UsdRates::class,'currency_id'); //good
    }
    public function bank(){
        return $this->belongsTo(BankAccount::class,'bank_id'); //good
    }
    public function transactions(){
        return $this->hasMany(Transactions::class,'invoice_id'); //good
    }

}
