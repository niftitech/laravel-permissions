<?php

declare(strict_types=1);

namespace LaravelPermissions\LaravelPermissions\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelPermissions\LaravelPermissions\LaravelPermissionsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Workbench\App\Models\User;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;
    protected function getPackageProviders($app): array
    {
        return [
            LaravelPermissionsServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        config()->set('laravel-permissions.models.user', User::class);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(dirname(__DIR__).'/database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }
}
