<?php

/**
|----------------------------------------------------------------------------
| Application database connection
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

    private const DEFAULT_SQL_QUERY_FETCH_TYPE = 'fetchAll';

    private array $defaultPdoOptions = [
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
        \PDO::ATTR_EMULATE_PREPARES => false,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    ];

    private \Pdo $pdo;

    private array $queryCache;
    
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

    private function checkQueryCache(string $cacheKey) {
      if (isset($this->queryCache[$cacheKey])) return $this->queryCache[$cacheKey];
    }

   private function setCacheKeyResult(string $cacheKey, mixed $result): void {
        $this->queryCache[$cacheKey] = $result;
    }

    public function execute(#[\SensitiveParameter] string $query, #[\SensitiveParameter] array $args = [], string $fetchType = self::DEFAULT_SQL_QUERY_FETCH_TYPE) {
        try {
            
            $cacheKey = md5($query . serialize($args));
            $cachedSQLQueryResultBasedOnCacheKey = $this->checkQueryCache($cacheKey);
            if ($cachedSQLQueryResultBasedOnCacheKey) return $cachedSQLQueryResultBasedOnCacheKey;

            $stmt = $this->pdo->prepare($query);
            $stmt->execute($args);
            $result = $stmt->{$fetchType}();
            $stmt = null;

            $this->setCacheKeyResult($cacheKey, $result);
  
            return $result;
        } catch (\PDOException $e) {
            if (!app()::isDevSite()) return;
            $errorQuery = 'QUERY FAIL: ' . $query . ' ------ EXCEPTION: ' . $e;
            CoreFunctions::dd($errorQuery);
        }
    }

    public function getLastID() {
        return $this->pdo->lastInsertId();
    }
}