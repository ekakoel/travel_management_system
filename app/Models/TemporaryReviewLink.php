<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryReviewLink extends Model
{
    use HasFactory;
    protected $fillable = [
        'agent',
        'booking_code',
        'jumlah_review',
        'expires_at',
        'qr_code_path',
        'link',
        'arrival_date',
        'departure_date',
    ];
    public $timestamps = true;
}
