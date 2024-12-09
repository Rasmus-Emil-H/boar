<?php

/**
|----------------------------------------------------------------------------
| PostgreSQL adapter
|----------------------------------------------------------------------------
|
| @author RE_WEB
| @package core
|
*/


namespace app\core\src\database\adapters;

use PDO;

class PgSQL extends Adapter {

    private array $options = [
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
        \PDO::ATTR_EMULATE_PREPARES => false,
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    ];

    protected string $driverName = 'mysql';

    public function doConnect(): PDO {
        $db = $this->config;

        $pdo = new PDO('pgsql:' . $db->dsn, $db->user, $db->password, $this->options);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

}