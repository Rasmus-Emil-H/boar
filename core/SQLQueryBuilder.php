<?php

/*******************************
 * Bootstrap SQLQueryBuilder 
 * AUTHOR: RE_WEB
 * @package app\core\SQLQueryBuilder
*/

namespace app\core;

class SQLQueryBuilder extends Database {

    public function __construct() {

    }

    public function init(string $table, string $selector = '', array $args = []): Database {
        $this->table = $table;
        $this->selector = $selector;
        $this->bindValues($args);
        return $this;
    }

    public function bindValues(array $arguments) {
        foreach($arguments as $selector => $value) {
            $this->where .= ( array_key_first($arguments) === $selector ? "WHERE " : " AND " ) . $selector . " = ?";
            $this->args[] = $value;
        }
    }

    public function select(): Database {
        $this->query .= "SELECT {$this->selector} FROM {$this->table} {$this->where}";
        return $this;
    }

    public function create(): void {
        $this->query .= "INSERT INTO {$this->tableName} ({$this->implodedFields}) VALUES ({$this->implodedArgs})";
    }

    public function patch(): void {
        $this->query .= "UPDATE {$this->tableName} SET {$this->implodedFields} {$this->where}";
    }

    public function remove(): void {
        $this->query .= "DELETE FROM {$this->tableName} {$this->where}";
    }

    public function limit(int $limit): DatabaseUtilities {
        $this->query .= ' LIMIT ' . $limit;
        return $this;
    }

}