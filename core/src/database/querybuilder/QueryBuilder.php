<?php

/**
|----------------------------------------------------------------------------
| Query builder initial extension
|----------------------------------------------------------------------------
|
| @author RE_WEB
| @package core
|
*/

namespace app\core\src\database\querybuilder;

use \app\core\src\database\table\Table;
use \app\core\src\utilities\Parser;
use \app\core\src\miscellaneous\CoreFunctions;

class QueryBuilder extends QueryBuilderBase {

    public function select(array $fields = ['*']): self {
        $this->upsertQuery($this::SELECT . implode(', ', $fields) . $this::FROM . $this->table);
        return $this;
    }

    public function selectFieldsFrom(array $fields, string $from = ''): self {
        $this->upsertQuery($this::SELECT . implode(', ', $fields) . $this::FROM . $from);
        return $this;
    }

    public function selectFromSubQuery(string $fields = '*') {
        $this->upsertQuery($this::SELECT . $fields . $this::FROM);
        return $this; 
    }

    public function initializeNewEntity(array $data): void {
        $this->bindValues($data);
        $this->create($data);
        $this->run();
    }
    
    public function bindValues(array $arguments): void {
        foreach($arguments as $selector => $value) {
            $this->upsertQuery((array_key_first($arguments) === $selector ? $this::WHERE : $this::AND) . $selector . $this::BIND . $selector);
            $this->setArgumentPair($selector, $value);
        }
    }

    public function valueToPlaceholder(array $fields): self {
        foreach ($fields as $fieldKey => $fieldValue) {
            $this->upsertQuery(':' . ( array_key_last($fields) === $fieldKey ? $fieldKey : $fieldKey . ',' ));
            $this->updateQueryArgument($fieldKey, $fieldValue);
        }
        return $this;
    }

    public function setArgumentPair(string $key, mixed $value): self {
        $this->updateQueryArgument($key, $value);
        return $this;
    }

    public function innerJoin(string $table, string $using = ''): self {
        if ($using !== '') $using = " USING({$using}) ";
        $this->upsertQuery($this::INNER_JOIN . " {$table} {$using} ");
        return $this;
    }

    public function innerJoins(array $innerJoinConditions): self {
        foreach ($innerJoinConditions as $table => $using)
            $this->innerJoin($table, $using);
        return $this;
    }

    public function count(string $count, string $countName = 'count'): self {
        $this->upsertQuery("SELECT COUNT({$count}) as {$countName} FROM {$this->table}");
        return $this;
    }
    
    public function leftJoin(string $table, string $on, array $and = []): self {
        $implodedAnd = (count($and) > 0 ? $this::AND : '') . implode($this::AND, $and);
        $this->upsertQuery($this::LEFT_JOIN . "{$table} {$on} {$implodedAnd} ");
        return $this;
    }

    public function rightJoin(string $table, string $on, array $and = []): self {
        $implodedAnd = (count($and) > 0 ? $this::AND : '') . implode($this::AND, $and);
        $this->upsertQuery($this::RIGHT_JOIN . "{$table} {$on} {$implodedAnd} ");
        return $this;
    }

    public function in(string $field, array $ins): self {
         $queryINString = array_map(function($fieldKey, $fieldValue) {
            $this->updateQueryArgument("inCounter$fieldKey", $fieldValue);
            return " :inCounter$fieldKey ";
        }, array_keys($ins), array_values($ins));

        $this->upsertQuery(" AND $field IN ( " . implode(', ', $queryINString) . " ) ");
        return $this;
    }

    public function on(string $field): self {
        $this->upsertQuery(" ON {$field} ");
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
        $this->upsertQuery("UPDATE {$this->table} SET ");

        foreach ($fields as $fieldKey => $fieldValue) {
            $this->updateQueryArgument($fieldKey, $fieldValue);
            $this->upsertQuery(" $fieldKey = :$fieldKey " . (array_key_last($fields) === $fieldKey ? '' : ','));
        }

        if ($primaryKeyField && $primaryKey) {
            $this->upsertQuery($this::WHERE . " $primaryKeyField = :primaryKey ");
            $this->updateQueryArgument('primaryKey', $primaryKey);
        }

        return $this;
    }

    private function getComparisonOperators(): array {
        return $this->comparisonOperators;
    }

    public function delete(): self {
        $this->upsertQuery($this::DELETE_FROM . $this->table);
        return $this;
    }

    public function truncate(): self {
        $this->upsertQuery($this::TRUNCATE . $this->table);
        return $this;
    }

    public function with(string $temp): self {
        $this->upsertQuery($this::WITH . $temp . $this::AS);
        return $this;
    }
    
    public function partitionBy(string $sqlMethod, string $partitonBy): self {
        $this->upsertQuery($sqlMethod . ' OVER ( ' . $this::PARTITION_BY . ' ' . $partitonBy);
        return $this;
    }

    public function limit(int $limit = self::DEFAULT_LIMIT): self {
        $this->upsertQuery($this::LIMIT . ' :limit ');
        $this->updateQueryArgument('limit', $limit);
        return $this;
    }

    public function offset(int $offset = self::DEFAULT_OFFSET): self {
        $this->upsertQuery($this::OFFSET . ' :offset ');
        $this->updateQueryArgument('offset', $offset);
        return $this;
    }

    private function checkStart(): string {
        return (strpos($this->getQuery(), $this::WHERE) === false ? $this::WHERE : $this::AND);
    }

    public function where(array $arguments = []): self {
        foreach ($arguments as $selector => $sqlValue) {
            $dateField = str_contains($selector, $this::DEFAULT_FRONTEND_DATE_FROM_INDICATOR) || str_contains($selector, $this::DEFAULT_FRONTEND_DATE_TO_INDICATOR);

            if ($dateField) $this->handleDateClausing($selector, $sqlValue);
            else {
                list($comparison, $sqlValue) = Parser::sqlComparsion(($sqlValue ?? ''), $this->getComparisonOperators());
                $key = preg_replace($this::DEFAULT_REGEX_REPLACE_PATTERN, '', $selector);

                $this->updateQueryArgument($key, $sqlValue);
                $this->upsertQuery($this->checkStart() . "{$selector} {$comparison} :{$key}");
            }
        }
        return $this;
    }

    private function handleDateClausing(string $selector, string $sqlValue) {
        list($order, $field) = explode('-', $selector);
        if (str_contains($order, '.')) $table = CoreFunctions::first(explode('.', $order))->scalar;

        $selector = preg_replace($this::DEFAULT_REGEX_REPLACE_PATTERN, '', $selector);
        $sqlValue = date($this::DEFAULT_SQL_DATE_FORMAT, strtotime($sqlValue));
        $arrow = CoreFunctions::last(explode('.', $order))->scalar === 'from' ? '>' : '<';

        $this->upsertQuery($this->checkStart() . (isset($table) && $table ? $table . '.' : '') . "{$field} " . $arrow . "= :{$selector}");
        $this->updateQueryArgument($selector, $sqlValue);
    }

    public function or(array $arguments) {
        foreach ($arguments as $selector => $sqlValue) {
            list($comparison, $sqlValue) = Parser::sqlComparsion(($sqlValue ?? ''), $this->getComparisonOperators());
            $key = trim($this::OR) . preg_replace($this::DEFAULT_REGEX_REPLACE_PATTERN, '', $selector);
            $this->updateQueryArgument($key, $sqlValue);
            $this->upsertQuery($this::OR . " {$selector} {$comparison} :{$key}");
        }
        return $this;
    }

    public function forceWhere(array $arguments = []): self {
        foreach ($arguments as $selector => $sqlValue) {
            list($comparison, $sqlValue) = Parser::sqlComparsion(($sqlValue ?? ''), $this->getComparisonOperators());
            $key = preg_replace($this::DEFAULT_REGEX_REPLACE_PATTERN, '', $selector);

            $this->updateQueryArgument($key, $sqlValue);
            $this->upsertQuery($this::WHERE . " {$selector} {$comparison} :{$key}");
        }
        return $this;
    }

    public function between(string $from, string $to, int $interval, $dateFormat = '%Y-%m-%d'): self {
        $this->upsertQuery(" AND STR_TO_DATE(:dateFormat) BETWEEN DATE(:from) - INTERVAL :interval DAY AND DATE(:from) + INTERVAL :interval DAY ");

        $this->updateQueryArguments(['dateFormat' => $dateFormat, 'from' => $from, 'to' => $to, 'interval' => $interval]);
        
        return $this;
    }

    public function groupBy(string $group): self {
        $this->upsertQuery($this::GROUP_BY . $group);
        return $this;
    }

    public function from(string $from): self {
        $this->upsertQuery($this::FROM . $from);
        return $this;
    }

    public function orderBy(string|array $field, string $order = self::DEFAULT_ASCENDING_ORDER): self {
        if (is_iterable($field)) $field = implode(',', $field);
        $this->upsertQuery($this::ORDER_BY . $field . ' ' . $order);
        return $this;
    }

    public function orderBySortOrder(string $order = self::DEFAULT_ASCENDING_ORDER): self {
        $this->upsertQuery($this::ORDER_BY . Table::SORT_ORDER_COLUMN . ' ' . $order);
        return $this;
    }

    public function like(array $arguments): self {
        foreach ($arguments as $selector => $sqlValue) {
            list($comparison, $sqlValue) = Parser::sqlComparsion(($sqlValue ?? ''), $this->getComparisonOperators());
            $this->updateQueryArgument($selector, $sqlValue);
            $sql = $this->checkStart() . "{$selector} LIKE CONCAT('%', :{$selector}, '%') ";
            $this->upsertQuery($sql);
        }
        return $this;
    }

    public function isNull(string $field): self {
        $this->upsertQuery($this->checkStart() . $field . $this::IS_NULL);
        return $this;
    }
    
    public function isNotNull(string $field): self {
        $this->upsertQuery($this->checkStart() . $field . $this::IS_NOT_NULL);
        return $this;
    }

    public function describeTable() {
        $this->upsertQuery($this::SQL_DESCRIBE . $this->table);
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
        $this->where([$field => $this::LOWER_THAN_CURRENT_DAY]);
        return $this;
    }

    public function after(string $field): self {
        $this->where([$field => $this::HIGHER_THAN_CURRENT_DAY]);
        return $this;
    }

    public function afterToday(string $field = 'CreatedAt'): self {
        $this->where([$field => $this::HIGHER_THAN_CURRENT_DAY]);
        return $this;
    }

    public function subQuery(\Closure $callback): self {
        $this->upsertQuery($this::SUBQUERY_OPEN);
        call_user_func($callback, $this);
        $this->upsertQuery($this::SUBQUERY_CLOSE);
        return $this;
    }

    public function distinct(): self {
        $this->upsertQuery('SELECT DISTINCT ' . $this->fields . $this::FROM . $this->table);
        return $this;
    }

    public function as(string $as): self {
        $this->upsertQuery($this::AS . $as);
        return $this;
    }

    public function having(array $conditions): self {
        foreach ($conditions as $field => $value) {
            $this->upsertQuery("HAVING {$field} = :{$field}");
            $this->updateQueryArgument($field, $value);
        }
        return $this;
    }

    public function sum(string $field, string $alias = 'sum'): self {
        $this->upsertQuery("SELECT SUM({$field}) AS {$alias} FROM {$this->table}");
        return $this;
    }
    
    public function avg(string $field, string $alias = 'avg'): self {
        $this->upsertQuery("SELECT AVG({$field}) AS {$alias} FROM {$this->table}");
        return $this;
    }
    
    public function min(string $field, string $alias = 'min'): self {
        $this->upsertQuery("SELECT MIN({$field}) AS {$alias} FROM {$this->table}");
        return $this;
    }
    
    public function max(string $field, string $alias = 'max'): self {
        $this->upsertQuery("SELECT MAX({$field}) AS {$alias} FROM {$this->table}");
        return $this;
    }

    public function appendParenthesisStart(): self {
        $this->upsertQuery(" ( ");
        return $this;
    }

    public function appendParenthesisEnd(): self {
        $this->upsertQuery(" ) ");
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

        foreach ($response as $obj)
            $objects[] = new $this->class((array)$obj);

        return $objects;
    }

}