<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class ApproveUser
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->is_approved) {
            return redirect()->route('approval.pending');
        }

        return $next($request);
    }
}
