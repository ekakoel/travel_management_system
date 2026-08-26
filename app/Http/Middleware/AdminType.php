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
            return redirect('/dashboard')->with('error_messages',__('messages.You are not authorized to access this page, please contact the system developer for confirmation!'));
        }
        return $next($request);
    }
}