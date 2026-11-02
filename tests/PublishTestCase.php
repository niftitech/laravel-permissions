<?php

declare(strict_types=1);

namespace LaravelPermissions\LaravelPermissions\Tests;

use LaravelPermissions\LaravelPermissions\LaravelPermissionsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class PublishTestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelPermissionsServiceProvider::class,
        ];
    }
}
