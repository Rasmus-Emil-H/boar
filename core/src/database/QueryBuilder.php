<?php

/**
|----------------------------------------------------------------------------
| Query builder
|----------------------------------------------------------------------------
|
| @author RE_WEB
| @package core
|
*/

namespace app\core\src\database;

use \app\core\src\utilities\Builder;
use \app\core\src\utilities\Parser;
use \app\core\src\miscellaneous\CoreFunctions;

class QueryBuilder implements Builder {

    public const WHERE       = ' WHERE ';
    public const AND         = ' AND ';
    public const BIND        = ' = :';
    public const INNERJOIN   = ' INNER JOIN ';

    protected const DEFAULT_LIMIT = 100;
    protected const DEFAULT_OFFSET = 0;
    
    protected string $query  = '';
    protected string $where  = '';
    protected string $fields = '';
    protected string $placeholders = '';

    protected array $args = [];

    private array $comparisonOperators = ['=', '<>', '!=', '>', '<', '>=', '<='];
    

    public function __construct(
        public string $class, 
        public string $table, 
        public string $keyID) {
        $this->resetQuery();
    }

    public function select(array $fields = ['*']): self {
        $this->query .= 'SELECT ' . implode(', ', $fields) . '  FROM ' . $this->table;
        return $this;
    }

    public function initializeNewEntity(array $data): void {
        $this->bindValues($data);
        $this->create($data);
        $this->run();
    }
    
    public function bindValues(array $arguments): void {
        foreach($arguments as $selector => $value) {
            $this->query .= ( array_key_first($arguments) === $selector ? self::WHERE : self::AND ) . $selector . self::BIND . $selector;
            $this->setArgumentPair($selector, $value);
        }
    }

    public function valueToPlaceholder(array $fields): self {
        foreach ($fields as $fieldKey => $fieldValue) {
            $this->query .= ':' . ( array_key_last($fields) === $fieldKey ? $fieldKey : $fieldKey . ',' );
            $this->args[$fieldKey] = $fieldValue;
        }
        return $this;
    }

    public function setArgumentPair(string $key, mixed $value): self {
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

    public function create(array|object $fields): self {
        $this->preparePlaceholdersAndBoundValues((array)$fields, 'insert');
        $this->query .= "INSERT INTO {$this->table} ({$this->fields}) VALUES ({$this->placeholders})";
        return $this;
    }

    public function preparePlaceholdersAndBoundValues(array $fields, string $fieldSetter): self {
        foreach ($fields as $key => $field) {
            $this->fields .= $key.(array_key_last($fields) === $key ? '' : ',');
            $this->placeholders .= ($fieldSetter === 'insert' ? '' : $key.'=') . "?" . (array_key_last($fields) === $key ? '' : ',');
            $this->args[] = $field;
        }
        return $this;
    }

    public function patch(array $fields, string $primaryKeyField, string $primaryKey): self {
        $this->preparePlaceholdersAndBoundValues($fields, 'patch');
        $this->query .= "UPDATE {$this->table} SET {$this->placeholders} WHERE $primaryKeyField = :keyValue";
        $this->args['keyValue'] = $primaryKey;
        return $this;
    }

    public function delete(): self {
        $this->query .= ' DELETE FROM ' . $this->table;
        return $this;
    }

    public function limit(int $limit = self::DEFAULT_LIMIT, int $offset = self::DEFAULT_OFFSET): self {
        $this->query .= " LIMIT :limit OFFSET :offset ";
        $this->args['limit']  = $limit;
        $this->args['offset'] = $offset;
        return $this;
    }

    public function where(array $arguments): self {
        foreach ($arguments as $selector => $sqlValue) {
            list($comparison, $sqlValue) = Parser::sqlComparsion(($sqlValue ?? ''), $this->comparisonOperators);
            $this->args[$selector] = $sqlValue;
            $this->query .= (strpos($this->query, self::WHERE) === false ? self::WHERE : self::AND) . "{$selector} {$comparison} :{$selector}";
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

    public function like(array $arguments): self {
        foreach ($arguments as $selector => $sqlValue) {
            list($comparison, $sqlValue) = Parser::sqlComparsion(($sqlValue ?? ''), $this->comparisonOperators);
            $this->args[$selector] = $sqlValue;
            $this->query .= (strpos($this->query, self::WHERE) === false ? self::WHERE : self::AND) . "{$selector} LIKE CONCAT('%', :{$selector}, '%') ";
        }
        return $this;
    }

    public function describeTable() {
        $this->query = ' DESCRIBE ' . $this->table;
        $this->run();
    }

    public function rawSQL(string $sql): self {
        $this->query = $sql;
        return $this;
    }

    public function fetchRow(?array $criteria = null) {
        $this->select()->where($criteria);
        $response = CoreFunctions::app()->getConnection()->execute($this->query, $this->args, 'fetch');
        $this->resetQuery();
        return $response;
    }

    public function debugQuery() {
        CoreFunctions::d("Currently debugging query: " . $this->query);
        CoreFunctions::dd($this->args);
    }

    public function run(string $fetchMode = 'fetchAll'): array {
        $response = CoreFunctions::app()->getConnection()->execute($this->query, $this->args, $fetchMode);
        $this->resetQuery();
        $objects = [];
        foreach ($response as $obj) $objects[] = new $this->class((array)$obj);
        return $objects;
    }

    public function resetQuery() {
        $this->where = '';
        $this->query = '';
        $this->fields = '';
        $this->args = [];
        $this->placeholders = '';
    }

}