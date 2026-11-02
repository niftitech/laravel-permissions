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
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/laravel-permissions.php' => config_path('laravel-permissions.php'),
        ], ['laravel-permissions', 'laravel-permissions-config']);

        $migration = __DIR__.'/../database/migrations/2026_01_01_000001_create_permission_authorization_tables.php';

        $this->publishesMigrations([
            $migration => database_path('migrations/'.basename($migration)),
        ], ['laravel-permissions-migrations', 'laravel-permissions']);
    }
}
