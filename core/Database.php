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
        $toBeAppliedMigrations = array_diff($appliedMigrations, $files);
        var_dump($toBeAppliedMigrations);
        $this->iterateMigrations($toBeAppliedMigrations);
    }

    public function iterateMigrations(array $toBeAppliedMigrations) {
        foreach ($toBeAppliedMigrations as $migration) {
            if ($migration === '.' || $migration === '..') continue;
            require_once Application::$ROOT_DIR . '/migrations/' . $migration . '.php';
            $className = pathinfo($migration, PATHINFO_FILENAME);
        }
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