<?php

namespace App\Http\Middleware;

use App\Services\RegistrationAccessService;
use Closure;
use Illuminate\Http\Request;

class EnsureRegistrationIsOpen
{
    public function __construct(protected RegistrationAccessService $registrationAccess)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        if ($this->registrationAccess->enabled()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('messages.Registration is currently unavailable.'),
            ], 403);
        }

        return redirect()
            ->route('login')
            ->withErrors(['registration' => __('messages.Registration is currently unavailable.')]);
    }
}
