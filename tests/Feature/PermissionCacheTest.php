<?php

declare(strict_types=1);

use LaravelPermissions\LaravelPermissions\Models\PermissionAction;
use LaravelPermissions\LaravelPermissions\Models\PermissionEntity;
use LaravelPermissions\LaravelPermissions\Models\PermissionRole;
use LaravelPermissions\LaravelPermissions\Models\PermissionRoleGrant;
use Workbench\App\Models\User;

beforeEach(function (): void {
    PermissionEntity::create(['slug' => 'trial', 'name' => 'Trials']);
    PermissionAction::create(['slug' => 'view', 'name' => 'View']);
    PermissionAction::create(['slug' => 'edit', 'name' => 'Edit']);

    config()->set('laravel-permissions.cache.enabled', true);
});

function permissionCacheFixture(): array
{
    $role = PermissionRole::create(['slug' => 'editor', 'name' => 'Editor']);
    $role->grant('trial', 'view');

    $user = User::create(['name' => 'Test User', 'email' => 'cache@example.com', 'password' => 'secret']);
    $user->assignRole($role);

    return [$user, $role];
}

test('a direct grant delete is not seen while the cache is enabled', function (): void {
    [$user] = permissionCacheFixture();
    expect($user->hasPermissionTo('trial', 'view'))->toBeTrue();

    PermissionRoleGrant::query()->delete();

    expect($user->hasPermissionTo('trial', 'view'))->toBeTrue();
});

test('without cache.enabled a direct grant delete is seen immediately', function (): void {
    config()->set('laravel-permissions.cache.enabled', false);
    [$user] = permissionCacheFixture();
    expect($user->hasPermissionTo('trial', 'view'))->toBeTrue();

    PermissionRoleGrant::query()->delete();

    expect($user->hasPermissionTo('trial', 'view'))->toBeFalse();
});

test('revoke updates the cached answer', function (): void {
    [$user, $role] = permissionCacheFixture();
    expect($user->hasPermissionTo('trial', 'view'))->toBeTrue();

    $role->revoke('trial', 'view');

    expect($user->hasPermissionTo('trial', 'view'))->toBeFalse();
});

test('removeRole updates the cached answer', function (): void {
    [$user, $role] = permissionCacheFixture();
    expect($user->hasPermissionTo('trial', 'view'))->toBeTrue();

    $user->removeRole($role);

    expect($user->hasPermissionTo('trial', 'view'))->toBeFalse();
});

test('assignRole updates the cached answer', function (): void {
    [$user] = permissionCacheFixture();
    $user->removeRole('editor');
    expect($user->hasPermissionTo('trial', 'view'))->toBeFalse();

    $user->assignRole('editor');

    expect($user->hasPermissionTo('trial', 'view'))->toBeTrue();
});

test('syncGrants updates the cached answer', function (): void {
    [$user, $role] = permissionCacheFixture();
    expect($user->hasPermissionTo('trial', 'view'))->toBeTrue();

    $role->syncGrants([['trial', 'edit']]);

    expect($user->hasPermissionTo('trial', 'view'))->toBeFalse()
        ->and($user->hasPermissionTo('trial', 'edit'))->toBeTrue();
});

test('permissions:clear-cache drops every cached answer', function (): void {
    [$user] = permissionCacheFixture();
    expect($user->hasPermissionTo('trial', 'view'))->toBeTrue();
    PermissionRoleGrant::query()->delete();

    $this->artisan('permissions:clear-cache')->assertSuccessful();

    expect($user->hasPermissionTo('trial', 'view'))->toBeFalse();
});

test('cache:clear also drops every cached answer', function (): void {
    [$user] = permissionCacheFixture();
    expect($user->hasPermissionTo('trial', 'view'))->toBeTrue();
    PermissionRoleGrant::query()->delete();

    $this->artisan('cache:clear')->assertSuccessful();

    expect($user->hasPermissionTo('trial', 'view'))->toBeFalse();
});
