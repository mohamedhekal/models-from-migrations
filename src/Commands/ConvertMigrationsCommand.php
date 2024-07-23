<?php
namespace Noouh\MigrationToModel\Commands;

use Illuminate\Console\Command;
use Noouh\MigrationToModel\Helpers\MigrationParser;

class ConvertMigrationsCommand extends Command
{
    protected $signature = 'migrations:convert {migration}';
    protected $description = 'Convert migration to model';

    public function handle()
    {
        $migration = $this->argument('migration');
        $parser = new MigrationParser();
        $model = $parser->parse($migration);

        $modelName = $parser->getModelName($migration);
        $path = app_path("Models/{$modelName}.php");
        file_put_contents($path, $model);

        $this->info("Model {$modelName} created successfully at {$path}");
    }
}
