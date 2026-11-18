<?php

declare(strict_types=1);

namespace LaravelPermissions\LaravelPermissions;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

final class PermissionModels
{
    /**
     * Reads a model class from `laravel-permissions.models`. A published config without the key
     * fails here with a clear message instead of passing null to a relation.
     *
     * @return class-string<Model>
     */
    public static function classFor(string $name): string
    {
        $modelClass = config("laravel-permissions.models.{$name}");

        if (! is_string($modelClass) || ! is_a($modelClass, Model::class, true)) {
            throw new InvalidArgumentException(sprintf(
                'The laravel-permissions.models.%s config value must be an Eloquent model class.',
                $name,
            ));
        }

        return $modelClass;
    }
}
