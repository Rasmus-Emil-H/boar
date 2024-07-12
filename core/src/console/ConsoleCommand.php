<?php

namespace app\core\src\console;

class ConsoleCommand
{
    protected $entityTypes = ['controller', 'model', 'migration', 'view'];

    public function run($argv) {
        if (count($argv) < 3) {
            $this->printUsage();
            return;
        }

        $command = $argv[1];
        $entityName = $argv[2];

        switch ($command) {
            case 'create-entity':
                $this->createEntity($entityName);
                break;
            default:
                echo "Unknown command: $command\n";
                $this->printUsage();
        }
    }

    protected function printUsage() {
        echo "Usage: php boar create-entity <entity-type> <entity-name>\n";
        echo "Entity types: " . implode(', ', $this->entityTypes) . "\n";
    }

    protected function createEntity($entityName) {
        echo "Creating entity: $entityName\n";

        foreach ($this->entityTypes as $type) {
            $method = "create" . ucfirst($type);
            if (method_exists($this, $method)) $this->$method(ucfirst($entityName));
        }
    }

    protected function createController($name) {
        $controllerTemplate = <<<EOT
        <?php

        namespace app\controllers;

        use \app\core\src\Controller;

        final class {$name}Controller extends Controller {

            public function index() {
                return \$this->setFrontendTemplateAndData('$name', []);
            }

        }
        EOT;

        $filename = "controllers/{$name}Controller.php";
        file_put_contents($filename, $controllerTemplate);
        echo "Created controller: $filename\n";
    }

    protected function createView($name) {
        $controllerTemplate = <<<EOT
        Im a template file for $name!
        EOT;

        $filename = "views/{$name}.tpl.php";
        file_put_contents($filename, $controllerTemplate);
        echo "Created view: $filename\n";
    }

    protected function createModel($name) {
        $modelTemplate = <<<EOT
        <?php

        namespace app\models;

        use \app\core\src\database\Entity;

        final class {$name}Model extends Entity {

            public function getTableName(): string {
                return '{$name}Table';
            }
                
            public function getKeyField(): string {
                return '{$name}ID';
            }
            
        }
        EOT;

        $filename = "models/{$name}Model.php";
        file_put_contents($filename, $modelTemplate);
        echo "Created model: $filename\n";
    }

    protected function createMigration($name) {
        $migrationName = 'add_'.$name.'_table_'.date('Y_m_d', strtotime('now')).'_0001';
        $tableNamespace = 'use \app\core\src\database\table\Table';

        $migrationTemplate = <<<EOT
        <?php

        $tableNamespace;
        use \app\core\src\database\Schema;

        class $migrationName {
            public function up() {
                (new Schema())->up('$name', function(Table \$table) {
                    \$table->increments('YourID');
                    \$table->timestamp();
                    \$table->primaryKey('YourID');
                });
            }

            public function down() {
                (new Schema())->down('$name');
            }
        }
        EOT;

        $filename = "migrations/$migrationName.php";
        file_put_contents($filename, $migrationTemplate);

        echo "Created migration: $filename\n";
    }
}
