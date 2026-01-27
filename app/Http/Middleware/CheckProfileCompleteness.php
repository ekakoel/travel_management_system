<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckProfileCompleteness
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (!$this->isProfileComplete($user)) {
            return redirect()->route('profile');
        }

        if (!$user->status=="Active") {
            return redirect()->route('profile');
        }
        return $next($request);
    }

    private function isProfileComplete($user)
    {
        return $user->name && $user->phone && $user->office && $user->address && $user->country;
    }
}

