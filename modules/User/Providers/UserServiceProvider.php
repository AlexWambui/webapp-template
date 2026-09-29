<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Gate;
use Modules\User\Models\User;
use Modules\User\Policies\UserPolicy;

class UserServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // // Merge a custom config file
        //$this->mergeConfigFrom(__DIR__ . '/../config.php', 'user');

        $this->app->register(RouteServiceProvider::class);

        Gate::policy(User::class, UserPolicy::class);
    }
}
