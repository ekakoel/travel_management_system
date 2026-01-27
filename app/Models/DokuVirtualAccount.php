<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokuVirtualAccount extends Model
{
    use HasFactory;

    protected $table = 'doku_virtual_accounts';

    protected $fillable = [
        'invoice_id',
        'invoice_number',
        'currency',
        'amount',
        'created_date',
        'expired_date',
        'token_id',
        'payment_Payment deadline',
        'checkout_url',
        'payment_date',
        'payment_method',
        'provider',
        'status',
    ];
}

