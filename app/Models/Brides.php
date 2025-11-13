<?php

namespace App\Models;

use App\Models\OrderWedding;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brides extends Model
{
    use HasFactory;
    protected $fillable = [
        'groom',
        'groom_chinese',
        'groom_contact',
        'groom_pasport_id',
        'groom_pasport_img',
        'bride',
        'bride_chinese',
        'bride_contact',
        'bride_pasport_id',
        'bride_pasport_img',
    ];
    public function weddings(){
        return $this->hasMany(OrderWedding::class,'bride_id');
    }
}
