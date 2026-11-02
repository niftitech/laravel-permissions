<?php

declare(strict_types=1);

it('merges the package config', function () {
    expect(config('laravel-permissions.models.user'))->toBe(Workbench\App\Models\User::class);
});
