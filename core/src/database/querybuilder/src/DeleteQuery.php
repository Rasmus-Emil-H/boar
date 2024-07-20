<?php

namespace app\core\src\database\querybuilder\src;

trait DeleteQuery {

    public function delete(): self {
        $this->upsertQuery($this::DELETE_FROM . $this->table);
        return $this;
    }

    public function truncate(): self {
        $this->upsertQuery($this::TRUNCATE . $this->table);
        return $this;
    }

    // Additional delete-related methods can go here
}
