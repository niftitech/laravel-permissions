<?php

declare(strict_types=1);

use LaravelPermissions\LaravelPermissions\Tests\PublishTestCase;

uses(PublishTestCase::class);

afterEach(function () {
    if (is_file($config = config_path('laravel-permissions.php'))) {
        unlink($config);
    }

    foreach (glob(database_path('migrations/*_create_permission_authorization_tables.php')) ?: [] as $migration) {
        unlink($migration);
    }
});

it('publishes config', function () {
    $this->artisan('vendor:publish', [
        '--tag' => 'laravel-permissions-config',
    ])->assertSuccessful();

    expect(config_path('laravel-permissions.php'))->toBeFile();
});

it('publishes migrations', function () {
    $this->artisan('vendor:publish', [
        '--tag' => 'laravel-permissions-migrations',
    ])->assertSuccessful();

    expect(glob(database_path('migrations/*_create_permission_authorization_tables.php')))->not->toBeEmpty();
});
