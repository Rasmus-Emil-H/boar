<?php

/**
 * Std db class
 * @return database runner
*/

namespace app\core;

class Database {

    /**
     * Pdo instance 
     * @var Pdo;
    */
    public \Pdo $pdo;

    /**
     * Migration table sql
     * table for migration so that we dont forking overwrite stuff
     * mkay?
     * @var sqlMigrationTable
    */
    protected string $sqlMigrationTable = 'CREATE TABLE IF NOT EXISTS Migrations (
        MigrationID int NOT NULL AUTO_INCREMENT,
        migration VARCHAR(255),
        created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (MigrationID)
    );';

    public function __construct(array $pdoConfigurations) {
        $this->pdo = new \Pdo($pdoConfigurations['dsn'], $pdoConfigurations['user'], $pdoConfigurations['password']);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations() {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();
        $files = scandir(Application::$ROOT_DIR.'/migrations');
        $toBeAppliedMigrations = array_diff($files, $appliedMigrations);
        $this->iterateMigrations($toBeAppliedMigrations);
    }

    public function iterateMigrations(array $toBeAppliedMigrations) {

        $newMigrations = [];

        foreach ($toBeAppliedMigrations as $migration) {
            if ($migration === '.' || $migration === '..') continue;
            require_once Application::$ROOT_DIR . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $currentMigration = new $className();
            $currentMigration->up();
            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) $this->saveMigrations($newMigrations);
        else echo 'Currently all migrations are applied.';
    }

    protected function saveMigrations(array $migrations) {
        $migrations = implode(',', array_map(fn($m) => "('$m')", $migrations));
        $stmt = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $migrations");
        $stmt->execute();
    }

    public function getAppliedMigrations() {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function createMigrationsTable() {
        $this->pdo->exec($this->sqlMigrationTable);
    }

}