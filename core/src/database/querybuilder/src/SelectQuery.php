<?php

namespace app\core\src\database\querybuilder\src;

use \app\core\src\database\table\Table;
use \app\core\src\miscellaneous\CoreFunctions;
use \app\core\src\utilities\Parser;


trait SelectQuery {

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

    public function select(array $fields = ['*']): self {
        $this->upsertQuery(Constants::SELECT . implode(', ', $fields) . Constants::FROM . $this->table);
        return $this;
    }

    public function selectFieldsFrom(array $fields, string $from = ''): self {
        $this->upsertQuery(Constants::SELECT . implode(', ', $fields) . Constants::FROM . $from);
        return $this;
    }

    public function selectFields(array $fields): self {
        $this->upsertQuery(Constants::SELECT . implode(', ', $fields));
        return $this;
    }

    public function selectFromSubQuery(string $fields = '*'): self {
        $this->upsertQuery(Constants::SELECT . $fields . Constants::FROM);
        return $this;
    }

    public function distinct(string $distinct): self {
        $this->upsertQuery(' DISTINCT ' . $distinct);
        return $this;
    }

    public function distinctFrom(): self {
        $this->upsertQuery('SELECT DISTINCT ' . $this->fields . Constants::FROM . $this->table);
        return $this;
    }

    public function count(string $count, string $countName = 'count'): self {
        $this->upsertQuery(" COUNT({$count}) as {$countName} ");
        return $this;
    }

    public function countFrom(string $count, string $countName = 'count'): self {
        $this->upsertQuery("SELECT COUNT({$count}) as {$countName} FROM {$this->table}");
        return $this;
    }

    public function where(array $arguments = []): self {
        foreach ($arguments as $selector => $sqlValue) {
            $dateField = str_contains($selector, Constants::DEFAULT_FRONTEND_DATE_FROM_INDICATOR) || str_contains($selector, Constants::DEFAULT_FRONTEND_DATE_TO_INDICATOR);

            if ($dateField) $this->handleDateClausing($selector, $sqlValue);
            else {
                list($comparison, $sqlValue) = Parser::sqlComparsion(($sqlValue ?? null), $this->getComparisonOperators());
                $key = preg_replace(Constants::DEFAULT_REGEX_REPLACE_PATTERN, '', $selector);

                $this->updateQueryArgument($key, $sqlValue);
                $this->upsertQuery($this->checkStart() . "{$selector} {$comparison} :{$key}");
            }
        }
        return $this;
    }

    private function handleDateClausing(string $selector, string $sqlValue) {
        list($order, $field) = explode('-', $selector);
        if (str_contains($order, '.')) $table = CoreFunctions::first(explode('.', $order))->scalar;

        $selector = preg_replace(Constants::DEFAULT_REGEX_REPLACE_PATTERN, '', $selector);
        $sqlValue = date(Constants::DEFAULT_SQL_DATE_FORMAT, strtotime($sqlValue));
        $arrow = CoreFunctions::last(explode('.', $order))->scalar === 'from' ? '>' : '<';

        $this->upsertQuery($this->checkStart() . (isset($table) && $table ? $table . '.' : '') . "{$field} " . $arrow . "= :{$selector}");
        $this->updateQueryArgument($selector, $sqlValue);
    }

    public function or(array $arguments) {
        foreach ($arguments as $selector => $sqlValue) {
            list($comparison, $sqlValue) = Parser::sqlComparsion(($sqlValue ?? ''), $this->getComparisonOperators());
            $key = trim(Constants::OR) . preg_replace(Constants::DEFAULT_REGEX_REPLACE_PATTERN, '', $selector);
            $this->updateQueryArgument($key, $sqlValue);
            $this->upsertQuery(Constants::OR . " {$selector} {$comparison} :{$key}");
        }
        return $this;
    }

    public function forceWhere(array $arguments = []): self {
        foreach ($arguments as $selector => $sqlValue) {
            list($comparison, $sqlValue) = Parser::sqlComparsion(($sqlValue ?? ''), $this->getComparisonOperators());
            $key = preg_replace(Constants::DEFAULT_REGEX_REPLACE_PATTERN, '', $selector);

            $this->updateQueryArgument($key, $sqlValue);
            $this->upsertQuery(Constants::WHERE . " {$selector} {$comparison} :{$key}");
        }
        return $this;
    }

    public function between(string|int $from, string|int $to): self {
        $this->upsertQuery($this->checkStart() . "  BETWEEN :from AND :to ");
        $this->updateQueryArguments(compact('from', 'to'));
        
        return $this;
    }

    public function notBetween(string|int $from, string|int $to): self {
        $this->upsertQuery($this->checkStart() . " NOT BETWEEN :from AND :to ");
        $this->updateQueryArguments(compact('from', 'to'));
        
        return $this;
    }

    public function dateBetween(string $column, string $from, string $to, $dateFormat = '%Y %m %d'): self {
        $formattedColumn = str_replace('.', '_', $column);
        
        $this->upsertQuery($this->checkStart() . " $column BETWEEN STR_TO_DATE(:fromDateRange_$formattedColumn, '$dateFormat') AND STR_TO_DATE(:toDateRange_$formattedColumn, '$dateFormat')");
        $this->updateQueryArguments([
            "fromDateRange_$formattedColumn" => $from,
            "toDateRange_$formattedColumn" => $to,
        ]);

        return $this;
    }

    public function dateNotBetween(string $column, string $from, string $to, $dateFormat = '%Y %m %d'): self {
        $formattedColumn = str_replace('.', '_', $column);
        
        $this->upsertQuery($this->checkStart() . " $column NOT BETWEEN STR_TO_DATE(:fromDateRange_$formattedColumn, '$dateFormat') AND STR_TO_DATE(:toDateRange_$formattedColumn, '$dateFormat') ");
        $this->updateQueryArguments([
            "fromDateRange_$formattedColumn" => $from,
            "toDateRange_$formattedColumn" => $to,
        ]);

        return $this;
    }

    public function isNull(string $field): self {
        $this->upsertQuery($this->checkStart() . $field . Constants::IS_NULL);
        return $this;
    }
    
    public function isNotNull(string $field): self {
        $this->upsertQuery($this->checkStart() . $field . Constants::IS_NOT_NULL);
        return $this;
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
        $this->where([$field => Constants::LOWER_THAN_CURRENT_DAY]);
        return $this;
    }

    public function after(string $field): self {
        $this->where([$field => Constants::HIGHER_THAN_CURRENT_DAY]);
        return $this;
    }

    public function afterToday(string $field = 'CreatedAt'): self {
        $this->where([$field => Constants::HIGHER_THAN_CURRENT_DAY]);
        return $this;
    }

    public function limit(int $limit = Constants::DEFAULT_LIMIT): self {
        $this->upsertQuery(Constants::LIMIT . ' :limit ');
        $this->updateQueryArgument('limit', $limit);
        return $this;
    }

    public function offset(int $offset = Constants::DEFAULT_OFFSET): self {
        $this->upsertQuery(Constants::OFFSET . ' :offset ');
        $this->updateQueryArgument('offset', $offset);
        return $this;
    }

    private function checkStart(): string {
        return (strpos($this->getQuery(), Constants::WHERE) === false ? Constants::WHERE : Constants::AND);
    }

    public function groupBy(string $group): self {
        $this->upsertQuery(Constants::GROUP_BY . $group);
        return $this;
    }

    public function from(?string $from = null): self {
        $this->upsertQuery(Constants::FROM . ($from ?? $this->table));
        return $this;
    }

    public function orderBy(string|array $field, string $order = Constants::DEFAULT_ASCENDING_ORDER): self {
        if (is_iterable($field)) $field = implode(',', $field);
        $this->upsertQuery(Constants::ORDER_BY . $field . ' ' . $order);
        return $this;
    }

    public function orderBySortOrder(string $order = Constants::DEFAULT_ASCENDING_ORDER): self {
        $this->upsertQuery(Constants::ORDER_BY . Table::SORT_ORDER_COLUMN . ' ' . $order);
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

    public function as(string $as): self {
        $this->upsertQuery(Constants::AS . $as);
        return $this;
    }

    public function having(array $conditions): self {
        foreach ($conditions as $field => $value) {
            $this->upsertQuery("HAVING {$field} = :{$field}");
            $this->updateQueryArgument($field, $value);
        }
        return $this;
    }

    public function with(string $temp): self {
        $this->upsertQuery(Constants::WITH . $temp . Constants::AS);
        return $this;
    }

    public function over(): self {
       $this->upsertQuery(' OVER ( '); 
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

    // Additional select-related methods can go here
}