<?php

/**
 * Entity base for models
 * @package app\core\database
 * @author RE_WEB
*/

namespace app\core\database;

use \app\core\database\relations\Relations;
use \app\core\database\QueryBuilder;

abstract class Entity extends Relations {

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
            $exists = (new QueryBuilder($this->getTableName()))->fetchRow([$key => $data[$key]]);
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

    public function key() {
        return $this->key;
    }

    public function exists(): bool {
        return $this->key !== null;
    }

    public function save() {
        try {
            if ($this->exists() === true) {
                (new QueryBuilder($this->getTableName()))
                    ->patch($this->data, $this->getKeyField(), $this->key)
                    ->run('fetch');
                return $this->data;
            }
            if(empty($this->data)) throw new \Exception("Data variable is empty");
            (new QueryBuilder($this->getTableName()))->create($this->data)->run();
            $this->key = app()->connection->getLastID();
            return $this->key;
        } catch(\Exception $e) {
            app()->globalThrower($e->getMessage());
        }
    }

    /**
     * Initialize new 
     * @return $this
     */

    public function init() {
		return (new QueryBuilder($this->getTableName()))->init($this->data);
	}

    /**
     * Soft delete
     * Don't actually delete the record, but update the delatedAt colmun 
     * @return $this
     */

    public function softDelete(): self {
		$this
            ->set(['DeletedAt' => new \DateTime('Y-m-d H:i:s')])
            ->save();
        return $this;
	}

    /**
     * Restore
     * Restore object where delatedAt !== null
     * @return $this
     */

    public function restore(): self {
	    $this
            ->set(['DeletedAt' => null])
            ->save();
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
        $rows = (new QueryBuilder(static::tableName))
            ->select(['*'])
            ->run();
        return self::load(array_column($rows, static::keyID));
    }

    /** 
     * @throw \Exception
     */

    public function __call($name, $arguments) {
        app()->globalThrower("Invalid method [{$name}]");
    }

    /** 
     * @throw \Exception
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

    public static function load(array|int $ids) {
        $class = get_called_class();

        if (is_array($ids)) {
            $objects = [];
            foreach($ids as $id) $objects[$id] = new $class($id);
            return $objects;
        } else if (is_numeric($ids)) {
            return new $class((int) $ids);
        }

        throw new \app\core\exceptions\InvalidTypeException("$class::load(); expects either an array or integer. '".gettype($ids)."' was provided.");
    }

    public function getData(): array {
        return $this->data;
    }

    public function __get(string $key) {
        return $this->data[$key] ?? new \Exception("Invalid key");
    }

    /**
     * Search current entity
     * @return \Iteratable
     */

    public static function search(array $criterias, array $values = ['*'], array $sqlClauses = []): array {
        $rows = (new QueryBuilder(static::tableName))->select($values)->where($criterias);
        foreach ($sqlClauses as $key => $value) $rows = $rows->{$key}($value);
        $rows = $rows->run();
        return self::load(array_column($rows, static::keyID));
    }

    public function getRelatedObject(string $key): string {
		return $this->relatedObjects[$key] ?? throw new \app\core\exceptions\NotFoundException("$key was not found on this entity.");
	}

    public function delete() {
        return (new QueryBuilder($this->getTableName()))
            ->delete($this->getTableName())
            ->where([$this->getKeyField() => $this->key()])
            ->run();
    }

     public function truncate() {
        return (new QueryBuilder($this->getTableName()))
            ->delete()
            ->run();
    }

     public function trashed() {
        return (new QueryBuilder($this->getTableName()))
            ->select(['*'])
            ->where(['DeletedAt' => 'IS NOT NULL'])
            ->run();
    }

    public function __toString() {
        $result = get_class($this)."($this->key):\n";
        foreach ($this->data as $key => $value) $result .= "[$key]:$value\n";
        return $result;
    }

    public function addMetaData(array $data): self {
        (new EntityMetaData())
            ->set(['EntityType' => $this->getTableName(), 'EntityID' => $this->key(), 'Data' => json_encode($data)])
            ->save();
        return $this;
    }

    public function setStatus(int $status): self {
        $this
            ->set(['Status' => $status])
            ->save();
        return $this;
    }

}
