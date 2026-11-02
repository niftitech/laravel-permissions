<?php

declare(strict_types=1);

namespace LaravelPermissions\LaravelPermissions;

use Illuminate\Support\ServiceProvider;
use LaravelPermissions\LaravelPermissions\Console\Commands\LaravelPermissionsCommand;

class LaravelPermissionsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-permissions.php', 'laravel-permissions');

        $this->app->singleton(LaravelPermissions::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'laravel-permissions');

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/laravel-permissions.php' => config_path('laravel-permissions.php'),
        ], ['laravel-permissions', 'laravel-permissions-config']);

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/laravel-permissions'),
        ], ['laravel-permissions', 'laravel-permissions-lang']);

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/laravel-permissions'),
        ], ['laravel-permissions', 'laravel-permissions-assets']);

        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], ['laravel-permissions', 'laravel-permissions-migrations']);

        $this->commands([
            LaravelPermissionsCommand::class,
        ]);
    }
}
