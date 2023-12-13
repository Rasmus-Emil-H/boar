<?php

/**
 * Connection relier
 * Author: RE_WEB
 * @package app\core\database
*/

namespace app\core\database;

#[\AllowDynamicProperties]

class Connection {

    private bool $transactionStarted = false;
    private array $defaultPdoOptions = [
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
        \PDO::ATTR_EMULATE_PREPARES => false,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    ];

    private ?\Pdo $pdo;
    
    public function __construct(array $pdoConfigurations) {
        $this->pdo = new \PDO($pdoConfigurations['dsn'], $pdoConfigurations['user'], $pdoConfigurations['password'], $this->defaultPdoOptions);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function __call(string $method, array $params = []) {
        return method_exists($this, $method) ? call_user_func_array([$this, $method], $params) : "PDO::$method does not exists.";
    }

    public function execute(string $query, array $args = [], string $fetchType = 'fetchAll') {
        try {
            $stmt = $this->prepare($query);
            $stmt->execute($args);
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

    public function prepare(string $sql): \PDOStatement {
        return $this->pdo->prepare($sql);
    }

    public function beginTransaction(): bool {
        return $this->transactionStarted = $this->pdo->beginTransaction();
    }

    public function transaction(): bool {
        return $this->beginTransaction();
    }

    public function commit(): bool|\PDOException {
        return $this->transactionStarted ? $this->pdo->commit() : throw new \PDOException("Attempted to commit when not in transaction, or transaction failed to start.");
    }

    public function rollback(): bool {
        return $this->transactionStarted ? $this->pdo->rollBack() : false;
    }

    /**
     * Log current execution context
     * @return void
     */

    protected function log(string $message, bool $exit = false): void {
        echo date('Y-m-d H:i:s') . ' ' . $message . PHP_EOL;
        if ($exit) exit();
    }

}