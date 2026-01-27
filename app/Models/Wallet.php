<?php

namespace App\Models;

use App\Models\User;
use App\Models\Transactions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
    use HasFactory;
    protected $fillable=[
        'code',
        'deposit_usd',
        'deposit_cny',
        'deposit_twd',
        'deposit_idr',
        'agent_id',
        'status',
    ];
    public function agent(){
        return $this->belongsTo(User::class,'agent_id');
    }
    public function transactions(){
        return $this->hasMany(Transactions::class,'wallet_id');
    }
}
