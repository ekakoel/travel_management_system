<?php

namespace App\Providers;

use App\Models\UiConfig;
use App\Services\BusinessProfileService;
use App\Services\FooterContentService;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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

        Blade::directive('uiEnabled', function ($name) {
            return "<?php 
                \$config = App\Models\UiConfig::get($name, true);
                if (\$config->status): 
            ?>";
        });

        Blade::directive('elseUiEnabled', function ($name) {
            return "<?php else: ?>
                <p class='text-danger'><?= \$config->message ?></p>
            ";
        });

        Blade::directive('endUiEnabled', function () {
            return "<?php endif; ?>";
        });

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
    }
}
