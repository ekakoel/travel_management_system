<?php

namespace App\Models;

use App\Models\Wallet;
use App\Models\InvoiceAdmin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transactions extends Model
{
    use HasFactory;
    protected $fillable=[
        'receipt',
        'kurs',
        'transaction_date',
        'wallet_id',
        'transaction_code',
        'invoice_id',
        'name',
        'type',
        'amount',
        'description',
        'author_id',
        'note',
        'status',
    ];
    public function wallet(){
        return $this->belongsTo(Wallet::class,'wallet_id');
    }
    public function invoice(){
        return $this->belongsTo(InvoiceAdmin::class,'invoice_id');
    }
}
