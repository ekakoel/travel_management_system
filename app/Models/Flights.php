<?php

namespace App\Models;

use App\Models\Guests;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Flights extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'group',
        'flight',
        'time',
        'guests',
        'guests_contact',
        'number_of_guests',
        'order_id',
        'status',
    ];
}
