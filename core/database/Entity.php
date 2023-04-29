<?php

/**
 * Entity base for models
 * @package app\core\database
 * @author RE_WEB
*/

namespace app\core\database;

use \app\core\Application;

abstract class Entity {

    private   $key;
    protected $data = [];

    abstract protected function getKeyField()  : string;
    abstract protected function getTableName() : string;

    /**
     * Gets obj based on current model
     * @return \Iteratable
    */

    public function get() {
        return Application::$app->connection->select($this->getTableName(), ['*'])->execute($this);
    }

    public static function all() {
        return Application::$app->connection->select(static::tableName, ['*'])->execute();
    }

    /**
    * Gets the current entity data
    * @return array
    */
    public function getData(): array {
        return $this->data;
    }

    /**
     * Search for model with specific criteria
     * @return \Iteratable
    */

    public function search(array $criterias) {
        return Application::$app->connection->select($this->getTableName(), ['*'])->execute($this);
    }

    /**
     * Delete obj based on PK
     * @return 
    */

    public function delete() {
        return Application::$app->connection
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