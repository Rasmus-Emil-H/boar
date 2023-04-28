<?php

/**
* Entity base for models
* @author RE_WEB
*/

namespace app\core\database;

abstract class Entity {

    abstract protected function getKeyField()  : string;
    abstract protected function getTableName() : string;

     /**
     * Gets obj based on current model
     * @return $this
    */

    public function get() {
        return \app\core\Application::$app->connection
            ->select($this->getTableName(), ['*'])
            ->execute();
    }

}