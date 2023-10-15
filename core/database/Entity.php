<?php

/**
 * Entity base for models
 * @package app\core\database
 * @author RE_WEB
*/

namespace app\core\database;

use \app\core\Application;
use \app\core\database\relations\Relations;

abstract class Entity extends Relations {

    private   $key;
    protected $data = [];

    /**
     * Related object array
     * @format like: 'objectIdentifier' => Model::class
    */
    // protected array $relatedObjects = [];

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
     * @param array $values key:value pairs of values to set
     * @param array $allowedFields keys of fields allowed to be altered
     * @return object The current entity instance
    */

    public function set($data = null, ?array $allowedFields = null): Entity {
        if(is_object($data) === true) $data = (array) $data;
        if(is_array($data) === true) foreach($data as $key => $value) $data[$key] = is_string($value) && trim($value) === '' ? null : $value;

        if(is_string($data) && trim($data) === '') $data = null;
        if ($allowedFields != null) $data = array_intersect_key($data, array_flip($allowedFields));
        $key = $this->getKeyField();
        if ($data !== null && gettype($data) !== "array") $data = [$key => $data];

        if(isset($data[$key])) {
            $exists = app()->connection->fetchRow($this->getTableName(), [$key => $data[$key]]);
            if(!empty($exists)) {
                $this->key = $exists->{$this->getKeyField()};
                $this->data = (array)$exists;
                unset($this->data[$this->getKeyField()]);
                unset($data[$this->getKeyField()]);
            }
        }
        
        if($data === null) $data = [];

        $this->data = array_merge($this->data, $data);
        return $this;
    }
    
    /**
    * get pk
    * @return bool
    */

    public function key() {
        return $this->key;
    }

    /**
    * Determine if the loaded entity exists in db
    * @return bool
    */

    public function exists(): bool {
        return $this->key !== null;
    }

    /**
    * @return string
    */

    public function save() {
        try {
            if ($this->exists() === true) {
                app()->connection->patch($this->getTableName(), $this->data, $this->getKeyField(), $this->key)->execute('fetch');
                return $this->data;
            } else {
                if(empty($this->data)) throw new \Exception("Data variable is empty");
                app()->connection->create($this->getTableName(), $this->data)->execute();
                $this->key = app()->connection->getLastID();
                return $this->key;
            }
        } catch(\Exception $e) {
            app()->globalThrower($e->getMessage());
        }
    }

    /**
     * Initialize new 
     * @return $this
    */

    public function init() {
		return app()->connection->init($this->getTableName(), $this->data);
	}

    /**
     * Soft delete
     * Don't actually delete the record, but update the delatedAt colmun 
     * @return $this
    */

    public function softDelete(): self {
		$this->deletedAt = new \DateTime(date('Y-m-d H:i:s'));
        $this->save();
        return $this;
	}

    /**
     * Restore
     * Restore object where delatedAt !== null
     * @return $this
    */

    public function restore(): self {
	    $this->deletedAt = null;
        $this->save();
        return $this;
	}

    /**
     * Gets value based on key
     * @param string key
     * @return \Iteratable
    */

    public function get(string $key) {
        return $this->data[$key] ?? "Invalid key: $key"; 
    }

    public static function all() {
        $rows = app()->connection->select(static::tableName, ['*'])->execute();
        return self::load(array_column($rows, static::keyID));
    }

    /** 
     * For those annoying bits
     * Maybe this should be used for executing something cool
     * @return \Exception
    */

    public function __call($name, $arguments) {
        app()->globalThrower("Invalid method [{$name}]");
    }

    /** 
     * For those annoying bits
     * Maybe this should be used for executing something cool
     * @return \Exception
    */

    public static function __callStatic($name, $arguments) {
        app()->globalThrower("Invalid static method [{$name}]");
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

        throw new \Exception("$class::load(); expects either an array or integer. '".gettype($ids)."' was provided.");
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

    public static function search(array $criterias, array $values = ['*'], array $additionalQueryBuilding = []): array {
        $rows = app()->connection->select(static::tableName, $values)->whereClause($criterias);
        foreach ( $additionalQueryBuilding as $key => $value ) $rows = $rows->{$key}($value);
        $rows = $rows->execute();
        return self::load(array_column($rows, static::keyID));
    }

    /**
     * @param string key
     * @return mixed 
    */

    public function getRelatedObject(string $key): string {
		return $this->relatedObjects[$key] ?? app()->globalThrower('Invalid relation');
	}

    /**
     * Delete obj based on primary
     * @return 
    */

    public function delete() {
        return app()->connection->delete($this->getTableName())->where([$this->getKeyField() => $this->key()])->execute();
    }

    /**
     * Model debugging
     * @return string
    */

    public function __toString() {
        $result = get_class($this)."($this->key:\n";
        foreach ($this->data as $key => $value) $result .= "[$key]:$value\n";
        return $result;
    }

}
