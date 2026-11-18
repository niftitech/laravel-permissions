<?php

declare(strict_types=1);

namespace LaravelPermissions\LaravelPermissions\Console\Commands;

use Illuminate\Console\Command;
use LaravelPermissions\LaravelPermissions\PermissionCache;

class ClearPermissionCacheCommand extends Command
{
    protected $signature = 'permissions:clear-cache';

    protected $description = 'Clear the cached role and grant lookups used by laravel-permissions';

    public function handle(): int
    {
        PermissionCache::clear();

        $this->components->info('Permission cache cleared.');

        return self::SUCCESS;
    }
}
