<?php

namespace Modules\Support\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class SupportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(\Modules\User\Providers\UserServiceProvider::class);
    }

    public function boot(Router $router): void
    {
        // Register 'role' middleware alias globally
        $router->aliasMiddleware('role', \Modules\Support\Http\Middleware\RoleMiddleware::class);
    }
}
