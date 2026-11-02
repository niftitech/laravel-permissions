# Laravel Permissions

Permissions with an Entity → Action style.

## Installation

```bash
composer require niftitech/laravel-permissions
php artisan migrate
```

Publish configuration (optional):

```bash
php artisan vendor:publish --tag=laravel-permissions-config
```

Set `laravel-permissions.models.user` to your user model class if it is not `App\Models\User`.

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
$user->permissionRoles()->attach($role);

$user->hasPermissionTo('trial', 'view'); // true

// String-backed enum cases are also accepted (case value used as slug):
$user->hasPermissionTo(TrialEntity::Trial, TrialAction::View);
```

Authorization uses **slugs only**; `name` is for display. Seed entities, actions, and roles from your app (enums, seeders, admin). For multi-locale labels, switch `name` to JSON in the package migration before the first migrate, or alter the column in your own migration, and handle translations in your application models.
