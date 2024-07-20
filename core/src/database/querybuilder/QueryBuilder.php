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

use \app\core\src\miscellaneous\CoreFunctions;
use app\core\src\database\querybuilder\src\Constants;

class QueryBuilder extends QueryBuilderBase {

    use src\SelectQuery;
    use src\DeleteQuery;
    use src\InsertQuery;
    use src\UpdateQuery;
    use src\JoinQuery;
    use src\AggregateQuery;
    use src\PartitionQuery;

    public function getComparisonOperators(): array {
        return Constants::COMPARISON_OPERATORS;
    }

    public function subQuery(\Closure $callback): self {
        $this->upsertQuery(Constants::SUBQUERY_OPEN);
        call_user_func($callback, $this);
        $this->upsertQuery(Constants::SUBQUERY_CLOSE);
        return $this;
    }

    public function describeTable() {
        $this->upsertQuery(Constants::SQL_DESCRIBE . $this->table);
        $this->run();
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