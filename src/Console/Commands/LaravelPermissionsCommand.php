<?php

declare(strict_types=1);

namespace LaravelPermissions\LaravelPermissions\Console\Commands;

use Illuminate\Console\Command;

class LaravelPermissionsCommand extends Command
{
    /**
     * The command signature.
     */
    protected $signature = 'laravel-permissions:placeholder';

    /**
     * The command description.
     */
    protected $description = 'Placeholder Artisan command shipped by the package laravel-permissions.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->line('LaravelPermissions placeholder command executed.');

        return self::SUCCESS;
    }
}
