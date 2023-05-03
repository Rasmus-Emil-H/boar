<?php

/*******************************
 * Bootstrap relations between entities 
 * AUTHOR: RE_WEB
 * @package app\core\relations
*/

namespace app\core\database\relations;

class Relations {

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
    protected function newRelatedInstance($class) {
        $class = '\app\\models\\'.$class.'Model';
        $static = new $class();
        var_dump($static);
    }

    /**
     * Define a one-to-one relationship.
     * @param  string  $related
     * @param  string  $foreignKey
     * @return \core\database\hasOne
     */
    public function hasOne($related, $foreignKey) {
        $instance = $this->newRelatedInstance($related);
    }

    /**
     * Define a one-to-many relationship.
     * @param  string  $related
     * @param  string  $foreignKey
     * @return \core\database\hasMany
     */
    public function hasMany($related, $foreignKey) {
        $instance = $this->newRelatedInstance($related);
    }

}