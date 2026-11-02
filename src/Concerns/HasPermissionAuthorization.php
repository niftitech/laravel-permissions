<?php

declare(strict_types=1);

namespace LaravelPermissions\LaravelPermissions\Concerns;

use BackedEnum;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use InvalidArgumentException;
use LaravelPermissions\LaravelPermissions\Models\PermissionRole;
use UnitEnum;

trait HasPermissionAuthorization
{
    /**
     * @return BelongsToMany<PermissionRole, $this>
     */
    public function permissionRoles(): BelongsToMany
    {
        return $this->belongsToMany(PermissionRole::class, 'permission_role_user');
    }

    /**
     * @param  string|BackedEnum&UnitEnum  $entity  Entity slug, or string-backed enum case whose value is the slug
     * @param  string|BackedEnum&UnitEnum  $action  Action slug, or string-backed enum case whose value is the slug
     */
    public function hasPermissionTo(mixed $entity, mixed $action): bool
    {
        $entitySlug = $this->permissionSlug($entity, 'entity');
        $actionSlug = $this->permissionSlug($action, 'action');

        return $this->permissionRoles()
            ->whereHas('grants', function ($query) use ($entitySlug, $actionSlug): void {
                $query->whereHas('permissionEntity', fn ($entityQuery) => $entityQuery->where('slug', $entitySlug))
                    ->whereHas('permissionAction', fn ($actionQuery) => $actionQuery->where('slug', $actionSlug));
            })
            ->exists();
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
