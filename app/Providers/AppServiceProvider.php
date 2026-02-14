<?php

namespace App\Providers;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register():void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot():void
    {
        Paginator::useBootstrapFour();

        // Register Blade directive for permission check
        Blade::if('permission', function ($permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });

        // Register Blade directive to check if user can perform action
        Blade::if('can', function ($permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });
    }
}

