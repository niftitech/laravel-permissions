<?php

declare(strict_types=1);

namespace LaravelPermissions\LaravelPermissions\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \LaravelPermissions\LaravelPermissions\LaravelPermissions
 */
class LaravelPermissions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \LaravelPermissions\LaravelPermissions\LaravelPermissions::class;
    }
}
