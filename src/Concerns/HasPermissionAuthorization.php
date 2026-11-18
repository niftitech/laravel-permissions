<?php

declare(strict_types=1);

namespace LaravelPermissions\LaravelPermissions\Concerns;

use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use LaravelPermissions\LaravelPermissions\PermissionCache;
use LaravelPermissions\LaravelPermissions\PermissionModels;
use UnitEnum;

trait HasPermissionAuthorization
{
    use ResolvesPermissionModels;

    /**
     * @return BelongsToMany<Model, $this>
     */
    public function permissionRoles(): BelongsToMany
    {
        // Pivot keys are explicit: Laravel would otherwise derive them from the role class's basename.
        return $this->belongsToMany(PermissionModels::classFor('role'), 'permission_role_user', 'user_id', 'permission_role_id');
    }

    /**
     * @param  string|(UnitEnum&BackedEnum)  $entity  Entity slug, or string-backed enum case whose value is the slug
     * @param  string|(UnitEnum&BackedEnum)  $action  Action slug, or string-backed enum case whose value is the slug
     */
    public function hasPermissionTo(mixed $entity, mixed $action): bool
    {
        $entitySlug = $this->permissionSlug($entity, 'entity');
        $actionSlug = $this->permissionSlug($action, 'action');

        if (PermissionCache::enabled()) {
            return $this->hasCachedPermission($entitySlug, $actionSlug);
        }

        return $this->permissionRoles()
            ->whereHas('grants', function (Builder $query) use ($entitySlug, $actionSlug): void {
                $query->whereHas('permissionEntity', fn (Builder $entityQuery) => $entityQuery->where('slug', $entitySlug))
                    ->whereHas('permissionAction', fn (Builder $actionQuery) => $actionQuery->where('slug', $actionSlug));
            })
            ->exists();
    }

    /**
     * Adds a role and keeps the user's other roles. Adding a role the user already has does nothing.
     *
     * @param  mixed  $role  PermissionRole, role slug, or string-backed enum case whose value is the slug
     */
    public function assignRole(mixed $role): void
    {
        // Not attach(): attaching a role the user already has violates the pivot's primary key.
        $this->permissionRoles()->syncWithoutDetaching([$this->resolvePermissionRole($role)->getKey()]);

        PermissionCache::forgetUser($this->getKey());
    }

    /**
     * @param  mixed  $role  PermissionRole, role slug, or string-backed enum case whose value is the slug
     */
    public function removeRole(mixed $role): void
    {
        $this->permissionRoles()->detach($this->resolvePermissionRole($role));

        PermissionCache::forgetUser($this->getKey());
    }

    /**
     * Replaces the user's roles with the given set.
     *
     * @param  iterable<int, mixed>  $roles  Each item is a PermissionRole, role slug, or string-backed enum case whose value is the slug
     */
    public function syncRoles(iterable $roles): void
    {
        $roleIds = collect($roles)
            ->map(fn (mixed $role): mixed => $this->resolvePermissionRole($role)->getKey())
            ->all();

        $this->permissionRoles()->sync($roleIds);

        PermissionCache::forgetUser($this->getKey());
    }

    private function resolvePermissionRole(mixed $role): Model
    {
        return $this->resolvePermissionModel(PermissionModels::classFor('role'), $role, 'role');
    }

    /**
     * Same answer as the query in hasPermissionTo(), read from the cache: the user's role ids,
     * then each role's grants.
     */
    private function hasCachedPermission(string $entitySlug, string $actionSlug): bool
    {
        $roleIds = PermissionCache::rememberUserRoleIds(
            $this->getKey(),
            fn (): array => $this->permissionRoles()->pluck('id')->all(),
        );

        foreach ($roleIds as $roleId) {
            $actionSlugsByEntity = PermissionCache::rememberRoleGrants(
                $roleId,
                fn (): array => $this->loadRoleGrantsByEntity($roleId),
            );

            if (in_array($actionSlug, $actionSlugsByEntity[$entitySlug] ?? [], true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<array-key, array<int, mixed>> Action slugs keyed by entity slug
     */
    private function loadRoleGrantsByEntity(mixed $roleId): array
    {
        return PermissionModels::classFor('grant')::query()
            ->where('permission_role_id', $roleId)
            ->with(['permissionEntity', 'permissionAction'])
            ->get()
            ->groupBy('permissionEntity.slug')
            ->map(fn (Collection $grants): array => $grants->pluck('permissionAction.slug')->values()->all())
            ->all();
    }
}
