<?php

namespace App\Models;

use App\Models\Orders;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderNote extends Model
{
    use HasFactory;
    use HasFactory;
    protected $fillable = [
        'order_id',
        'order_wedding_id',
        'note',
        'user_id',
        'status',
    ];

    public function orders(){
        return $this->belongsTo(Orders::class,'order_id');
    }
}
