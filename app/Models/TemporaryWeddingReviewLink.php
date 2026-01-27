<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryWeddingReviewLink extends Model
{
    use HasFactory;
    protected $fillable = [
        'wedding_organizer',
        'booking_code',
        'groom',
        'bride',
        'jumlah_review',
        'expires_at',
        'qr_code_path',
        'link',
        'wedding_date',
    ];
    public $timestamps = true;
}
