<?php

/*******************************
 * Bootstrap SQLQueryBuilder 
 * AUTHOR: RE_WEB
 * @package app\core\SQLQueryBuilder
*/

namespace app\core;

class SQLQueryBuilder extends Database {

    public const WHERE      = ' WHERE ';
    public const AND        = ' AND ';
    public const BIND       = " = ?";
    public const INNERJOIN  = ' INNER JOIN ';

    public function select(string $table, array $fields): Database {
        $this->table  = $table;
        $this->bindFields($fields);
        $this->query .= "SELECT {$this->fields} FROM {$this->table}";
        return $this;
    }

    public function bindFields(array $fields): void {
        $this->fields = implode(', ', $fields);
    }

    public function where(array $conditions): Database {
        $this->bindValues($conditions);
        return $this;
    }

    public function join(string $table, string $using): Database {
        $this->query .= self::INNERJOIN . " {$table} USING({$using}) ";
        return $this;
    }

    public function bindValues(array $arguments): void {
        foreach($arguments as $selector => $value) {
            $this->query .= ( array_key_first($arguments) === $selector ? self::WHERE : self::AND ) . $selector . self::BIND;
            $this->args[] = $value;
        }
    }

    public function create(): Database {
        $this->query .= "INSERT INTO {$this->tableName} ({$this->implodedFields}) VALUES ({$this->implodedArgs})";
        return $this;
    }

    public function patch(): Database {
        $this->query .= "UPDATE {$this->tableName} SET {$this->implodedFields} {$this->where}";
        return $this;
    }

    public function delete(): Database {
        $this->query .= "DELETE FROM {$this->tableName} {$this->where}";
        return $this;
    }

    public function limit(int $limit): Database {
        $this->query .= ' LIMIT ' . $limit;
        return $this;
    }

}