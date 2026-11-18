<?php

declare(strict_types=1);

namespace LaravelPermissions\LaravelPermissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use LaravelPermissions\LaravelPermissions\PermissionModels;

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
class PermissionRoleGrant extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'permission_role_id',
        'permission_entity_id',
        'permission_action_id',
    ];

    /**
     * @return BelongsTo<Model, $this>
     */
    public function permissionRole(): BelongsTo
    {
        return $this->belongsTo(PermissionModels::classFor('role'));
    }

    /**
     * @return BelongsTo<Model, $this>
     */
    public function permissionEntity(): BelongsTo
    {
        return $this->belongsTo(PermissionModels::classFor('entity'));
    }

    /**
     * @return BelongsTo<Model, $this>
     */
    public function permissionAction(): BelongsTo
    {
        return $this->belongsTo(PermissionModels::classFor('action'));
    }
}
