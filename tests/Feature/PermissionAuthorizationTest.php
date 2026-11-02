<?php

declare(strict_types=1);

use LaravelPermissions\LaravelPermissions\Models\PermissionAction;
use LaravelPermissions\LaravelPermissions\Models\PermissionEntity;
use LaravelPermissions\LaravelPermissions\Models\PermissionRole;
use LaravelPermissions\LaravelPermissions\Models\PermissionRoleGrant;
use Workbench\App\Models\User;

test('a user with a matching grant is authorized', function (): void {
    $entity = PermissionEntity::create([
        'slug' => 'trial',
        'name' => 'Trials',
    ]);

    $action = PermissionAction::create([
        'slug' => 'view',
        'name' => 'View',
    ]);

    $role = PermissionRole::create([
        'slug' => 'test',
        'name' => 'Test',
    ]);

    PermissionRoleGrant::create([
        'permission_role_id' => $role->id,
        'permission_entity_id' => $entity->id,
        'permission_action_id' => $action->id,
    ]);

    $user = User::create([
        'name' => 'Test User',
        'email' => 'authorized@example.com',
        'password' => 'secret',
    ]);
    $user->permissionRoles()->attach($role);

    expect($user->hasPermissionTo('trial', 'view'))->toBeTrue();
});

test('a user without the role is not authorized', function (): void {
    PermissionEntity::create(['slug' => 'trial', 'name' => 'Trials']);
    PermissionAction::create(['slug' => 'view', 'name' => 'View']);

    $user = User::create([
        'name' => 'Test User',
        'email' => 'unauthorized@example.com',
        'password' => 'secret',
    ]);

    expect($user->hasPermissionTo('trial', 'view'))->toBeFalse();
});
