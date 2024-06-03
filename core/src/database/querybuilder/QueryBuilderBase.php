<?php

/**
|----------------------------------------------------------------------------
| Query builder base
|----------------------------------------------------------------------------
|
| @author RE_WEB
| @package core
|
*/

namespace app\core\src\database\querybuilder;

use \app\core\src\utilities\Builder;

abstract class QueryBuilderBase implements Builder {

    public const WHERE          = ' WHERE ';
    public const AND            = ' AND ';
    public const OR             = ' OR ';
    public const LIMIT          = ' LIMIT ';
    public const OFFSET         = ' OFFSET ';
    public const BIND           = ' = :';
    public const INNER_JOIN     = ' INNER JOIN ';
    public const LEFT_JOIN      = ' LEFT JOIN ';
    public const RIGHT_JOIN     = ' RIGHT JOIN ';
    public const SUBQUERY_OPEN  = ' ( ';
    public const SUBQUERY_CLOSE = ' ) ';

    public const WITH = ' WITH ';
    public const AS = ' AS ';
    public const DELETE_FROM = ' DELETE FROM ';
    public const TRUNCATE = ' TRUNCATE TABLE ';
    public const FROM = ' FROM ';
    public const SELECT = ' SELECT ';
    public const IS_NULL = ' IS NULL ';
    public const IS_NOT_NULL = ' IS NOT NULL ';

    protected const SQL_DESCRIBE = ' DESCRIBE ';
    protected const GROUP_BY     = ' GROUP BY ';
    protected const ORDER_BY     = ' ORDER BY ';
    protected const DEFAULT_ASCENDING_ORDER = ' ASC ';
    protected const DEFAULT_DESCENDING_ORDER = ' DESC ';
    protected const DEFAULT_SQL_DATE_FORMAT = 'Y/m/d';
    protected const DEFAULT_FRONTEND_DATE_FROM_INDICATOR = 'from-';
    protected const DEFAULT_FRONTEND_DATE_TO_INDICATOR = 'to-';

    protected const DEFAULT_LIMIT = 20;
    protected const DEFAULT_OFFSET = 0;
    
    protected string $query  = '';
    protected string $where  = '';
    protected string $fields = '';
    protected string $placeholders = '';

    protected string $lastQueryPart = '';

    protected array $args = [];

    protected array $comparisonOperators = ['=', '<>', '!=', '>', '<', '>=', '<='];
    public const DEFAULT_REGEX_REPLACE_PATTERN = '/[^a-zA-Z0-9]/';
    
    public function __construct(
        public string $class, 
        public string $table, 
        public string $keyID
    ) {
        $this->resetQuery();
    }

    public function upsertQuery(string $query): void {
        $this->query .= $query;
        $this->insertLastQueryPart($query);
    }

    private function insertLastQueryPart(string $query) {
        $this->lastQueryPart = $query;
    }

    /**
     * LIFO observer for most recent query part
     * @return ?string
     */

    public function getLastQueryPart(): ?string {
        return $this->lastQueryPart ?? null;
    }

    private function checkQueryKey(string $key) {
        if (isset($this->args[$key])) debug('Your key: ' . $key . ' is already set in the current query');
    }

    public function updateQueryArguments(array $arguments): void {
        foreach ($arguments as $key => $value) {
            $this->checkQueryKey($key);

            $this->args[$key] = $value;
        }
    }

    public function updateQueryArgument(string $key, string $value): void {
        $this->checkQueryKey($key);

        $this->args[$key] = $value;
    }

    public function getQuery(): string {
        return $this->query;
    }

    public function getArguments(): array {
        return $this->args;
    }

    public function resetQuery() {
        $this->where = '';
        $this->query = '';
        $this->fields = '';
        $this->args = [];
        $this->placeholders = '';
    }

}