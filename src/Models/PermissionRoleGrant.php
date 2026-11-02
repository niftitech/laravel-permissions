<?php

declare(strict_types=1);

namespace LaravelPermissions\LaravelPermissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * Entity + action pair granted to a role (no separate permissions table).
 *
 * @property int $id
 * @property int $permission_role_id
 * @property int $permission_entity_id
 * @property int $permission_action_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class PermissionRoleGrant extends Model implements AuditableContract
{
    use Auditable;

    /** @var list<string> */
    protected $fillable = [
        'permission_role_id',
        'permission_entity_id',
        'permission_action_id',
    ];

    /**
     * @return BelongsTo<PermissionRole, $this>
     */
    public function permissionRole(): BelongsTo
    {
        return $this->belongsTo(PermissionRole::class);
    }

    /**
     * @return BelongsTo<PermissionEntity, $this>
     */
    public function permissionEntity(): BelongsTo
    {
        return $this->belongsTo(PermissionEntity::class);
    }

    /**
     * @return BelongsTo<PermissionAction, $this>
     */
    public function permissionAction(): BelongsTo
    {
        return $this->belongsTo(PermissionAction::class);
    }
}
