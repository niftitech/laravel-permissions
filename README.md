# Laravel Permissions

Permissions with an Entity → Action style.

## Installation

```bash
composer require niftitech/laravel-permissions
```

Publish the migration and config **before** running migrations so you can adjust schema or settings (for example JSON `name` columns):

```bash
php artisan vendor:publish --provider="LaravelPermissions\LaravelPermissions\LaravelPermissionsServiceProvider"
```

Or publish individually:

```bash
php artisan vendor:publish --tag=laravel-permissions-config
php artisan vendor:publish --tag=laravel-permissions-migrations
```

Set `laravel-permissions.models.user` to your user model class if it is not `App\Models\User`.

To add traits such as translations or auditing, extend the package models in your app and point the matching key at your class: `models.role`, `models.entity`, `models.action`, and `models.grant`.

Then run migrations:

```bash
php artisan migrate
```

## Usage

Add the authorization trait to your user model:

```php
use LaravelPermissions\LaravelPermissions\Concerns\HasPermissionAuthorization;

class User extends Authenticatable
{
    use HasPermissionAuthorization;
}
```

Define entities and actions (stable slugs), create a role, and grant entity–action pairs on that role:

```php
use LaravelPermissions\LaravelPermissions\Models\PermissionAction;
use LaravelPermissions\LaravelPermissions\Models\PermissionEntity;
use LaravelPermissions\LaravelPermissions\Models\PermissionRole;
use LaravelPermissions\LaravelPermissions\Models\PermissionRoleGrant;

$entity = PermissionEntity::create(['slug' => 'trial', 'name' => 'Trials']);
$action = PermissionAction::create(['slug' => 'view', 'name' => 'View']);

$role = PermissionRole::create(['slug' => 'editor', 'name' => 'Editor']);

PermissionRoleGrant::create([
    'permission_role_id' => $role->id,
    'permission_entity_id' => $entity->id,
    'permission_action_id' => $action->id,
]);
```

Assign roles to users and check permissions:

```php
$user->assignRole($role);

$user->hasPermissionTo('trial', 'view'); // true

// String-backed enum cases are also accepted (case value used as slug):
$user->hasPermissionTo(TrialEntity::Trial, TrialAction::View);
```

Manage roles and grants with these methods. They accept a model, a slug, or a string-backed enum case:

```php
$user->assignRole('editor');          // add one role, keep the others
$user->removeRole('editor');          // remove one role
$user->syncRoles(['editor', 'viewer']); // replace all roles

$role->grant('trial', 'view');        // add one entity–action grant
$role->revoke('trial', 'view');       // remove one grant
$role->syncGrants([                   // replace all grants with these pairs
    ['trial', 'view'],
    ['trial', 'edit'],
]);
```

### Caching

Set `laravel-permissions.cache.enabled` to `true` to cache the lookups behind `hasPermissionTo()` in your default cache store. The helpers `assignRole()`, `removeRole()`, `syncRoles()`, `grant()`, `revoke()`, and `syncGrants()` refresh the cache when they change data. Direct writes such as `auditSync()`, `PermissionRoleGrant::create()`, or deleting a role model do not, so clear the cache after using them:

```bash
php artisan permissions:clear-cache
```

`php artisan cache:clear` also works.

Authorization uses **slugs only**; `name` is for display. Seed entities, actions, and roles from your app (enums, seeders, admin). For multi-locale labels, switch `name` to JSON in the published migration before the first migrate, or alter the column in your own migration, and handle translations in your application models.
