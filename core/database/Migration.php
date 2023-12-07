<?php

namespace app\core\database;

use \app\models\MigrationModel;
use \app\core\database\seeders\DatabaseSeeder;

class Migration {
    protected const MAX_LENGTH = 255;
    protected const MIGRATION_DIR = '/migrations/';

    public function getAppliedMigrations(): array {
        return app()
            ->connection
            ->rawSQL("SELECT migration FROM Migrations")
            ->execute();
    }

    public function createMigrationsTable() {
        (new Schema())->up('Migrations', function(table\Table $table) {
            $table->increments('MigrationID');
            $table->varchar('migration', self::MAX_LENGTH);
            $table->timestamp();
            $table->primaryKey('MigrationID');
        });
    }

    public function applyMigrations() {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();
        $migrationsFolder = app()::$ROOT_DIR . self::MIGRATION_DIR;
        $migrations = scandir($migrationsFolder);
        $mappedMigrations = array_map(fn($object) => $object->migration, $appliedMigrations);
        $missingMigrations = [];
        foreach ( $migrations as $migration ) {
            $migrationFile = $migrationsFolder . $migration;
            if ($migration === '.' || $migration === '..') continue;
            $actualMigration = str_replace('.php', '', $migration);
            if (!is_file($migrationFile) || in_array($actualMigration, $mappedMigrations)) continue;
            $date = preg_replace('/\_/', '-', substr(substr($migration, -19), 0, 10));
            if (!strtotime($date)) app()->connection->log("Invalid migration name ($migration), must be formatted: migration_yyyy_mm_dd_xxxx", true);
            isset($missingMigrations[strtotime($date)]) ? $missingMigrations[strtotime($date)+1] = $migration : $missingMigrations[strtotime($date)] = $migration;
        }
        ksort($missingMigrations);
        $this->iterateMigrations($missingMigrations);
    }

    public function iterateMigrations(array $toBeAppliedMigrations): void {
        foreach ($toBeAppliedMigrations as $migration) {
            require_once app()::$ROOT_DIR . self::MIGRATION_DIR . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            if (strlen($className) > self::MAX_LENGTH) app()->connection->log("Classname ($className) is too long!", true);
            app()->classCheck($className);
            $currentMigration = new $className();
            $currentMigration->up();
            (new MigrationModel())
                ->set(['migration' => $className])
                ->save();
            app()
                ->connection
                ->log('Successfully applied new migration: ' . $className);
        }

        app()
            ->connection
            ->log("Done");
    }

    public function initialApplicationSeed() {
       (new DatabaseSeeder())->up('Language', ['Name' => 'English', 'Code' => 'en'], 1); 
    }

}