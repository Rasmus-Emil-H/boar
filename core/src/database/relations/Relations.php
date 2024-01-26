<?php

/**
|----------------------------------------------------------------------------
| Entity relations
|----------------------------------------------------------------------------
| Describes the relationships betwen entities
| 
| @author RE_WEB
| @package \app\core\src\database
|
*/

namespace app\core\src\database\relations;

use \app\core\src\database\QueryBuilder;
use app\core\src\database\table\Table;
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

    public function connectedWith(string $relatedEntity, string $table) {
        $queryBuilder = new QueryBuilder($relatedEntity, $table, '');
        return $queryBuilder->select()->where([$this->getKeyField() => $this->key()])->run();
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

    public function oneHasMany(string $class, string $table, string $column, string $value): array {
        $queryBuilder = new QueryBuilder($class, $table, $this->key());
        return $queryBuilder->select()->where([$column => $value])->run();
    }

    public function hasManyPolymorphic(string $class, string $table) {
        $queryBuilder = new QueryBuilder($class, $table, $this->key());
        return $queryBuilder->select()->where([Table::ENTITY_TYPE_COLUMN => $this->getKeyField(), Table::ENTITY_ID_COLUMN => $this->key()])->run(); 
    }

}