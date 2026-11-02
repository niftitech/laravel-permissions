<div align="center">
    <h1>Laravel Permissions</h1>
</div>

<p align="center">
    <a href="https://packagist.org/packages/daniel-eckerstr-m/laravel-permissions"><img src="https://img.shields.io/packagist/v/daniel-eckerstr-m/laravel-permissions.svg?style=flat-square" alt="Packagist"></a>
    <a href="https://packagist.org/packages/daniel-eckerstr-m/laravel-permissions"><img src="https://img.shields.io/packagist/php-v/daniel-eckerstr-m/laravel-permissions.svg?style=flat-square" alt="PHP from Packagist"></a>
    <a href="https://packagist.org/packages/daniel-eckerstr-m/laravel-permissions"><img src="https://badge.laravel.cloud/badge/daniel-eckerstr-m/laravel-permissions?style=flat" alt="Laravel versions"></a>
    <a href="https://github.com/daniel-eckerstr-m/laravel-permissions/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/actions/workflow/status/daniel-eckerstr-m/laravel-permissions/tests.yml?branch=main&label=Tests&style=flat-square"></a>
    <a href="https://packagist.org/packages/daniel-eckerstr-m/laravel-permissions"><img src="https://img.shields.io/packagist/dt/daniel-eckerstr-m/laravel-permissions.svg?style=flat-square" alt="Total Downloads"></a>
</p>

Permissions with a Entity -> Action style

## Installation

You can install the package via Composer:

```bash
composer require daniel-eckerstr-m/laravel-permissions
```

You may publish all of the package's resources at once:

```bash
php artisan vendor:publish --tag="laravel-permissions"
```

Or, you may publish each resource individually:

### Publishing the Configuration File

```bash
php artisan vendor:publish --tag="laravel-permissions-config"
```

### Publishing and Running the Migrations

```bash
php artisan vendor:publish --tag="laravel-permissions-migrations"
php artisan migrate
```

### Publishing the Translations

```bash
php artisan vendor:publish --tag="laravel-permissions-lang"
```

### Publishing the Public Assets

```bash
php artisan vendor:publish --tag="laravel-permissions-assets"
```

## Usage

<!-- Add a basic usage example here. -->

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Thank you for considering contributing to Laravel Permissions! Please review our [contributing guide](.github/CONTRIBUTING.md) to get started.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Daniel Eckerström](https://github.com/daniel-eckerstr-m)
- [All Contributors](../../contributors)

## License

Laravel Permissions is open-sourced software licensed under the [MIT license](LICENSE.md).
