<?php

/**
 * Schema modifier
 * @package app\core\database
 * @author RE_WEB
*/

namespace app\core\database;

class Schema {

    public function create(string $table, $callback): void {
        $table = new table\Table($table);
        $callback($table);
    }

}