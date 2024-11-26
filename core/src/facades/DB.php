<?php

namespace app\core\src\facades;

use \app\core\src\database\querybuilder\QueryBuilder;
use \app\core\src\exceptions\NotFoundException;

class DB {

    public function __construct(
        protected $data
    ) {}

    public function get(?string $key = null): mixed {
        return $key ? 
            (
                !isset($this->data[$key]) ? throw new NotFoundException('Invalid key') : $this->data[$key]
            )
            : $this->data;
    }

    public static function table(string $table, string $class = __CLASS__, string|int $primaryKey = ''): QueryBuilder {
        return (new QueryBuilder($class, $table, $primaryKey));
    }

}