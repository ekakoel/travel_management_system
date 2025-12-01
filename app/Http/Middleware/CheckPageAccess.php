<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\UiConfig;

class CheckPageAccess
{
    // public function handle(Request $request, Closure $next)
    // {
    //     $routeName = $request->route()->getName();
    //     $uiConfig = UiConfig::where('name',$routeName)->first();
    //     if ($routeName && !ui_config($routeName)) {
    //         abort(403, $uiConfig->message);
    //     }

    //     return $next($request);
    // }
    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()->getName();
        $uiConfig = UiConfig::where('name',$routeName)->first();

        // Jika routeName ada dan akses tidak diizinkan
        if ($routeName && !ui_config($routeName)) {

            // Ambil message jika ada, jika tidak gunakan default
            $message = $uiConfig->message ?? 'Access denied';

            // Redirect ke dashboard + kirim pesan error
            return redirect('/dashboard')->with('error', $message);
        }

        return $next($request);
    }
}
