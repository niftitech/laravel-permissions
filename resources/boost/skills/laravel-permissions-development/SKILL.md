---
name: laravel-permissions-development
description: >
  Install, configure, and use niftitech/laravel-permissions in Laravel apps:
  publish migrations and config, entity–action grants, roles, and hasPermissionTo checks.
license: MIT
metadata:
  author: Daniel Eckerström
---

# Laravel Permissions

Use this skill when a Laravel application integrates `niftitech/laravel-permissions` (Entity → Action authorization via roles and grants).

## Primary Goal

Apply the package public API in the smallest correct way: publish assets before migrate, configure the user model, wire the user trait, seed slugs, assign roles, and check permissions.

## Workflow

### 1. Install and publish (before migrate)

```bash
composer require niftitech/laravel-permissions
```

Publish config and migrations so the app can customize schema (for example JSON `name` columns) **before** the first migrate:

```bash
php artisan vendor:publish --provider="LaravelPermissions\LaravelPermissions\LaravelPermissionsServiceProvider"
```

Or publish by tag:

```bash
php artisan vendor:publish --tag=laravel-permissions-config
php artisan vendor:publish --tag=laravel-permissions-migrations
```

Publish tags:

- `laravel-permissions` — config and migrations together
- `laravel-permissions-config` — `config/laravel-permissions.php`
- `laravel-permissions-migrations` — permission tables migration (Laravel may stamp the filename with the publish date)

Package migrations are **not** auto-loaded from vendor; the app must publish (or copy) them.

### 2. Configure

In `config/laravel-permissions.php` (or merged defaults before publish), set:

- `models.user` — FQCN of the authenticatable user model (default `App\Models\User`)

Then migrate:

```bash
php artisan migrate
```

### 3. User model

Add `HasPermissionAuthorization` to the configured user model:

```php
use LaravelPermissions\LaravelPermissions\Concerns\HasPermissionAuthorization;

class User extends Authenticatable
{
    use HasPermissionAuthorization;
}
```

### 4. Data model (slugs)

Authorization resolves **slugs only**; `name` is for display.

- `PermissionEntity` — resource type (for example slug `trial`)
- `PermissionAction` — verb (for example slug `view`)
- `PermissionRole` — named role users receive
- `PermissionRoleGrant` — links a role to an entity + action pair
- Pivot `permission_role_user` — user ↔ role

Seed entities, actions, roles, and grants from app seeders, enums, or admin UI.

### 5. Assign roles and check access

```php
$user->permissionRoles()->attach($role);

$user->hasPermissionTo('trial', 'view'); // true when a grant exists on any of the user's roles
```

`hasPermissionTo` accepts string slugs or **string-backed enum cases** (case `value` is the slug).

## Rules, References, and Templates

- Package README: install order and usage examples
- Config key prefix: `laravel-permissions.*`
- Public models: `PermissionEntity`, `PermissionAction`, `PermissionRole`, `PermissionRoleGrant`
- User API: `permissionRoles()`, `hasPermissionTo($entity, $action)`

## Examples

**Grant editor role view on trials**

```php
$entity = PermissionEntity::create(['slug' => 'trial', 'name' => 'Trials']);
$action = PermissionAction::create(['slug' => 'view', 'name' => 'View']);
$role = PermissionRole::create(['slug' => 'editor', 'name' => 'Editor']);

PermissionRoleGrant::create([
    'permission_role_id' => $role->id,
    'permission_entity_id' => $entity->id,
    'permission_action_id' => $action->id,
]);
```

**Multi-locale labels:** edit the published migration to use `json('name')` on entity/action/role tables before migrate, or alter columns in a follow-up app migration; handle translations in app-layer models if needed.

**Upgrading the package:** `composer update niftitech/laravel-permissions`. If new migration files ship, publish migrations again only when you intend to change schema; avoid duplicate create migrations if tables already exist.

## Anti-patterns

- Running `php artisan migrate` before publishing migrations on a fresh install (tables will not be created).
- Expecting vendor migrations to run automatically without publish.
- Using `name` or display labels in authorization checks instead of stable slugs.
- Passing int-backed enums or non-backed enums to `hasPermissionTo` (string-backed only).
- Documenting package test helpers, workbench, or internal provider wiring as consumer API.
