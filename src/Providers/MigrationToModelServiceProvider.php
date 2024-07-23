<?php
namespace YourVendor\MigrationToModel\Providers;

use Illuminate\Support\ServiceProvider;
use YourVendor\MigrationToModel\Commands\ConvertMigrationsCommand;

class MigrationToModelServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            ConvertMigrationsCommand::class,
        ]);
    }
}
