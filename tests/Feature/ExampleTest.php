<?php

declare(strict_types=1);

use LaravelPermissions\LaravelPermissions\LaravelPermissions;

it('resolves the singleton', function () {
    expect(app(LaravelPermissions::class))->toBeInstanceOf(LaravelPermissions::class);
});

it('returns the same instance from the container', function () {
    expect(app(LaravelPermissions::class))->toBe(app(LaravelPermissions::class));
});

it('merges the package config', function () {
    expect(config('laravel-permissions.placeholder'))->toBe('default');
});

it('loads the package translations', function () {
    expect(trans('laravel-permissions::messages.placeholder'))->toBe('LaravelPermissions placeholder translation.');
});

it('registers the artisan command', function () {
    $this->artisan('laravel-permissions:placeholder')
        ->expectsOutputToContain('LaravelPermissions placeholder command executed.')
        ->assertSuccessful();
});
