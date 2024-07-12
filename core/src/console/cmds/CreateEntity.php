<?php

namespace app\core\src\console\cmds;

class CreateEntity {

    protected $entityTypes = ['controller', 'model', 'migration', 'view'];

    public function createEntity(string $entityName) {
        $this->checkEntityExistence($entityName);

        echo "Creating entity: $entityName\n";

        foreach ($this->entityTypes as $type) {
            $method = "create" . ucfirst($type);
            if (method_exists($this, $method)) $this->$method(ucfirst($entityName));
        }
    }

    private function checkEntityExistence(string $entityName) {
        $filename = "models/{$entityName}Model.php";
        if (!file_exists($filename)) return;

        exit('Entity already exists - Aborting operation');
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

    protected function createMigration($name) {
        $migrationName = 'add_'.strtolower($name).'_table_'.date('Y_m_d', strtotime('now')).'_0001';
        $tableNamespace = 'use \app\core\src\database\table\Table';

        $migrationTemplate = <<<EOT
        <?php

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