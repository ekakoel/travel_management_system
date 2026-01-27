<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectRandomUrls
{
    public function handle($request, Closure $next)
    {
        $allowedUrls = [
            // Daftar URL yang diperbolehkan
            '/',
            '/dashboard',
            '/gagal-terhubung',
            '/welcome',
            '/forget-password',
            '/reset-password/{token}',
            '/reset-password',
            '/profile',
            '/login',
            '/profile',
            // ...
        ];

        if (!in_array($request->path(), $allowedUrls)) {
            return view('main.gagal-terhubung'); // Ganti dengan URL tujuan yang diinginkan
        }

        return $next($request);
    }
}

