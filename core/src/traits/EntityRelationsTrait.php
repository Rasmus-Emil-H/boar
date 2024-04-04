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

namespace app\core\src\traits;

use \app\core\src\database\QueryBuilder;
use \app\core\src\database\table\Table;
use \app\core\src\miscellaneous\CoreFunctions;
use \app\core\src\factories\ModelFactory;

trait EntityRelationsTrait {

    /**
     * Return entity
     */

    private function getInstanceOf(string $class) {
        $class = preg_replace('/Model/', '', CoreFunctions::last(explode('\\', $class))->scalar);
        return (new ModelFactory(['handler' => $class]))->create();
    }

    /**
     * Find entities on the child table where the parent has a corresponding primary key
     */
    
    public function hasMany(string $related): QueryBuilder {
        $instance = $this->getInstanceOf($related);
        return $instance->query()->select()->where([$this->getKeyField() => $this->key()]);
    }

    /**
     * Find entity on the parent where the child has a param primary key
     */

    public function hasOne(string $entity, string $entityKey) {
        $instance = $this->getInstanceOf($entity);
        $queryBuilder = new QueryBuilder($entity, $this->getTableName(), $this->key());
        return $queryBuilder->select()->where([$instance->getKeyField() => $entityKey])->run();
    }

    /**
     * Find entities on a param table where the key and value is a match
     */

    public function attachedTo($entity, string $table, string $key, string $value) {
        $queryBuilder = new QueryBuilder($entity, $table, $key);
        return $queryBuilder->select()->where([$key => $value])->run();
    }

    /**
     * Find entities on a param table where the parent primary key exist
     */

    public function connectedWith(string $relatedEntity, string $table) {
        $queryBuilder = new QueryBuilder($relatedEntity, $table, '');
        return $queryBuilder->select()->where([$this->getKeyField() => $this->key()])->run();
    }

    /**
     * Find entities on param entity where parent key is a match
     */
    
    public function belongsTo(string $related) {
        $instance = $this->getInstanceOf($related);
        return $instance->find($this->getKeyField(), $this->key());
    }

    /**
     * Find entity on parent table where the param key is a match
     */

    public function isBasedOn(string $relatedEntity, string $key) {
        $queryBuilder = new QueryBuilder($relatedEntity, $this->getTableName(), $this->key());
        return $queryBuilder->select()->where([$this->getKeyField() => $key])->run();
    }

    /**
     * Create a pivot relation with N amount of KVPs
     */

    public function createPivot(...$keys) {
        $queryBuilder = new QueryBuilder(get_called_class(), $this->getPivot(), '');
        $queryBuilder->create(CoreFunctions::first($keys))->run();
        return app()->getConnection()->getLastID();
    }

    /**
     * Create a pivot relation with N amount of KVPs
     */

     public function createCustomPivot($table, ...$keys) {
        $queryBuilder = new QueryBuilder(get_called_class(), $table, '');
        return $queryBuilder->create(CoreFunctions::first($keys))->run();
    }

    /**
     * Find entites on pivot table where parent primary key is a match
     */

    public function manyToMany(string $relatedEntity): array {
        $queryBuilder = new QueryBuilder($relatedEntity, $this->getPivot(), '');
        return $queryBuilder->select()->where([$this->getKeyField() => $this->key()])->run();
    }

    /**
     * Target specific pivot
     */

    public function hasManyToMany(string $relatedEntity, string $pivot) {
        $querBuilder = new QueryBuilder($relatedEntity, $pivot, '');
        return $querBuilder->select()->where([Table::ENTITY_TYPE_COLUMN => $this->getTableName(), Table::ENTITY_ID_COLUMN => $this->key()]);
    }

    /**
     * Find entities on a table where the column and value is a match
     */

    public function oneHasMany(string $class, string $table, string $column, string $value): QueryBuilder {
        $queryBuilder = new QueryBuilder($class, $table, $this->key());
        return $queryBuilder->select()->where([$column => $value]);
    }

    /**
     * Find entites on a polymorphic table where parent entity and primary key is match
     */

    public function hasManyPolymorphic(string $class) {
        $polyMorphicEntity = $this->getInstanceOf($class);
        return $polyMorphicEntity->search([Table::ENTITY_TYPE_COLUMN => $this->getTableName(), Table::ENTITY_ID_COLUMN => $this->key()]);
    }

    /**
     * Remove specific relation
     */

    public function deleteRelation(array $keys) {
        $queryBuilder = new QueryBuilder($this, $this->getPivot(), $this->key());
        return $queryBuilder->delete()->where($keys)->run();
    }

}