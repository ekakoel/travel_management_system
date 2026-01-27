<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Passport::routes();

        Gate::define('isAdmin', function ($user) {
            return $user->type === 'admin';
        });
        Gate::define('isUser', function ($user) {
            return $user->type === 'user';
        });
        Gate::define('posDev', function ($user) {
            return $user->position === 'developer';
        });
        Gate::define('posAuthor', function ($user) {
            return $user->position === 'author';
        });
        Gate::define('posRsv', function ($user) {
            return $user->position === 'reservation';
        });
        Gate::define('posStaff', function ($user) {
            return $user->position === 'staff';
        });
        Gate::define('weddingDvl', function ($user) {
            return $user->position === 'weddingDvl';
        });
        Gate::define('weddingRsv', function ($user) {
            return $user->position === 'weddingRsv';
        });
        Gate::define('weddingSls', function ($user) {
            return $user->position === 'weddingSls';
        });
        Gate::define('weddingAuthor', function ($user) {
            return $user->position === 'weddingAuthor';
        });
        Gate::define('devRsv', function ($user) {
            return in_array($user->position, ['developer', 'reservation']);
        });
        Gate::define('devRsvAut', function ($user) {
            return in_array($user->position, ['developer', 'reservation','author']);
        });
    }
}
