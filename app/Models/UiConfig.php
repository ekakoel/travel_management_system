<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UiConfig extends Model
{
    use HasFactory;
    protected $fillable = [
        'page',
        'name',
        'status',
        'message',
    ];
    public static function get($name, $default = false)
    {
        return self::where('name', $name)->first() ?? new self(['status' => $default, 'message' => 'Fitur ini dinonaktifkan.']);
    }

    public static function set($page, $name, $status, $message)
    {
        return self::updateOrCreate(
            ['name' => $name],
            ['status' => $status, 'message' => $message, 'page' => $page]
        );
    }
}
