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
];
