<?php

/**
 * Schema modifier
 * @package app\core\database
 * @author RE_WEB
 */

namespace app\core\database;

use \app\core\database\table\Table;
use \app\utilities\Utilities;

class Schema {

    private const CREATE_TABLE_SYNTAX = 'CREATE TABLE IF NOT EXISTS ';
    private const DROP_TABLE_SYNTAX   = 'DROP TABLE IF EXISTS ';

    public function down(string $table) {
        $query = self::DROP_TABLE_SYNTAX . $table;
        app()
            ->connection
            ->rawSQL($query)
            ->execute();
    }

    public function up($table, \Closure $callback): void {
        $table = new Table($table);
        $callback($table);
        $this->createIfNotExists($table);
    }

    public function createIfNotExists(Table $table) {
        $query = self::CREATE_TABLE_SYNTAX . $table->getName() . '(';
        foreach ($table->getColumns() as $columnKey => $columnOptions)
            $query .= $columnOptions->queryString() . Utilities::appendToStringIfKeyNotLast($table->getColumns(), $columnKey);
        $query .= ')';
        app()
            ->connection
            ->rawSQL($query)
            ->execute();
    }

    /**
     * Used for CRUDing existing tables
     * 
     * @param string $table
     * @param \Closure $callback
     * @return void
     */
    
    public function table($table, \Closure $callback): void {
        $table = new Table($table);
        $callback($table);
        $query = 'ALTER TABLE ' . $table->getName() . ' ';
        foreach ($table->getColumns() as $columnKey => $columnOptions)
            $query .= ($columnOptions->queryString(isAlteringTable: true) . Utilities::appendToStringIfKeyNotLast($table->getColumns(), $columnKey));
        app()
            ->connection
            ->rawSQL($query)
            ->execute();
    }

}