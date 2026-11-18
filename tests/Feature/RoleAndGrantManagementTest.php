<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\ModelNotFoundException;
use LaravelPermissions\LaravelPermissions\Models\PermissionAction;
use LaravelPermissions\LaravelPermissions\Models\PermissionEntity;
use LaravelPermissions\LaravelPermissions\Models\PermissionRole;
use LaravelPermissions\LaravelPermissions\Models\PermissionRoleGrant;
use Workbench\App\Models\User;

enum TestRoleSlug: string
{
    case Editor = 'editor';
}

class TestCustomRole extends PermissionRole
{
    protected $table = 'permission_roles';
}

function createUser(string $email): User
{
    return User::create([
        'name' => 'Test User',
        'email' => $email,
        'password' => 'secret',
    ]);
}

test('assignRole accepts a model, slug, or enum and never adds the same role twice', function (): void {
    $role = PermissionRole::create(['slug' => 'editor', 'name' => 'Editor']);
    $user = createUser('assign@example.com');

    $user->assignRole($role);
    $user->assignRole('editor');
    $user->assignRole(TestRoleSlug::Editor);

    expect($user->permissionRoles()->pluck('slug')->all())->toBe(['editor']);
});

test('assignRole keeps the roles the user already has', function (): void {
    $editor = PermissionRole::create(['slug' => 'editor', 'name' => 'Editor']);
    $viewer = PermissionRole::create(['slug' => 'viewer', 'name' => 'Viewer']);
    $user = createUser('keep@example.com');

    $user->assignRole($editor);
    $user->assignRole($viewer);

    expect($user->permissionRoles()->pluck('slug')->sort()->values()->all())->toBe(['editor', 'viewer']);
});

test('removeRole detaches only the given role', function (): void {
    $editor = PermissionRole::create(['slug' => 'editor', 'name' => 'Editor']);
    $viewer = PermissionRole::create(['slug' => 'viewer', 'name' => 'Viewer']);
    $user = createUser('remove@example.com');
    $user->syncRoles(['editor', 'viewer']);

    $user->removeRole('editor');

    expect($user->permissionRoles()->pluck('slug')->all())->toBe(['viewer']);
});

test('syncRoles replaces the user roles with the given set', function (): void {
    PermissionRole::create(['slug' => 'editor', 'name' => 'Editor']);
    PermissionRole::create(['slug' => 'viewer', 'name' => 'Viewer']);
    PermissionRole::create(['slug' => 'admin', 'name' => 'Admin']);
    $user = createUser('sync@example.com');
    $user->syncRoles(['editor', 'viewer']);

    $user->syncRoles([TestRoleSlug::Editor, 'admin']);

    expect($user->permissionRoles()->pluck('slug')->sort()->values()->all())->toBe(['admin', 'editor']);
});

test('a role granted through grant authorizes users with that role', function (): void {
    PermissionEntity::create(['slug' => 'trial', 'name' => 'Trials']);
    PermissionAction::create(['slug' => 'view', 'name' => 'View']);
    $role = PermissionRole::create(['slug' => 'editor', 'name' => 'Editor']);
    $user = createUser('grant@example.com');
    $user->assignRole($role);

    $role->grant('trial', 'view');
    $role->grant('trial', 'view');

    expect($role->grants()->count())->toBe(1)
        ->and($user->hasPermissionTo('trial', 'view'))->toBeTrue();
});

test('revoke removes a grant from the role', function (): void {
    PermissionEntity::create(['slug' => 'trial', 'name' => 'Trials']);
    PermissionAction::create(['slug' => 'view', 'name' => 'View']);
    $role = PermissionRole::create(['slug' => 'editor', 'name' => 'Editor']);
    $user = createUser('revoke@example.com');
    $user->assignRole($role);
    $role->grant('trial', 'view');

    $role->revoke('trial', 'view');

    expect($role->grants()->count())->toBe(0)
        ->and($user->hasPermissionTo('trial', 'view'))->toBeFalse();
});

test('syncGrants replaces every grant on the role', function (): void {
    PermissionEntity::create(['slug' => 'trial', 'name' => 'Trials']);
    PermissionAction::create(['slug' => 'view', 'name' => 'View']);
    PermissionAction::create(['slug' => 'edit', 'name' => 'Edit']);
    $role = PermissionRole::create(['slug' => 'editor', 'name' => 'Editor']);
    $role->grant('trial', 'view');

    $role->syncGrants([['trial', 'edit']]);

    expect(PermissionRoleGrant::query()->where('permission_role_id', $role->id)->count())->toBe(1)
        ->and($role->grants()->first()->permissionAction->slug)->toBe('edit');
});

test('the role model class can be overridden in config', function (): void {
    config()->set('laravel-permissions.models.role', TestCustomRole::class);
    $role = TestCustomRole::create(['slug' => 'editor', 'name' => 'Editor']);
    $user = createUser('override@example.com');

    $user->assignRole($role);

    expect($user->permissionRoles()->first())->toBeInstanceOf(TestCustomRole::class);
});

test('assigning an unknown role slug fails', function (): void {
    $user = createUser('unknown@example.com');

    $user->assignRole('missing');
})->throws(ModelNotFoundException::class);
