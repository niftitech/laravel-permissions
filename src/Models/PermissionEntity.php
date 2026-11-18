<?php

declare(strict_types=1);

namespace LaravelPermissions\LaravelPermissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use LaravelPermissions\LaravelPermissions\PermissionModels;

/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class PermissionEntity extends Model
{
    /** @var list<string> */
    protected $fillable = ['slug', 'name'];

    /**
     * @return HasMany<Model, $this>
     */
    public function permissionRoleGrants(): HasMany
    {
        return $this->hasMany(PermissionModels::classFor('grant'));
    }
}
