<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StaffPos
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->position !== 'staff') {
            return redirect('/dashboard')->with('error_messages','You are not authorized to access this page, please contact the system developer for confirmation!');
        }
        return $next($request);
    }
}
