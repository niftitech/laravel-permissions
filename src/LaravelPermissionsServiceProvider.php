<?php

declare(strict_types=1);

namespace LaravelPermissions\LaravelPermissions;

use Illuminate\Support\ServiceProvider;

class LaravelPermissionsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-permissions.php', 'laravel-permissions');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/laravel-permissions.php' => config_path('laravel-permissions.php'),
        ], ['laravel-permissions', 'laravel-permissions-config']);
    }
}
