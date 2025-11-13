<?php

namespace App\Providers;

use App\Models\UiConfig;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
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
        config(['app.locale' => 'en']);
	    Carbon::setLocale('en');

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
    }
}
