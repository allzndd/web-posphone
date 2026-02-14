<?php

namespace App\Providers;

use App\Services\PermissionService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Paginator::useBootstrapFour();

        // Register Gates untuk permission checking
        $this->registerGates();

        // Register Blade directives
        $this->registerBladeDirectives();
    }

    /**
     * Register Gates
     */
    private function registerGates(): void
    {
        // Gate untuk customer permissions
        Gate::define('customer.create', function ($user) {
            return PermissionService::check('customer.create');
        });

        Gate::define('customer.read', function ($user) {
            return PermissionService::check('customer.read');
        });

        Gate::define('customer.update', function ($user) {
            return PermissionService::check('customer.update');
        });

        Gate::define('customer.delete', function ($user) {
            return PermissionService::check('customer.delete');
        });

        // Generic gate untuk dynamic permissions
        Gate::define('has-permission', function ($user, $permission) {
            return PermissionService::check($permission);
        });

        // Gate untuk cek multiple permissions (ANY)
        Gate::define('has-any-permission', function ($user, $permissions) {
            return PermissionService::checkAny($permissions);
        });

        // Gate untuk cek multiple permissions (ALL)
        Gate::define('has-all-permissions', function ($user, $permissions) {
            return PermissionService::checkAll($permissions);
        });
    }

    /**
     * Register Blade Directives
     */
    private function registerBladeDirectives(): void
    {
        // @permission('customer.create') ... @endpermission
        Blade::directive('permission', function ($expression) {
            // Trim quotes from the expression
            $permission = trim($expression, " '\"");
            return "<?php if (\App\Services\PermissionService::check('{$permission}')): ?>";
        });

        Blade::directive('endpermission', function () {
            return "<?php endif; ?>";
        });

        // @canAccess('customer.create') ... @endcanAccess
        Blade::directive('canAccess', function ($expression) {
            // Trim quotes from the expression
            $permission = trim($expression, " '\"");
            return "<?php if (\App\Services\PermissionService::check('{$permission}')): ?>";
        });

        Blade::directive('endcanAccess', function () {
            return "<?php endif; ?>";
        });

        // @hasQuota('customer.create', $count) ... @endhasQuota
        Blade::directive('hasQuota', function ($expression) {
            return "<?php if (!\App\Services\PermissionService::isReachedLimit({$expression})): ?>";
        });

        Blade::directive('endhasQuota', function () {
            return "<?php endif; ?>";
        });
    }
}


