<?php

/*******************************
 * Bootstrap relations between entities 
 * AUTHOR: RE_WEB
 * @package app\core\relations
*/

namespace app\core\database\relations;

use app\core\Application;

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
    protected function newRelatedInstance(string $class) {
        Application::$app->classCheck($class);
        return new $class();
    }

    /**
     * Define a one-to-one relationship.
     * @param  string  $related
     * @param  string  $foreignKey
     * @return \core\database\relations\hasOne
     */
    public function hasOne($related, $foreignKey) {
        $instance = $this->newRelatedInstance($related);
    }

    /**
     * Define a one-to-many relationship.
     * @param  string  $related
     * @param  string  $foreignKey
     * @return \core\database\relations\hasMany
     */
    public function hasMany($related) {
        $instance = $this->newRelatedInstance($related);
        return $instance::search(['entityID' => $this->key]);
    }

}