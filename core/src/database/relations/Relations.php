<?php

/**
|----------------------------------------------------------------------------
| Entity relations
|----------------------------------------------------------------------------
| Describe x-x relations
| 
| @author RE_WEB
| @package core
|
*/

namespace app\core\src\database\relations;

use \app\core\src\database\QueryBuilder;
use \app\core\src\miscellaneous\CoreFunctions;

class Relations {

    protected function getInstanceOf(string $class) {
        CoreFunctions::app()->classCheck($class);
        return new $class();
    }
    
    public function hasMany(string $related): QueryBuilder {
        $instance = $this->getInstanceOf($related);
        return $instance->query()->select()->where([$this->getKeyField() => $this->key()]);
    }
    
    public function belongsTo(string $related) {
        $instance = $this->getInstanceOf($related);
        return $instance->find($this->getKeyField(), $this->key());
    }

    public function pivot(...$keys) {
        $queryBuilder = new QueryBuilder(get_called_class(), $this->getPivot(), '');
        $queryBuilder->create(CoreFunctions::first($keys))->run();
    }

    public function manyToMany(string $relatedEntity): array {
        $queryBuilder = new QueryBuilder($relatedEntity, $this->getPivot(), '');
        return $queryBuilder->select()->where([$this->getKeyField() => $this->key()])->run();
    }

}