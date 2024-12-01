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

use InvalidArgumentException;

class Connection {
    
    private static ?Connection $instance = null;

    private const DEFAULT_SQL_QUERY_FETCH_TYPE = 'fetchAll';

    private array $defaultPdoOptions = [
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
        \PDO::ATTR_EMULATE_PREPARES => false,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    ];

    private \Pdo $pdo;
    
    protected function __construct(
        #[\SensitiveParameter] array $pdoConfigurations,
        private Cache $cache = new Cache()
    ) {
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

    public function execute(
        #[\SensitiveParameter] string $query, 
        #[\SensitiveParameter] array $args = [], 
        string $fetchType = self::DEFAULT_SQL_QUERY_FETCH_TYPE, 
        $cache = true
    ) {
        try {
            $this->validateFetchType($fetchType);

            $cacheKey = $this->generateCacheKey($query, $args);

            $cachedResult = $this->cache->get($cacheKey);
            
            if ($cache && $cachedResult) return $cachedResult;

            $result = $this->performQuery($query, $args, $fetchType);

            if ($cache && !empty($result)) $this->cache->set($cacheKey, $result);

            return $result;
        } catch (\PDOException $pdoException) {
            app()->getLogger()->log($pdoException);
        }
    }

    private function generateCacheKey(string $query, array $args): string {
        $serializedArgs = array_map(fn($arg) => $arg instanceof \SimpleXMLElement ? (string)$arg : $arg, $args);
        return md5($query . serialize($serializedArgs));
    }

    private function validateFetchType(string $fetchType): void {
        if (method_exists(\PDOStatement::class, $fetchType)) return;

        throw new InvalidArgumentException('Invalid fetch type');
    }

    private function performQuery(string $query, array $args, string $fetchType): mixed {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($args);
        return $stmt->{$fetchType}();
    }

    public function getLastInsertedID() {
        return $this->pdo->lastInsertId();
    }
}