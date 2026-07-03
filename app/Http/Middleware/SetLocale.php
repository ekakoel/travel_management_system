<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = config('app.supported_locales', ['en']);
        $locale = session('locale');

        if (Auth::check() && in_array(Auth::user()->preferred_language, $supportedLocales, true)) {
            $locale = Auth::user()->preferred_language;
        }

        if (!in_array($locale, $supportedLocales, true)) {
            $locale = config('app.fallback_locale', 'en');
        }

        app()->setLocale($locale);
        Carbon::setLocale($locale === 'zh-CN' ? 'zh_CN' : $locale);

        return $next($request);
    }
}
