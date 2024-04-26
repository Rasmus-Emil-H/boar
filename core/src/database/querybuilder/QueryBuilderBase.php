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
    public const INNERJOIN   = ' INNER JOIN ';
    public const SUBQUERY_OPEN  = ' ( ';
    public const SUBQUERY_CLOSE = ' ) ';

    protected const SQL_DESCRIBE = ' DESCRIBE ';
    protected const GROUP_BY     = ' GROUP BY ';
    protected const ORDER_BY     = ' ORDER BY ';
    protected const DEFAULT_ASCENDING_ORDER = ' ASC ';
    protected const DEFAULT_DESCENDING_ORDER = ' DESC ';

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