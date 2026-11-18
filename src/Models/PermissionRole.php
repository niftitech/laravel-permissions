<?php

declare(strict_types=1);

namespace LaravelPermissions\LaravelPermissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use LaravelPermissions\LaravelPermissions\Concerns\ResolvesPermissionModels;
use LaravelPermissions\LaravelPermissions\PermissionCache;
use LaravelPermissions\LaravelPermissions\PermissionModels;

/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class PermissionRole extends Model
{
    use ResolvesPermissionModels;

    /** @var list<string> */
    protected $fillable = ['slug', 'name'];

    /**
     * @return HasMany<Model, $this>
     */
    public function grants(): HasMany
    {
        return $this->hasMany(PermissionModels::classFor('grant'));
    }

    /**
     * @return BelongsToMany<Model, $this>
     */
    public function users(): BelongsToMany
    {
        /** @var class-string<Model> $userModel */
        $userModel = config('laravel-permissions.models.user');

        return $this->belongsToMany($userModel, 'permission_role_user');
    }

    /**
     * Grants an entity–action pair to this role. Granting the same pair twice keeps one grant.
     *
     * @param  mixed  $entity  PermissionEntity, entity slug, or string-backed enum case whose value is the slug
     * @param  mixed  $action  PermissionAction, action slug, or string-backed enum case whose value is the slug
     */
    public function grant(mixed $entity, mixed $action): void
    {
        $this->grants()->firstOrCreate($this->grantAttributes($entity, $action));

        PermissionCache::forgetRole($this->getKey());
    }

    /**
     * @param  mixed  $entity  PermissionEntity, entity slug, or string-backed enum case whose value is the slug
     * @param  mixed  $action  PermissionAction, action slug, or string-backed enum case whose value is the slug
     */
    public function revoke(mixed $entity, mixed $action): void
    {
        $this->grants()->where($this->grantAttributes($entity, $action))->delete();

        PermissionCache::forgetRole($this->getKey());
    }

    /**
     * Replaces every grant on this role with the given entity–action pairs.
     * Grant rows are deleted and recreated rather than diffed, so their ids change.
     *
     * @param  iterable<int, array{mixed, mixed}>  $grants  Each item is [entity, action]
     */
    public function syncGrants(iterable $grants): void
    {
        $this->getConnection()->transaction(function () use ($grants): void {
            $this->grants()->delete();

            foreach ($grants as [$entity, $action]) {
                $this->grant($entity, $action);
            }
        });

        // Forget again after commit: a read during the transaction could cache the old grants.
        PermissionCache::forgetRole($this->getKey());
    }

    /**
     * @return array{permission_entity_id: mixed, permission_action_id: mixed}
     */
    private function grantAttributes(mixed $entity, mixed $action): array
    {
        return [
            'permission_entity_id' => $this->resolvePermissionModel(PermissionModels::classFor('entity'), $entity, 'entity')->getKey(),
            'permission_action_id' => $this->resolvePermissionModel(PermissionModels::classFor('action'), $action, 'action')->getKey(),
        ];
    }
}
