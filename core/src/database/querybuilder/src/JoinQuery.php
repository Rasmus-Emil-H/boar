<?php

namespace app\core\src\database\querybuilder\src;

trait JoinQuery {

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

    // Additional join-related methods can go here
}
