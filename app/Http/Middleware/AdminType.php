<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminType
{
    public function handle(Request $request, Closure $next)
    {
        \Log::info('User Position: ' . auth()->user()->type);
        if (auth()->user()->type !== 'admin') {
            return redirect('/dashboard');
        }
        return $next($request);
    }
}