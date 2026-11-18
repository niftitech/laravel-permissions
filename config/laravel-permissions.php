<?php

declare(strict_types=1);

use LaravelPermissions\LaravelPermissions\Models\PermissionAction;
use LaravelPermissions\LaravelPermissions\Models\PermissionEntity;
use LaravelPermissions\LaravelPermissions\Models\PermissionRole;
use LaravelPermissions\LaravelPermissions\Models\PermissionRoleGrant;

return [

    'models' => [
        'user' => 'App\Models\User',
        // Extend these in your app to add traits such as translations or auditing.
        'role' => PermissionRole::class,
        'entity' => PermissionEntity::class,
        'action' => PermissionAction::class,
        'grant' => PermissionRoleGrant::class,
    ],

    // Caches role and grant lookups for hasPermissionTo(). Only the helpers (assignRole, removeRole,
    // syncRoles, grant, revoke, syncGrants) keep it current; direct model or pivot writes do not.
    'cache' => [
        'enabled' => false,
    ],
];
