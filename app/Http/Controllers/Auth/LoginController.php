<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
    use ThrottlesLogins;

    protected $maxAttempts = 5;
    protected $decayMinutes = 1;
    // protected $maxLoginAttempts = 10; 
    // protected $lockoutTime = 120; 
 
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm(Request $request)
    {
        $redirect = (string) $request->query('redirect', '');

        if ($this->isSafeRedirectTarget($redirect)) {
            session(['url.intended' => $redirect]);
        }

        return view('auth.login');
    }
    
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return $this->limiter()->tooManyAttempts($this->throttleKey($request), 6, 30);
    }

    private function isSafeRedirectTarget(string $redirect): bool
    {
        if ($redirect === '') {
            return false;
        }

        if (Str::startsWith($redirect, '/')) {
            return true;
        }

        return Str::startsWith($redirect, [
            url('/'),
            secure_url('/'),
        ]);
    }
}
