<?php

/**
|----------------------------------------------------------------------------
| Application connection
|----------------------------------------------------------------------------
| 
| @author RE_WEB
| @package core
|
*/

namespace app\core\src\database;

use \app\core\src\miscellaneous\CoreFunctions;

class Connection {
    private static ?Connection $instance = null;

    private array $defaultPdoOptions = [
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
        \PDO::ATTR_EMULATE_PREPARES => false,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    ];

    private \Pdo $pdo;
    
    protected function __construct(#[\SensitiveParameter] array $pdoConfigurations) {
        $this->pdo = new \PDO($pdoConfigurations['dsn'], $pdoConfigurations['user'], $pdoConfigurations['password'], $this->defaultPdoOptions);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    protected function __clone() {
        throw new \app\core\src\exceptions\ForbiddenException('Can not clone ' . get_called_class());
    }

    public function __wakeup() {
        throw new \Exception('Can not unserialize ' . get_called_class());
    }

    public function __call(string $method, array $params = []) {
        return method_exists($this, $method) ? call_user_func_array([$this, $method], $params) : "PDO::$method does not exists.";
    }

    public static function getInstance(array $pdoConfigurations) {
        if (!self::$instance) self::$instance = new self($pdoConfigurations);
        return self::$instance;
    }

    public function execute(#[\SensitiveParameter] string $query, #[\SensitiveParameter] array $args = [], string $fetchType = 'fetchAll') {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($args);
            $result = $stmt->{$fetchType}();
            $stmt = null;
            return $result;
        } catch (\PDOException $e) {
            if (!CoreFunctions::app()::isDevSite()) return;
            $errorQuery = 'QUERY FAIL: ' . $query . ' ------ EXCEPTION: ' . $e;
            CoreFunctions::dd($errorQuery);
        }
    }

    public function getLastID() {
        return $this->pdo->lastInsertId();
    }
}