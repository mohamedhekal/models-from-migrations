
<?php
namespace Noouh\MigrationToModel\Commands;

use Illuminate\Console\Command;
use Noouh\MigrationToModel\Helpers\MigrationParser;
use Illuminate\Support\Facades\File;

class ConvertMigrationsCommand extends Command
{
    protected $signature = 'migrations:convert {migration?}';
    protected $description = 'Convert migration to model';

    public function handle()
    {
        $migration = $this->argument('migration');

        if ($migration) {
            $this->convertMigration($migration);
        } else {
            $this->convertAllMigrations();
        }
    }

    protected function convertMigration($migration)
    {
        $parser = new MigrationParser();
        $model = $parser->parse($migration);

        $modelName = $parser->getModelName($migration);
        $path = app_path("Models/{$modelName}.php");
        file_put_contents($path, $model);

        $this->info("Model {$modelName} created successfully at {$path}");
    }

    protected function convertAllMigrations()
    {
        $migrationsPath = database_path('migrations');
        $migrationFiles = File::files($migrationsPath);

        foreach ($migrationFiles as $migrationFile) {
            $migrationName = pathinfo($migrationFile, PATHINFO_FILENAME);
            $tableName = $this->extractTableName($migrationName);
            $this->convertMigration($tableName);
        }

        $this->info('All models created successfully.');
    }

    protected function extractTableName($migrationName)
    {
        preg_match('/create_(.*)_table/', $migrationName, $matches);
        return $matches[1] ?? $migrationName;
    }
}
