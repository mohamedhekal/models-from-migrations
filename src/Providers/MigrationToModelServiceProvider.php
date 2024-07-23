<?php
namespace Noouh\MigrationToModel\Providers;

use Illuminate\Support\ServiceProvider;
use Noouh\MigrationToModel\Commands\ConvertMigrationsCommand;

class MigrationToModelServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            ConvertMigrationsCommand::class,
        ]);
    }
}
