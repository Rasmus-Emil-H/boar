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
     * @return \Iteratable
    */

    public function get() {
        return \app\core\Application::$app->connection
            ->select($this->getTableName(), ['*'])
            ->execute();
    }

    /**
     * Model debgging
     * @return string
    */
    public function __toString() {
        $result = get_class($this)."(".$this->key."):\n";
        foreach ($this->data as $key => $value) $result .= " [".$key."] ".$value."\n";
        return $result;
    }

}