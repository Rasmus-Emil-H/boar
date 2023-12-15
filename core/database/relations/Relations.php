<?php

/**
 * Bootstrap relations between entities 
 * AUTHOR: RE_WEB
 * @package app\core\relations
 */

namespace app\core\database\relations;

use app\core\database\QueryBuilder;

class Relations {

    protected function getInstanceOf(string $class) {
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
        $instance = $this->getInstanceOf($related);
    }

    /**
     * Define a has-many relationship.
     * @param  string  $related
     * @param  string  $foreignKey
     * @return \core\database\relations\hasMany
     */
    
    public function hasMany(string $related): QueryBuilder {
        $instance = $this->getInstanceOf($related);
        return $instance::query();
    }

    /*
     * Define a belongs-to relationship.
     * return \[Entity]
     */
    
    public function belongsTo(string $related) {
        $instance = $this->getInstanceOf($related);
        return $instance::query();
    }

}