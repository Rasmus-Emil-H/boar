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
     * Loads a given entity, instantiates a new if none given.
     * @param mixed $data Can be either an array of existing data or an entity ID to load.
     * @return void
    */
    public function __construct($data = null, ?array $allowedFields = null) {
        $this->set($data, $allowedFields);
    }

    /**
     * Sets ones or more properties to a given value.
     * @param array $values key:value pairs of values to set
     * @param array $allowedFields keys of fields allowed to be altered
     * @return object The current entity instance
    */
    public function set($data = null, ?array $allowedFields = null): Entity {
        if(is_object($data) === true) $data = (array) $data;

        if(is_array($data) === true) {
            // Find empty strings in dataset and convert to null instead.
            // JSON fields doesn't allow empty strings to be stored.
            // This also helps against empty strings telling exists(); to return true
            foreach($data as $key => $value)
                $data[$key] = is_string($value) && trim($value) === '' ? null : $value;
        }

        // Again empty strings should be null
        if(is_string($data) && trim($data) === '') $data = null;

        if ($allowedFields != null) $data = array_intersect_key($data, array_flip($allowedFields));
        
        $key = $this->getKeyField();
        
        if ($data !== null && gettype($data) !== "array") $data = [$key => $data];

        if(isset($data[$key])) {
            $exists = Connection::getInstance()->fetchRow($this->getTableName(), [$key => $data[$key]]);

            if(!empty($exists)) {
                $this->key = $exists->$key;
                $this->data = (array)$exists;
                unset($data[$key]);
            }
        }

        if($data === null) $data = [];

        $this->data = array_merge($this->data, $data);
        return $this;
    }

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