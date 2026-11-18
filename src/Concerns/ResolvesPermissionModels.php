<?php

declare(strict_types=1);

namespace LaravelPermissions\LaravelPermissions\Concerns;

use BackedEnum;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use UnitEnum;

trait ResolvesPermissionModels
{
    /**
     * Accepts a model instance as-is; slugs and string-backed enum cases are looked up by slug.
     *
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $modelClass
     * @return TModel
     */
    private function resolvePermissionModel(string $modelClass, mixed $value, string $argument): Model
    {
        if ($value instanceof $modelClass) {
            return $value;
        }

        return $modelClass::query()->where('slug', $this->permissionSlug($value, $argument))->firstOrFail();
    }

    private function permissionSlug(mixed $value, string $argument): string
    {
        if (is_string($value)) {
            return $value;
        }

        if ($value instanceof UnitEnum) {
            if (! $value instanceof BackedEnum || ! is_string($value->value)) {
                throw new InvalidArgumentException(sprintf(
                    'The %s argument must be a string or a string-backed enum case.',
                    $argument,
                ));
            }

            return $value->value;
        }

        throw new InvalidArgumentException(sprintf(
            'The %1$s argument must be a string or a string-backed enum case.',
            $argument,
        ));
    }
}
