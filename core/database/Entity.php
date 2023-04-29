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

    /**
     * Gets the value for a given property name
     * @param string $name name of the property from whom to retrieve a value
     * @return mixed A property value
    * @throws Exception
    */
    public function __get(string $name) {
        if ($name == $this->getKeyField())
            throw new Exception("Cannot return key field from getter, try calling ".get_called_class()."::id(); in object context instead.");

        return $this->data[$name];
    }

    /**
     * Saves the entity to a long term storage.
     * Empty strings are converted to null values
     * @return mixed if a new entity was just inserted, returns the primary key for that entity, otherwise the current data is returned
    */
    public function save() {
        try {
            if ($this->exists() === true) {
                Connection::getInstance()->update($this->getTableName(), $this->data, $this->getKeyFilter());
                return $this->data;
            } else {
                if(empty($this->data)) throw new Exception("Data variable is empty");
                $this->key = Connection::getInstance()->insert($this->getTableName(), $this->data);
                return $this->key;
            }
        } catch(Exception $e) {
            throw $e;
        }
    }

}