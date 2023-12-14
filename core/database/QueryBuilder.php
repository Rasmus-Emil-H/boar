<?php

namespace app\core\database;

use \app\utilities\Builder;

class QueryBuilder implements Builder {

    public const WHERE       = ' WHERE ';
    public const AND         = ' AND ';
    public const BIND        = ' = :';
    public const INNERJOIN   = ' INNER JOIN ';
    public const DEFAULT_LIMIT = 100;

    protected const MAX_LENGTH = 255;
    
    protected string $query  = '';
    protected string $where  = '';
    protected string $fields = '';
    protected string $placeholders = '';
    protected string $table = '';
    protected string $tableName = '';

    protected array $fieldPlaceholders = [];
    protected array  $args = [];

    public function select(string $table, array $fields): self {
        $this->table  = $table;
        $this->bindFields($fields);
        $this->query .= "SELECT {$this->fields} FROM {$this->table}";
        return $this;
    }

    public function replaceWithPlaceholders(string $value): string {
        return '?';
    }

    /**
     * Initialize new entity
     * @return void
    */

    public function init(string $table, array $data): void {
        $this->tableName = $table;
        $this->bindFields($data); 
        $this->bindValues($data);
        $this->create($table, $data);
        $this->run();
    }

    public function bindFields(array $fields): void {
        $this->fields = implode(', ', $fields);
    }
    
    public function bindValues(array $arguments): void {
        foreach($arguments as $selector => $value) {
            $this->query .= ( array_key_first($arguments) === $selector ? self::WHERE : self::AND ) . $selector . self::BIND . $selector;
            $this->setArgumentPair($selector, $value);
        }
    }

    public function setArgumentPair(string $key, string $value): self {
        $this->args[$key] = $value;
        return $this;
    }

    public function innerJoin(string $table, string $using): self {
        $this->query .= self::INNERJOIN . " {$table} USING({$using}) ";
        return $this;
    }
    
    public function leftJoin(string $table, string $on, array $and = []): self {
        $implodedAnd = (count($and) > 0 ? ' AND ' : '') . implode(' AND ', $and);
        $this->query .= " LEFT JOIN {$table} {$on} {$implodedAnd} ";
        return $this;
    }

    public function in(array $inValues): self {
        $this->query .= " IN ( " . implode(', ', $inValues) . " ) ";
        return $this;
    }

    public function create(string $table, array $fields): self {
        $this->preparePlaceholdersAndBoundValues($fields, 'insert');
        $this->query .= "INSERT INTO {$table} ({$this->fields}) VALUES ({$this->placeholders})";
        return $this;
    }

    public function preparePlaceholdersAndBoundValues(array $fields, string $fieldSetter): self {
        foreach ( $fields as $key => $field ) {
            $this->fields .= $key.(array_key_last($fields) === $key ? '' : ',');
            $this->placeholders .= ($fieldSetter === 'insert' ? '' : $key.'=')."?".(array_key_last($fields) === $key ? '' : ',');
            $this->args[] = $field;
        }
        return $this;
    }

    public function patch(string $table, array $fields, string $primaryKey, string $primaryKeyValue): self {
        $this->preparePlaceholdersAndBoundValues($fields, 'patch');
        $this->query .= "UPDATE {$table} SET {$this->placeholders} WHERE {$primaryKey} = $primaryKeyValue";
        return $this;
    }

    public function delete(string $table): self {
        $this->query .= "DELETE FROM {$table} ";
        return $this;
    }

    public function limit(int $limit = self::DEFAULT_LIMIT): self {
        $this->query .= " LIMIT $limit ";
        return $this;
    }

    public function where(array $arguments): self {
        foreach($arguments as $selector => $value) {
            $this->query .= ( array_key_first($arguments) === $selector ? self::WHERE : self::AND ) . $selector . '=? ';
            $this->args[] = $value;
        }
        return $this;
    }

    public function between(string $from, string $to, int $interval, $dateFormat = '%Y-%m-%d'): self {
        $this->query .= " AND STR_TO_DATE(:dateFormat) BETWEEN DATE(:from) - INTERVAL :interval DAY AND DATE(:from) + INTERVAL :interval DAY ";
        $this->args['dateFormat'] = $dateFormat;
        $this->args['from'] = $from;
        $this->args['to'] = $to;
        $this->args['interval'] = $interval;
        return $this;
    }

    public function groupBy(string $group): self {
        $this->query .= ' GROUP BY ' . $group;
        return $this;
    }

    public function orderBy(string $order): self {
        $this->query .= ' ORDER BY ' . $order;
        return $this;
    }

    public function describe() {
        $this->query = "DESCRIBE {$this->tableName}";
        $this->run();
    }

    public function createTable(string $tableName, array $fields): void {
        exit('Table logic should be done via a migration.');
    }

    public function alterTable(string $oldColumn, string $newColumn): void {
        exit('Table logic should be done via a migration.');
    }

    protected function rawSQL(string $sql): self {
        $this->query = $sql;
        return $this;
    }

    public function fetchRow(string $table, ?array $criteria) {
        $this->select($table, ['*'])->where($criteria);
        return $this->run('fetch');
    }

    public function run(string $fetchMode = '') {
        app()->connection->execute($this->query, $this->args, $fetchMode);
    }

}