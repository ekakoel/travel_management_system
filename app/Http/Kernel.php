<?php

namespace App\Http;

use App\Http\Middleware\LogActivityMiddleware;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        // \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \Illuminate\Http\Middleware\HandleCors::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Laravel\Passport\Http\Middleware\CreateFreshApiToken::class,
            \RealRashid\SweetAlert\ToSweetAlert::class,
            \App\Http\Middleware\UserActivity::class,
            LogActivityMiddleware::class,
            
            \App\Http\Middleware\SetLocale::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'redirectRandomUrls' => \App\Http\Middleware\RedirectRandomUrls::class,
        'type' => \App\Http\Middleware\Type::class,
        'position' => \App\Http\Middleware\Position::class,
        'profile.complete' => \App\Http\Middleware\CheckProfileCompleteness::class,
        'approve' => \App\Http\Middleware\ApproveUser::class,
        'adminType' => \App\Http\Middleware\AdminType::class,
        'reservationPos' => \App\Http\Middleware\ReservationPos::class,
        'developerPos' => \App\Http\Middleware\DeveloperPos::class,
        'authorPos' => \App\Http\Middleware\AuthorPos::class,
        'staffPos' => \App\Http\Middleware\StaffPos::class,
        'weddingDvl' => \App\Http\Middleware\WeddingDdvl::class,
        'weddingSls' => \App\Http\Middleware\WeddingSls::class,
        'weddingRsv' => \App\Http\Middleware\WeddingRsv::class,
        'weddingAuthor' => \App\Http\Middleware\WeddingAuthor::class,
        'checkPosition' => \App\Http\Middleware\CheckPosition::class,
        'page.access' => \App\Http\Middleware\CheckPageAccess::class,
    ];
}
