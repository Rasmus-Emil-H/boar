<?php

namespace app\core\src\console\cmds;

class CreateEntity {

    protected array $entityTypes = ['controller', 'model', 'migration', 'view'];

    public function run(array $args): void {
        $entityName = $args[1];
        
        $this->checkEntityExistence($entityName);

        echo "Creating entity: $entityName\n";

        foreach ($this->entityTypes as $type) {
            $method = "create" . ucfirst($type);
            if (method_exists($this, $method)) $this->$method(ucfirst($entityName));
        }
    }

    private function checkEntityExistence(string $entityName): void {
        $filename = "models/{$entityName}Model.php";
        if (!file_exists($filename)) return;

        exit('Entity already exists - Aborting operation');
    }

    protected function createController(string $name): void {
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

    protected function createView(string $name): void {
        $controllerTemplate = <<<EOT
        Im a template file for $name!
        EOT;

        $filename = "views/{$name}.tpl.php";
        file_put_contents($filename, $controllerTemplate);
        echo "Created view: $filename\n";
    }

    protected function createModel(string $name): void {
        $modelTemplate = <<<EOT
        <?php

        namespace app\models;

        use \app\core\src\database\Entity;

        final class {$name}Model extends Entity {

            public function getTableName(): string {
                return '{$name}';
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

    private function formatMigrationName(string $name): string {
        return 'add_'.strtolower($name).'_table_'.date('Y_m_d', strtotime('now')).'_0001';
    }

    protected function createMigration(string $name): void {
        $migrationName = $this->formatMigrationName($name);
        $tableNamespace = 'use \app\core\src\database\table\Table';

        $migrationTemplate = <<<EOT
        <?php

        /**
        |----------------------------------------------------------------------------
        | Automatically created migration
        |----------------------------------------------------------------------------
        |
        | Adjust table specifications to your needs
        |
        */

        $tableNamespace;
        use \app\core\src\database\Schema;

        class $migrationName {
            public function up() {
                (new Schema())->up('$name', function(Table \$table) {
                    \$table->increments('{$name}ID');
                    \$table->timestamp();
                    \$table->primaryKey('{$name}ID');
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