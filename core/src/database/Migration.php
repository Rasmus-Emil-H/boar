<?php

namespace app\core\src\database;

use \app\models\MigrationModel;
use \app\core\src\database\seeders\DatabaseSeeder;
use \app\core\src\factories\MigrationFactory;
use \app\core\src\miscellaneous\CoreFunctions;

class Migration {
    
    protected const MIGRATION_DIR = '/migrations/';

    public function getAppliedMigrations(): array {
        return MigrationModel::all();
    }

    public function createMigrationsTable() {
        (new Schema())->up('Migrations', function(table\Table $table) {
            $table->increments('MigrationID');
            $table->varchar('Migration', Connection::MAX_COLUMN_LENGTH);
            $table->timestamp();
            $table->primaryKey('MigrationID');
        });
    }

    public function applyMigrations() {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();
        $migrationsFolder = CoreFunctions::app()::$ROOT_DIR . self::MIGRATION_DIR;
        $migrations = scandir($migrationsFolder);
        $mappedMigrations = array_map(fn($object) => $object->Migration, $appliedMigrations);
        $missingMigrations = [];
        foreach ( $migrations as $migration ) {
            $migrationFile = $migrationsFolder . $migration;
            $actualMigration = str_replace('.php', '', $migration);
            if (!is_file($migrationFile) || in_array($actualMigration, $mappedMigrations)) continue;
            $date = preg_replace('/\_/', '-', substr(substr($migration, -19), 0, 10));
            if (!strtotime($date)) CoreFunctions::app()->log("Invalid migration name ($migration), must be formatted: migration_yyyy_mm_dd_xxxx", true);
            isset($missingMigrations[strtotime($date)]) ? $missingMigrations[strtotime($date)+1] = $migration : $missingMigrations[strtotime($date)] = $migration;
        }
        ksort($missingMigrations);
        $this->iterateMigrations($missingMigrations);
    }

    public function iterateMigrations(array $toBeAppliedMigrations): void {
        $app = CoreFunctions::app();
        foreach ($toBeAppliedMigrations as $migration) {
            require_once $app::$ROOT_DIR . self::MIGRATION_DIR . $migration;
            $handler = pathinfo($migration, PATHINFO_FILENAME);
            if (strlen($handler) > Connection::MAX_COLUMN_LENGTH) $app->log("Classname ($handler) is too long!", exit: true);
            $app->classCheck($className);
            (new MigrationFactory(['handler' => $handler]))->create()->up();
            (new MigrationModel())->set(['Migration' => $handler])->save();
            $app->log('Successfully applied new migration: ' . $handler);
        }

        $app->log("Done");
    }

    public function seedLanguage() {
       (new DatabaseSeeder())->up('Language', ['Name' => 'English', 'Code' => 'en'], 1); 
    }

}