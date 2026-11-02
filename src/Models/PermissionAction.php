<?php

declare(strict_types=1);

namespace LaravelPermissions\LaravelPermissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class PermissionAction extends Model implements AuditableContract
{
    use Auditable;

    /** @var list<string> */
    protected $fillable = ['slug', 'name'];

    /**
     * @return HasMany<PermissionRoleGrant, $this>
     */
    public function permissionRoleGrants(): HasMany
    {
        return $this->hasMany(PermissionRoleGrant::class);
    }
}
