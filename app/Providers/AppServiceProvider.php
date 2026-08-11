<?php

namespace App\Providers;

use App\Models\Services;
use App\Services\BusinessProfileService;
use App\Services\FooterContentService;
use App\Services\RegistrationAccessService;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }
    public function boot()
    {
        Paginator::useBootstrap();
        Carbon::setLocale(config('app.locale'));

        View::composer('frontend.landing-page.about.index', function ($view) {
            if (array_key_exists('businessProfile', $view->getData())) {
                return;
            }

            $view->with('businessProfile', app(BusinessProfileService::class)->primary());
        });

        View::composer('frontend.layouts.footer-modern', function ($view) {
            if (array_key_exists('footerData', $view->getData())) {
                return;
            }

            $view->with('footerData', app(FooterContentService::class)->data());
        });

        View::composer('auth.login', function ($view) {
            if (array_key_exists('registrationEnabled', $view->getData())) {
                return;
            }

            $view->with('registrationEnabled', app(RegistrationAccessService::class)->enabled());
        });
        View::composer('*', function ($view) {
            $services = Services::query()
                ->where('status', 'Active')
                ->orderBy('name')
                ->get();

            $view->with('globalServices', $services);
        });
    }
}
