<?php

declare(strict_types=1);

namespace LaravelPermissions\LaravelPermissions;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

final class PermissionCache
{
    // Every key includes this token. Replacing it (permissions:clear-cache) makes all old keys unreachable without listing them.
    private const TOKEN_KEY = 'laravel-permissions.token';

    public static function enabled(): bool
    {
        return config('laravel-permissions.cache.enabled') === true;
    }

    /**
     * @param  Closure(): array<int, mixed>  $resolve
     * @return array<int, mixed>
     */
    public static function rememberUserRoleIds(mixed $userId, Closure $resolve): array
    {
        return Cache::rememberForever(self::key('user', $userId), $resolve);
    }

    /**
     * @param  Closure(): array<array-key, array<int, mixed>>  $resolve
     * @return array<array-key, array<int, mixed>>
     */
    public static function rememberRoleGrants(mixed $roleId, Closure $resolve): array
    {
        return Cache::rememberForever(self::key('role', $roleId), $resolve);
    }

    public static function forgetUser(mixed $userId): void
    {
        Cache::forget(self::key('user', $userId));
    }

    public static function forgetRole(mixed $roleId): void
    {
        Cache::forget(self::key('role', $roleId));
    }

    public static function clear(): void
    {
        Cache::forget(self::TOKEN_KEY);
    }

    private static function key(string $type, mixed $id): string
    {
        $token = Cache::rememberForever(self::TOKEN_KEY, fn (): string => Str::random(20));

        return "{$token}.{$type}.{$id}";
    }
}
