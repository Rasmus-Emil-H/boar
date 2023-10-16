<?php

/**
 * Bootstrap relations between entities 
 * AUTHOR: RE_WEB
 * @package app\core\relations
*/

namespace app\core\database\relations;

use app\core\Application;

class Relations {

    private const ENTITY_TYPE = 'entityType';
    private const ENTITY_ID   = 'entityID';

    public function __construct() {

    }

    /**
     * RELATIONSHIP SECTION
     * @return \app\models\Entity
    */

    /**
     * Create a new model instance for a related model.
     * @param  string  $class
     * @return mixed
    */

    protected function newRelatedInstance(string $class) {
        app()->classCheck($class);
        return new $class();
    }

    /**
     * Define a one-to-one relationship.
     * @param  string  $related
     * @param  string  $foreignKey
     * @return \core\database\relations\hasOne
    */

    public function hasOne(string $related, string $foreignKey) {
        $instance = $this->newRelatedInstance($related);
    }

    /**
     * Define a one-to-many relationship.
     * @param  string  $related
     * @param  string  $foreignKey
     * @return \core\database\relations\hasMany
    */
    
    public function hasMany(string $related) {
        $instance = $this->newRelatedInstance($related);
        return $instance::search([self::ENTITY_TYPE => $this->getTableName(), self::ENTITY_ID => $this->key()]);
    }

    /*
     * Define a belongs-to relationship.
     * return \[Entity]
    */
    
    public function belongsTo(string $related) {
        $instance = $this->newRelatedInstance($related);
        return $instance::search([self::ENTITY_TYPE => $instance->getTableName(), self::ENTITY_ID => $this->key()]);
    }

}