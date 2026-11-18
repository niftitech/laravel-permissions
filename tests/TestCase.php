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

    protected function setUp(): void
    {
        parent::setUp();

        // Set after the provider boots: defineEnvironment() runs earlier, and the provider's
        // shallow mergeConfigFrom() would then replace the whole `models` array with this one key.
        config()->set('laravel-permissions.models.user', User::class);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(dirname(__DIR__).'/database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }
}
