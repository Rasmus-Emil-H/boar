<?php

/**
 * Std db class
 * @return database runner
*/

namespace app\core\database;

#[\AllowDynamicProperties]

class Connection {

    private bool $transactionStarted = false;

    protected const MAX_LENGTH = 255;
    
    protected string $query  = '';
    protected string $where  = '';
    protected string $fields = '';
    protected string $placeholders = '';
    protected string $table;

    protected array $fieldPlaceholders = [];
    protected array  $args = [];
    private   array $defaultPdoOptions = [
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
        \PDO::ATTR_EMULATE_PREPARES => false,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    ];
    
    public const WHERE       = ' WHERE ';
    public const AND         = ' AND ';
    public const BIND        = ' = :';
    public const INNERJOIN   = ' INNER JOIN ';
    public const DEFAULT_LIMIT = 100;

    /**
     * Pdo instance 
     * @var Pdo;
    */
    public \Pdo $pdo;
    
    public function __construct(array $pdoConfigurations) {
        $this->pdo = new \PDO($pdoConfigurations['dsn'], $pdoConfigurations['user'], $pdoConfigurations['password'], $this->defaultPdoOptions);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        //$this->pdo->setAttribute(\PDO::ATTR_STATEMENT_CLASS, ["\app\core\database\statement", [$this]]);
    }

    /**
     * Allow methods not implemented by this class to be called on the connection
    */
    public function __call(string $method, array $params = []) {
        return method_exists($this, $method) ? call_user_func_array([$this, $method], $params) : "PDO::$method does not exists.";
    } 

    public function select(string $table, array $fields): self {
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

    public function init(string $table, array $data) {
        $this->tableName = $table;
        $this->bindFields($data); 
        $this->bindValues($data);
        $this->create();
        $this->execute();
    }
    
    /**
     * @return binders for fields and arguments 
    */

    public function bindFields(array $fields): void {
        $this->fields = implode(', ', $fields);
    }
    
    public function bindValues(array $arguments): void {
        foreach($arguments as $selector => $value) {
            $this->query .= ( array_key_first($arguments) === $selector ? self::WHERE : self::AND ) . $selector . self::BIND . $selector;
            $this->setArgumentPair($selector, $value);
        }
    }

    public function setArgumentPair(string $key, string $value): self {
        $this->args[$key] = $value;
        return $this;
    }

    public function where(array $conditions): self {
        $this->preparePlaceholdersAndBoundValues($conditions, 'insert');
        return $this;
    }

    public function innerJoin(string $table, string $using): self {
        $this->query .= self::INNERJOIN . " {$table} USING({$using}) ";
        return $this;
    }
    
    public function leftJoin(string $table, string $on, array $and = []): self {
        $implodedAnd = (count($and) > 0 ? ' AND ' : '') . implode(' AND ', $and);
        $this->query .= " LEFT JOIN {$table} {$on} {$implodedAnd} ";
        return $this;
    }

    public function in(array $inValues): self {
        $this->query .= " IN ( " . implode(', ', $inValues) . " ) ";
        return $this;
    }

    public function create(string $table, array $fields): self {
        $this->preparePlaceholdersAndBoundValues($fields, 'insert');
        $this->query .= "INSERT INTO {$table} ({$this->fields}) VALUES ({$this->placeholders})";
        return $this;
    }

    public function preparePlaceholdersAndBoundValues(array $fields, string $fieldSetter): self {
        foreach ( $fields as $key => $field ) {
            $this->fields .= $key.(array_key_last($fields) === $key ? '' : ',');
            $this->placeholders .= ($fieldSetter === 'insert' ? '' : $key.'=')."?".(array_key_last($fields) === $key ? '' : ',');
            $this->args[] = $field;
            // $this->setArgumentPair($key, $field);
        }
        return $this;
    }

    public function patch(string $table, array $fields, string $primaryKey, string $primaryKeyValue): self {
        $this->preparePlaceholdersAndBoundValues($fields, 'patch');
        $this->query .= "UPDATE {$table} SET {$this->placeholders} WHERE {$primaryKey} = $primaryKeyValue";
        return $this;
    }

    public function delete(string $table): self {
        $this->query .= "DELETE FROM {$table} {$this->where}";
        return $this;
    }

    public function limit(int $limit = self::DEFAULT_LIMIT): self {
        $this->query .= ' LIMIT ' . $limit;
        return $this;
    }

    public function whereClause(array $arguments): self {
        foreach($arguments as $selector => $value) {
            $this->query .= ( array_key_first($arguments) === $selector ? self::WHERE : self::AND ) . $selector . '=? ';
            $this->args[] = $value;
        }
        return $this;
    }

    /*
     * Global query executioner
     * Responsible for executing the desired query
     * @return object | array
    */
    public function execute(string $fetchType = 'fetchAll') {
        try {
            $stmt = $this->prepare($this->query);
            $stmt->execute($this->args);
            $result = $stmt->{$fetchType}();
            $stmt = null;
            $this->resetQuery();
            return $result;
        } catch (\PDOException $e) {
            $errorQuery = $this->query;
            $errorQuery .= $e;
            $this->resetQuery();
            throw new \PDOException("ERROR WITH THE FOLLOWING QUERY: $errorQuery");
        }
    }

    public function getLastID() {
        return $this->pdo->lastInsertId();
    }

    public function groupBy(string $group): self {
        $this->query .= ' GROUP BY ' . $group;
        return $this;
    }

    public function orderBy(string $order): self {
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
        $this->placeholders = '';
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
        migration VARCHAR('.self::MAX_LENGTH.'),
        created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (MigrationID)
    );';

    public function applyMigrations() {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();
        $files = scandir(app()::$ROOT_DIR.'/migrations');
        $toBeAppliedMigrations = array_diff($files, $appliedMigrations);
        $this->iterateMigrations($toBeAppliedMigrations);
    }

    public function iterateMigrations(array $toBeAppliedMigrations) {

        $newMigrations = [];

        foreach ($toBeAppliedMigrations as $migration) {
            if ($migration === '.' || $migration === '..') continue;
            require_once app()::$ROOT_DIR . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            if (strlen($className) > self::MAX_LENGTH) throw new \Exception("Classname ($className) is too long!");
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
        $stmt = $this->pdo->prepare("INSERT INTO Migrations (migration) VALUES $migrations");
        $stmt->execute();
    }

    protected function rawSQL(string $sql): self {
        $this->query = $sql;
        return $this;
    }

    public function prepare(string $sql) {
        return $this->pdo->prepare($sql);
    }

    public function getAppliedMigrations() {
        $statement = $this->pdo->prepare("SELECT migration FROM Migrations");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function createMigrationsTable() {
        $this
            ->rawSQL($this->sqlMigrationTable)
            ->execute();
    }

    /**
    * Begin transaction
    * @return boolean
    */
    public function beginTransaction() : bool {
        return $this->transactionStarted = $this->pdo->beginTransaction();
    }

    /**
    * @return boolean
    */
    public function transaction() : bool {
        return $this->beginTransaction();
    }

    /**
    * Commits a transaction, returning the database connection to autocommit mode.
    * @throws PDOException
    * @return boolean
    */
    public function commit() : bool {
        if($this->transactionStarted === true) {
            return $this->pdo->commit();
        } else {
            throw new \PDOException("Attempted to commit when not in transaction, or transaction failed to start.");
        }
    }

    /**
    * Rolls back the current transaction
    * @throws PDOException
    * @return boolean
    */
    public function rollback() : bool {
        return $this->transactionStarted ? $this->pdo->rollBack() : false;
    }

    public function fetchRow(string $table, ?array $criteria) {
        $this->select($table, ['*'])->whereClause($criteria);
        return $this->execute('fetch');
    }

    protected function log(string $message): void {
        echo date('Y-m-d H:i:s') . ' ' . $message . PHP_EOL;
    }

}