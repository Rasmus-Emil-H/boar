<?php

/**
 * Bootstrap relations between entities 
 * AUTHOR: RE_WEB
 * @package app\core\src\database\relations
 */

namespace app\core\src\database\relations;

use \app\core\src\database\QueryBuilder;

class Relations {

    protected function getInstanceOf(string $class) {
        app()->classCheck($class);
        return new $class();
    }

    public function hasOne(string $related, string $foreignKey) {
        $instance = $this->getInstanceOf($related);
    }
    
    public function hasMany(string $related): QueryBuilder {
        $instance = $this->getInstanceOf($related);
        return $instance::query()->select()->where([$this->getKeyField() => $this->key()]);
    }
    
    public function belongsTo(string $related) {
        $instance = $this->getInstanceOf($related);
        return $instance::query();
    }

}