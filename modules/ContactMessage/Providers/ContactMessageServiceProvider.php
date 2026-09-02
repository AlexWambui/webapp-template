<?php

namespace Modules\ContactMessage\Providers;

use Illuminate\Support\ServiceProvider;

class ContactMessageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->app->register(RouteServiceProvider::class);
    }
}
