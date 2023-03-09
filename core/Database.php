<?php

/**
 * Std db class
 * @return database runner
*/

namespace app\core;

class Database {
    
    protected string $query  = '';
    protected string $where  = '';
    protected string $fields = '';
    protected array  $args   = [];
    protected string $placeholders = '';
    protected string $table;

    protected array $fieldPlaceholders = [];
    
    public const WHERE       = ' WHERE ';
    public const AND         = ' AND ';
    public const BIND        = ' = ? ';
    public const INNERJOIN   = ' INNER JOIN ';
    
    public const DEFAULT_LIMIT = 100;

    /**
     * Pdo instance 
     * @var Pdo;
    */
    public \Pdo $pdo;
    
    public function __construct(array $pdoConfigurations) {
        $this->pdo = new \Pdo($pdoConfigurations['dsn'], $pdoConfigurations['user'], $pdoConfigurations['password']);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function select(string $table, array $fields): Database {
        $this->table  = $table;
        $this->bindFields($fields);
        $this->query .= "SELECT {$this->fields} FROM {$this->table}";
        return $this;
    }

    /**
     * Init method for needing objects
     * @return Database
    */

    public function replaceWithPlaceholders(string $value): string {
        return '?';
    }

    public function init(string $table, array $fields, array $values): Database {
        $holders = '';
        foreach ( $values as $valKey => $value ) $holders .= '?' . ( array_key_last($values) === $valKey ? '' : ', ' );
        $this->tableName = $table;
        $this->fields = implode(', ', $fields);
        $this->args = $values;
        $this->placeholders = $holders;
        $this->create();
        $this->execute();
        return $this;
    }
    
    /**
     * @return binders for fields and arguments 
    */

    public function bindFields(array $fields): void {
        $this->fields = implode(', ', $fields);
    }
    
    public function bindValues(array $arguments): void {
        foreach($arguments as $selector => $value) {
            $this->query .= ( array_key_first($arguments) === $selector ? self::WHERE : self::AND ) . $selector . self::BIND;
            $this->args[] = $value;
        }
    }

    public function where(array $conditions): Database {
        $this->bindValues($conditions);
        return $this;
    }

    public function innerJoin(string $table, string $using): Database {
        $this->query .= self::INNERJOIN . " {$table} USING({$using}) ";
        return $this;
    }
    
    public function leftJoin(string $table, string $on, array $and = []): Database {
        $implodedAnd = (count($and) > 0 ? ' AND ' : '') . implode(' AND ', $and);
        $this->query .= " LEFT JOIN {$table} {$on} {$implodedAnd} ";
        return $this;
    }

    public function create(): Database {
        $this->query .= "INSERT INTO {$this->tableName} ({$this->fields}) VALUES ({$this->placeholders})";
        return $this;
    }

    public function patch(): Database {
        $this->query .= "UPDATE {$this->tableName} SET {$this->implodedFields} WHERE {$this->where}";
        return $this;
    }

    public function delete(string $table): Database {
        $this->query .= "DELETE FROM {$table} {$this->where}";
        return $this;
    }

    public function limit(int $limit = self::DEFAULT_LIMIT): Database {
        $this->query .= ' LIMIT ' . $limit;
        return $this;
    }

    public function execute(): array {
        try {
            $stmt = $this->prepare($this->query);
            $stmt->execute($this->args);
            $result = $stmt->fetchAll();
            $stmt = null;
            $this->resetQuery();
            return $result;
        } catch (\PDOException $e) {
            exit("[ SQL ERROR ] " . $e);
        }
    }

    public function groupBy(string $group): Database {
        $this->query .= ' GROUP BY ' . $group;
        return $this;
    }

    public function orderBy(string $order): Database {
        $this->query .= ' ORDER BY ' . $order;
        return $this;
    }

    public function resetQuery() {
        $this->type = '';
        $this->selector = '';
        $this->where = '';
        $this->implodedFields = '';
        $this->implodedArgs = '';
        $this->query = '';
        $this->fields = '';
        $this->args = [];
    }

    public function describe() {
        $this->query = "DESCRIBE {$this->tableName}";
        $this->execute();
    }

    public function createTable(string $tableName, array $fields) {
        $tableFields = implode(', ', $fields);
        $this->query = "CREATE TABLE {$this->exists} {$tableName} ({$tableFields})";
    }

    public function alterTable(string $oldColumn, string $newColumn) {
        $this->query = "ALTER TABLE {$this->tableName} CHANGE {$oldColumn} {$newColumn}";
    }

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
            $this->log('Applying new migration: ' . $className);
            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) $this->saveMigrations($newMigrations);
        else $this->log('Currently all migrations are applied.');
    }

    protected function saveMigrations(array $migrations) {
        $migrations = implode(',', array_map(fn($m) => "('$m')", $migrations));
        $stmt = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $migrations");
        $stmt->execute();
    }

    public function prepare(string $sql) {
        return $this->pdo->prepare($sql);
    }

    public function getAppliedMigrations() {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function createMigrationsTable() {
        $this->pdo->exec($this->sqlMigrationTable);
    }

    protected function log(string $message): void {
        echo date('Y-m-d H:i:s') . ' ' . $message . PHP_EOL;
    }

}