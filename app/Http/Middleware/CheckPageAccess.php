<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\UiConfig;

class CheckPageAccess
{
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        $routeName = $route ? $route->getName() : null;

        if (!$routeName) {
            return $next($request);
        }

        $uiConfig = UiConfig::where('name', $routeName)->first();

        if ($uiConfig && !ui_config($routeName, true)) {
            abort(403, $uiConfig->message ?: 'This page is currently unavailable.');
        }

        return $next($request);
    }
}
