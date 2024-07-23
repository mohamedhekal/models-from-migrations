
<?php
namespace Noouh\MigrationToModel\Helpers;

class MigrationParser
{
    public function parse($migration)
    {
        $path = database_path("migrations/*_create_{$migration}_table.php");
        $files = glob($path);
        if (empty($files)) {
            throw new \Exception("Migration file does not exist: {$path}");
        }

        $content = file_get_contents($files[0]);
        $fields = $this->extractFields($content);
        $relations = $this->extractRelations($content);

        $model = $this->generateModel($migration, $fields, $relations);
        return $model;
    }

    protected function extractFields($content)
    {
        preg_match_all('/\$table->(.*?)\(\'(.*?)\'(,.*?)*\);/', $content, $matches);
        $fields = [];
        foreach ($matches[2] as $key => $field) {
            $fields[] = $field;
        }
        return $fields;
    }

    protected function extractRelations($content)
    {
        preg_match_all('/\$table->foreign\(\'(.*?)\'\)->references\(\'(.*?)\'\)->on\(\'(.*?)\'\);/', $content, $matches);
        $relations = [];
        foreach ($matches[1] as $key => $foreignKey) {
            $relations[] = [
                'foreignKey' => $foreignKey,
                'references' => $matches[2][$key],
                'on' => $matches[3][$key]
            ];
        }
        return $relations;
    }

    protected function generateModel($migration, $fields, $relations)
    {
        $modelName = ucwords(str_replace('_', ' ', $migration));
        $modelName = str_replace(' ', '', $modelName);
        $fillable = implode("', '", $fields);
        $relationMethods = $this->generateRelationMethods($relations);

        return <<<EOD
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$modelName} extends Model
{
    use HasFactory;

    protected \$table = '{$migration}';
    protected \$fillable = ['{$fillable}'];

    {$relationMethods}
}
EOD;
    }

    protected function generateRelationMethods($relations)
    {
        $methods = '';
        foreach ($relations as $relation) {
            $methodName = $this->generateMethodName($relation['foreignKey']);
            $methods .= <<<EOD

    public function {$methodName}()
    {
        return \$this->belongsTo({$this->getRelatedModel($relation['on'])}::class, '{$relation['foreignKey']}', '{$relation['references']}');
    }
EOD;
        }
        return $methods;
    }

    protected function generateMethodName($foreignKey)
    {
        return lcfirst(str_replace('_id', '', ucwords($foreignKey, '_')));
    }

    protected function getRelatedModel($tableName)
    {
        return ucwords(str_replace('_', ' ', $tableName));
    }
}
