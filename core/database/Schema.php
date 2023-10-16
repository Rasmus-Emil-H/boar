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
        $this->formSQL($table);
    }

    public function formSQL(table\Table $table) {
        $query = 'CREATE TABLE IF NOT EXISTS ' . $table->getName() . '(';
        foreach ( $table->getColumns() as $columnKey => $columnOptions )
            $query .= $columnOptions->queryString() . (array_key_last($table->getColumns()) === $columnKey ? null : ', ');
        $query .= ')';
        var_dump($query);
        app()->connection->rawSQL($query);
    }

}