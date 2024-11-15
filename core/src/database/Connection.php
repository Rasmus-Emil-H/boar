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

use app\core\src\exceptions\NotFoundException;

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
    private const CACHE_TTL = 60;
    private const MAX_CACHE_SIZE = 1000;
    
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

    public function execute(#[\SensitiveParameter] string $query, #[\SensitiveParameter] array $args = [], string $fetchType = self::DEFAULT_SQL_QUERY_FETCH_TYPE) {
        try {
            $serializedArguments = array_map(function($arg) {
                return $arg instanceof \SimpleXMLElement ? (string)$arg : $arg;
            }, $args);

            $cacheKey = md5($query . serialize($serializedArguments));

            if (isset($this->queryCache[$cacheKey])) {
                $cachedResult = $this->queryCache[$cacheKey];
                if (time() - $cachedResult['timestamp'] < self::CACHE_TTL) return $cachedResult['result'];
                unset($this->queryCache[$cacheKey]);
            }

            $stmt = $this->pdo->prepare($query);
            if (!method_exists($stmt, $fetchType)) throw new NotFoundException('Invalid fetch type');
            $stmt->execute($args);

            $result = $stmt->{$fetchType}();
            
            if (!empty($result)) {
                /**
                 * If you want N amount of max entries
                 * if (count($this->queryCache) >= self::MAX_CACHE_SIZE) array_shift($this->queryCache);
                 */

                $this->queryCache[$cacheKey] = ['result' => $result, 'timestamp' => time()];
            }
  
            return $result;
        } catch (\PDOException $e) {
            app()->getLogger()->log('SQL QUERY FAIL: ' . implode(',' . PHP_EOL, explode(',', $query)));
        }
    }

    public function getLastInsertedID() {
        return $this->pdo->lastInsertId();
    }
}