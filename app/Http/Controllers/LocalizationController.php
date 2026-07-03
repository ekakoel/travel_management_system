<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocalizationController extends Controller
{
    public function changeLanguage($locale)
    {
        $supportedLocales = config('app.supported_locales', ['en']);

        abort_unless(in_array($locale, $supportedLocales, true), 404);

        session(['locale' => $locale]);

        if (Auth::check()) {
            Auth::user()->forceFill([
                'preferred_language' => $locale,
            ])->save();
        }

        $redirect = request()->get('redirect');

        if ($redirect && $this->isSafeRedirect($redirect, request()->getHost())) {
            return redirect($redirect);
        }

        $previous = url()->previous();

        if ($previous && $this->isSafeRedirect($previous, request()->getHost()) && $previous !== request()->fullUrl()) {
            return redirect($previous);
        }

        return redirect(url('/'));
    }

    private function isSafeRedirect(string $redirect, string $currentHost): bool
    {
        if (str_starts_with($redirect, '/')) {
            return true;
        }

        if (!filter_var($redirect, FILTER_VALIDATE_URL)) {
            return false;
        }

        $redirectHost = parse_url($redirect, PHP_URL_HOST);

        return $redirectHost === $currentHost;
    }
}
