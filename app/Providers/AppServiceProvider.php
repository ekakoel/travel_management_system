<?php

namespace App\Providers;

use App\Services\BusinessProfileService;
use App\Services\FooterContentService;
use App\Services\Navigation\BackendNavigationService;
use App\Services\RegistrationAccessService;
use App\View\Composers\BackendNavigationComposer;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->scoped(BackendNavigationService::class);
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
        View::composer([
            'component.menu',
            'backend.partials.left-navbar',
        ], BackendNavigationComposer::class);

        View::composer('frontend.layouts.navbar', function ($view) {
            if (array_key_exists('globalServices', $view->getData())) {
                return;
            }

            $view->with(
                'globalServices',
                app(BackendNavigationService::class)->navigationItems()
            );
        });
    }
}
