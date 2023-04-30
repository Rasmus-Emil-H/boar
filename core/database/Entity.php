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

        if(is_string($data) && trim($data) === '') $data = null;

        if ($allowedFields != null) $data = array_intersect_key($data, array_flip($allowedFields));
        
        $key = $this->getKeyField();
        
        if ($data !== null && gettype($data) !== "array") $data = [$key => $data];

        if(isset($data[$key])) {
            $exists = Application::$app->connection->fetchRow($this->getTableName(), [$key => $data[$key]]);

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
    * Determine if the loaded entity exists in db
    * @return bool
    */
    public function exists() : bool {
        return $this->key !== null;
    }

    /**
    * Saves the entity to a long term storage.
    * Empty strings are converted to null values
    * @return mixed if a new entity was just inserted, returns the primary key for that entity, otherwise the current data is returned
    */
    public function save() {
        try {
            if ($this->exists() === true) {
                Application::$app->connection->update($this->getTableName(), $this->data, $this->getKeyFilter());
                return $this->data;
            } else {
                if(empty($this->data)) throw new Exception("Data variable is empty");
                $this->key = Application::$app->connection->create($this->getTableName(), $this->data);
                return $this->key;
            }
        } catch(Exception $e) {
            throw $e;
        }
    }

    /**
     * Gets obj based on current model
     * @return \Iteratable
    */

    public function get() {
        return Application::$app->connection->select($this->getTableName(), ['*'])->execute($this);
    }

    public static function all() {
        $rows = Application::$app->connection->select(static::tableName, ['*'])->execute();
        return self::load(array_column($rows, static::keyID));
    }

    /**
    * Load one or more ID's into entities
    * @param mixed $ids an array of ID's or an integer to load
    * @return mixed The loaded entities
    * @throws Exception
    */
    public static function load($ids) {
        $class = get_called_class();

        if(is_array($ids)) {
            $objects = [];
            foreach($ids as $id) $objects[$id] = new $class($id);
            return $objects;
        } else if(is_numeric($ids)) {
            return new $class((int) $ids);
        }

        throw new Exception($class."::load(); expects either an array or integer. '".gettype($ids)."' was provided.");
    }

    /**
    * Gets the current entity data
    * @return array
    */
    public function getData(): array {
        return $this->data;
    }

    /**
    * Get value based on key
    * @return array
    */
    public function __get(string $key) {
        return $this->data[$key] ?? new \Exception("Invalid key");
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