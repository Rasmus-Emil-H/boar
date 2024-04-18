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

    private const SQL_DESCRIBE = ' DESCRIBE ';
    private const GROUP_BY     = ' GROUP BY ';
    private const ORDER_BY     = ' ORDER BY ';

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
        public string $keyID
    ) {
        $this->resetQuery();
    }

    private function upsertQuery(string $query): void {
        $this->query .= $query;
    }

    private function updateQueryArguments($key, $value): void {
        $this->args[$key] = $value;
    }

    private function getQuery(): string {
        return $this->query;
    }

    private function getArguments(): array {
        return $this->args;
    }

    public function select(array $fields = ['*']): self {
        $this->upsertQuery('SELECT ' . implode(', ', $fields) . '  FROM ' . $this->table);
        return $this;
    }

    public function initializeNewEntity(array $data): void {
        $this->bindValues($data);
        $this->create($data);
        $this->run();
    }
    
    public function bindValues(array $arguments): void {
        foreach($arguments as $selector => $value) {
            $this->upsertQuery((array_key_first($arguments) === $selector ? self::WHERE : self::AND) . $selector . self::BIND . $selector);
            $this->setArgumentPair($selector, $value);
        }
    }

    public function valueToPlaceholder(array $fields): self {
        foreach ($fields as $fieldKey => $fieldValue) {
            $this->upsertQuery(':' . ( array_key_last($fields) === $fieldKey ? $fieldKey : $fieldKey . ',' ));
            $this->updateQueryArguments($fieldKey, $fieldValue);
        }
        return $this;
    }

    public function setArgumentPair(string $key, mixed $value): self {
        $this->updateQueryArguments($key, $value);
        return $this;
    }

    public function innerJoin(string $table, string $using): self {
        $this->upsertQuery(self::INNERJOIN . " {$table} USING({$using}) ");
        return $this;
    }

    public function count(string $count, string $countName = 'count'): self {
        $this->upsertQuery("SELECT COUNT({$count}) as {$countName} FROM {$this->table}");
        return $this;
    }
    
    public function leftJoin(string $table, string $on, array $and = []): self {
        $implodedAnd = (count($and) > 0 ? self::AND : '') . implode(self::AND, $and);
        $this->upsertQuery(" LEFT JOIN {$table} {$on} {$implodedAnd} ");
        return $this;
    }

    public function rightJoin(string $table, string $on, array $and = []): self {
        $implodedAnd = (count($and) > 0 ? self::AND : '') . implode(self::AND, $and);
        $this->upsertQuery(" RIGHT JOIN {$table} {$on} {$implodedAnd} ");
        return $this;
    }

    public function in(string $field, array $ins): self {
         $queryINString = array_map(function($fieldKey, $fieldValue) {
            $this->updateQueryArguments("inCounter$fieldKey", $fieldValue);
            return " :inCounter$fieldKey ";
        }, array_keys($ins), array_values($ins));

        $this->upsertQuery(" AND $field IN ( " . implode(', ', $queryINString) . " ) ");
        return $this;
    }

    public function create(array|object $fields): self {
        $this->preparePlaceholdersAndBoundValues((array)$fields, 'insert');
        $this->upsertQuery("INSERT INTO {$this->table} ({$this->fields}) VALUES ({$this->placeholders})");
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

    public function patch(array $fields, ?string $primaryKeyField = null, ?string $primaryKey = null): self {
        $this->query .= "UPDATE {$this->table} SET ";

        foreach ($fields as $fieldKey => $fieldValue) {
            $this->updateQueryArguments($fieldKey, $fieldValue);
            $this->upsertQuery(" $fieldKey = :$fieldKey " . (array_key_last($fields) === $fieldKey ? '' : ','));
        }

        if ($primaryKeyField && $primaryKey) {
            $this->upsertQuery(" WHERE $primaryKeyField = :primaryKey ");
            $this->updateQueryArguments('primaryKey', $primaryKey);
        }

        return $this;
    }

    private function getComparisonOperators(): array {
        return $this->comparisonOperators;
    }

    public function delete(): self {
        $this->upsertQuery(' DELETE FROM ' . $this->table);
        return $this;
    }

    public function limit(int $limit = self::DEFAULT_LIMIT, int $offset = self::DEFAULT_OFFSET): self {
        $this->upsertQuery(" LIMIT :limit OFFSET :offset ");
        $this->updateQueryArguments('limit', $limit);
        $this->updateQueryArguments('offset', $offset);
        return $this;
    }

    public function where(array $arguments): self {
        foreach ($arguments as $selector => $sqlValue) {
            list($comparison, $sqlValue) = Parser::sqlComparsion(($sqlValue ?? ''), $this->getComparisonOperators());
            $this->updateQueryArguments($selector, $sqlValue);
            $this->upsertQuery((strpos($this->query, self::WHERE) === false ? self::WHERE : self::AND) . "{$selector} {$comparison} :{$selector}");
        }
        return $this;
    }

    public function between(string $from, string $to, int $interval, $dateFormat = '%Y-%m-%d'): self {
        $this->upsertQuery(" AND STR_TO_DATE(:dateFormat) BETWEEN DATE(:from) - INTERVAL :interval DAY AND DATE(:from) + INTERVAL :interval DAY ");
        $this->updateQueryArguments('dateFormat', $dateFormat);
        $this->updateQueryArguments('from', $from);
        $this->updateQueryArguments('to', $to);
        $this->updateQueryArguments('interval', $interval);
        return $this;
    }

    public function groupBy(string $group): self {
        $this->upsertQuery(self::GROUP_BY . $group);
        return $this;
    }

    public function orderBy(string $field, string $order): self {
        $this->upsertQuery(self::ORDER_BY . $field . ' ' . $order);
        return $this;
    }

    public function like(array $arguments): self {
        foreach ($arguments as $selector => $sqlValue) {
            list($comparison, $sqlValue) = Parser::sqlComparsion(($sqlValue ?? ''), $this->getComparisonOperators());
            $this->updateQueryArguments($selector, $sqlValue);
            $this->upsertQuery((strpos($this->query, self::WHERE) === false ? self::WHERE : self::AND) . "{$selector} LIKE CONCAT('%', :{$selector}, '%') ");
        }
        return $this;
    }

    public function describeTable() {
        $this->upsertQuery(self::SQL_DESCRIBE . $this->table);
        $this->run();
    }

    public function rawSQL(string $sql): self {
        $this->upsertQuery($sql);
        return $this;
    }

    public function before(string $field): self {
        $this->where([$field => '< ' . date('Y-m-d')]);
        return $this;
    }

    public function beforeToday(string $field = 'CreatedAt'): self {
        $this->where([$field => '< CURRENT_DATE()']);
        return $this;
    }

    public function after(string $field): self {
        $this->where([$field => '> CURRENT_DATE()']);
        return $this;
    }

    public function afterToday(string $field = 'CreatedAt'): self {
        $this->where([$field => '> CURRENT_DATE()']);
        return $this;
    }

    public function fetchRow(?array $criteria = null) {
        $this->select()->where($criteria);
        $response = app()->getConnection()->execute($this->getQuery(), $this->getArguments(), 'fetch');
        $this->resetQuery();
        return $response;
    }

    public function debugQuery() {
        CoreFunctions::d("Currently debugging query: " . $this->getQuery());
        CoreFunctions::dd($this->getArguments());
    }

    public function run(string $fetchMode = 'fetchAll'): array {
        $response = app()->getConnection()->execute($this->getQuery(), $this->getArguments(), $fetchMode);
        $this->resetQuery();
        $objects = [];
        if (!is_iterable($response)) return [];
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