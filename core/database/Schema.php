<?php

/**
 * Schema modifier
 * @package app\core\database
 * @author RE_WEB
*/

namespace app\core\database;

use \app\core\database\table\Table;

class Schema {

    private const CREATE_TABLE_SYNTAX = 'CREATE TABLE IF NOT EXISTS ';
    private const DROP_TABLE_SYNTAX   = ' DROP TABLE IF EXISTS ';

    public function drop(string $table) {
        $query = self::DROP_TABLE_SYNTAX . $table;
        app()
            ->connection
            ->rawSQL($query)
            ->execute();
    }

    /**
     * @param string $tablename
     * @param \Closure $callback
     * @return void
     */

    public function up($table, \Closure $callback): void {
        $table = new Table($table);
        $callback($table);
        $this->createIfNotExists($table);
    }

    public function createIfNotExists(Table $table) {
        $query = self::CREATE_TABLE_SYNTAX . $table->getName() . '(';
        foreach ( $table->getColumns() as $columnKey => $columnOptions )
            $query .= $columnOptions->queryString() . (array_key_last($table->getColumns()) === $columnKey ? null : ', ');
        $query .= ')';
        app()
            ->connection
            ->rawSQL($query)
            ->execute();
    }

}