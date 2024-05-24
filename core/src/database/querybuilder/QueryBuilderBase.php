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

    public const WHERE       = ' WHERE ';
    public const AND         = ' AND ';
    public const BIND        = ' = :';
    public const INNER_JOIN   = ' INNER JOIN ';
    public const SUBQUERY_OPEN  = ' ( ';
    public const SUBQUERY_CLOSE = ' ) ';

    public const WITH = ' WITH ';
    public const AS = ' AS ';
    public const DELETE_FROM = ' DELETE FROM ';

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

    protected array $args = [];

    protected array $comparisonOperators = ['=', '<>', '!=', '>', '<', '>=', '<='];
    
    public function __construct(
        public string $class, 
        public string $table, 
        public string $keyID
    ) {
        $this->resetQuery();
    }

    public function upsertQuery(string $query): void {
        $this->query .= $query;
    }

    public function updateQueryArguments($key, $value): void {
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