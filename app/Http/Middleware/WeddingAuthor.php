<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WeddingAuthor
{
    public function handle(Request $request, Closure $next)
    {
        \Log::info('User Position: ' . auth()->user()->position);
        if (auth()->user()->position !== 'weddingAuthor') {
            return redirect('/dashboard')->with('error_messages','You are not authorized to access this page, please contact the system developer for confirmation!');
        }
        return $next($request);
    }
}