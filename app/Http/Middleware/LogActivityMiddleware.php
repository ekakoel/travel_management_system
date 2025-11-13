<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class LogActivityMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        if (Auth::check()) {
            $user = Auth::user();
            $agent = new Agent();
            $ip = $request->ip();
            $log = '[' . date('Y-m-d H.i') . ']->[User] ' . $user->name . ' [doing] ' . $request->method() . ' [at] ' . $request->fullUrl() . ' [using] ' . $agent->getUserAgent().' [from] ' . $ip . PHP_EOL;
            
            $logPath = storage_path('logs/activity.log');
            file_put_contents($logPath, $log, FILE_APPEND);
        }
        
        return $response;
    }
}
