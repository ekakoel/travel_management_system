<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPosition
{
    public function handle(Request $request, Closure $next, ...$positions)
    {
        $user = auth()->user();

        if (!in_array($user->position, $positions)) {
            return redirect('/dashboard')->with('error_messages',__('messages.You are not authorized to access this page, please contact the system developer for confirmation!'));
        }

        return $next($request);
    }
}