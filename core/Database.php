<?php

/**
 * Std db class
 * @return database runner
*/

namespace app\core;

class Database {

    public \Pdo $pdo;

    public function __construct(array $pdoConfigurations) {
        $this->pdo = new \Pdo($pdoConfigurations['dsn'], $pdoConfigurations['user'], $pdoConfigurations['password']);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
}