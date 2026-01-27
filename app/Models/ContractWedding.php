<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractWedding extends Model
{
    use HasFactory;
    protected $fillable=[
        'hotels_id',
        'name',
        'file_name',
        'period_start',
        'period_end',
    ];

    public function contract_wedding(){
        return $this->belongsTo(Hotels::class,'hotels_id');
    }
}
