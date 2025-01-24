<?php

namespace app\core\src\facades;

use \app\core\src\database\querybuilder\QueryBuilder;

use \app\core\src\exceptions\NotFoundException;

use \app\core\src\miscellaneous\CoreFunctions;

class DB {

    public function __construct(
        protected $data
    ) {}

    public function get(?string $key = null): mixed {
        if (!isset($this->data[$key])) throw new NotFoundException(__CLASS__ . ' invalid key');

        return $key ? $this->data[$key] : $this->data;
    }

    public function getData(): array {
        return $this->data;
    }

    public static function table(string $table, string $class = __CLASS__, string|int $primaryKey = ''): QueryBuilder {
        return (new QueryBuilder($class, $table, $primaryKey));
    }

    public static function dump(array $tables = []): void {
        $config = env('database');

        $dsn = CoreFunctions::last(explode(';', $config->dsn));

        $db = str_replace('dbname=', '', $dsn->scalar);
        
        $fileName = app()::$ROOT_DIR . '/' . time() . 'test.sql';

        exec(sprintf(
            'mysqldump -u %s -p%s -h 127.0.0.1 %s %s > %s',
            escapeshellarg($config->user),
            escapeshellarg($config->password),
            escapeshellarg($db),
            implode(' ', array_map('escapeshellarg', $tables)),
            escapeshellarg($fileName) 
        ));
    }

}