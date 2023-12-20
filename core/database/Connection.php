<?php

/**
 * Connection relier
 * Author: RE_WEB
 * @package app\core\database
 */

namespace app\core\database;

class Connection {

    public const MAX_COLUMN_LENGTH = 255;

    private static ?Connection $instance = null;

    private bool $transactionStarted = false;
    private array $defaultPdoOptions = [
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
        \PDO::ATTR_EMULATE_PREPARES => false,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    ];

    private ?\Pdo $pdo;
    
    protected function __construct(array $pdoConfigurations) {
        $this->pdo = new \PDO($pdoConfigurations['dsn'], $pdoConfigurations['user'], $pdoConfigurations['password'], $this->defaultPdoOptions);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    protected function __clone() {

    }

    public function __wakeup() {
        throw new \Exception('Cannot unserialize');
    }

    public function __call(string $method, array $params = []) {
        return method_exists($this, $method) ? call_user_func_array([$this, $method], $params) : "PDO::$method does not exists.";
    }

    public static function getInstance(array $pdoConfigurations) {
        if (!self::$instance) self::$instance = new self($pdoConfigurations);
        return self::$instance;
    }

    public function execute(string $query, array $args = [], string $fetchType = 'fetchAll') {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($args);
            $result = $stmt->{$fetchType}();
            $stmt = null;
            return $result;
        } catch (\PDOException $e) {
            $errorQuery = $query . $e;
            app()->addSystemEvent(['Query failed: ' . $query . ' With the following arguments: ' . implode(',', $args)]);
            throw new \PDOException('ERROR WITH THE FOLLOWING QUERY: ' . $errorQuery);
        }
    }

    public function getLastID() {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction(): bool {
        return $this->transactionStarted = $this->pdo->beginTransaction();
    }

    public function transaction(): bool {
        return $this->beginTransaction();
    }

    public function commit(): bool|\PDOException {
        return $this->transactionStarted ? $this->pdo->commit() : throw new \PDOException('Attempted to commit when not in transaction, or transaction failed to start.');
    }

    public function rollback(): bool {
        return $this->transactionStarted ? $this->pdo->rollBack() : false;
    }

}