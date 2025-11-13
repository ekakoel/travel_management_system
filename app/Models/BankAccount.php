<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        'bank',
        'currency',
        'account_name',
        'account_number',
        'location',
        'address',
        'telephone',
        'swift_code',
        'bank_code',
    ];
}
