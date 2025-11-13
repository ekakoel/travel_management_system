<?php

namespace App\Models;

use App\Models\InvoiceAdmin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdditionalInvoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'inv_id',
        'date',
        'description',
        'rate',
        'unit',
        'times',
        'amount',
    ];
    public function invoice(){
        return $this->belongsTo(InvoiceAdmin::class,'inv_id'); //good
    }
}
