# Release Notes

## [Unreleased](https://github.com/niftitech/laravel-permissions/compare/v0.1.0...1.x)

### Added

- `assignRole`, `removeRole`, and `syncRoles` on users, and `grant`, `revoke`, and `syncGrants` on roles.

### Changed

- Package migrations are no longer auto-loaded from vendor. Publish them with the `laravel-permissions-migrations` tag (or the service provider) before running `php artisan migrate`.
- Package models read their classes from `laravel-permissions.models.role`, `entity`, `action`, and `grant`, so apps can extend them. Republish the config to get these keys.

### Removed

- `owen-it/laravel-auditing` and the `Auditable` trait on the package models. Add auditing in your app by extending the models and configuring them as above.

## [v0.1.0](https://github.com/niftitech/laravel-permissions/compare/...v0.1.0) - 202x-xx-xx

Initial pre-release.
