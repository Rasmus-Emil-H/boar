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
    
    public function __construct($data = null, ?array $allowedFields = null) {
        $this->set($data, $allowedFields);
    }

    /**
     * @param array $values key:value pairs of values to set
     * @param array $allowedFields keys of fields allowed to be altered
     * @return object The current entity instance
     */

    public function set($data = null, array $allowedFields = null): Entity {
        if(is_object($data) === true) $data = (array) $data;
        if(is_array($data) === true) foreach($data as $key => $value) $data[$key] = is_string($value) && trim($value) === '' ? null : $value;

        if(is_string($data) && trim($data) === '') $data = null;
        if ($allowedFields != null) $data = array_intersect_key($data, array_flip($allowedFields));
        $key = $this->getKeyField();
        if ($data !== null && gettype($data) !== "array") $data = [$key => $data];
        if(isset($data[$key])) {
            $exists = $this->getQueryBuilder()->fetchRow([$key => $data[$key]]);
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
                $this->getQueryBuilder()->patch($this->data, $this->getKeyField(), $this->key)->run('fetch');
                return $this->data;
            }
            if(empty($this->data)) throw new \Exception("Data variable is empty");
            $this->getQueryBuilder()->create($this->data)->run();
            $this->key = app()->connection->getLastID();
            return $this->key;
        } catch(\Exception $e) {
            throw new \app\core\exceptions\NotFoundException($e->getMessage());
        }
    }

    public function init() {
		return $this->getQueryBuilder()->new($this->data);
	}

    public function softDelete(): self {
		$this->set(['DeletedAt' => new \DateTime('Y-m-d H:i:s')])->save();
        return $this;
	}

    public function restore(): self {
	    $this->set(['DeletedAt' => null])->save();
        return $this;
	}

    public function get(string $key) {
        return $this->data[$key] ?? "Invalid key: $key"; 
    }

    public static function all() {
        return (new QueryBuilder(get_called_class(), static::tableName, static::keyID))->select()->run();
    }

    public function __call($name, $arguments) {
    throw new \app\core\exceptions\NotFoundException("Invalid non static method method [{$name}]");
    }

    public static function __callStatic($name, $arguments) {
        throw new \app\core\exceptions\NotFoundException("Invalid static method [{$name}]");
    }

    public function getData(): array {
        return $this->data;
    }

    public function __get(string $key) {
        return $this->data[$key] ?? new \Exception("Invalid entity key");
    }

    public static function query(): QueryBuilder {
        return (new QueryBuilder(get_called_class(), static::tableName, static::keyID));
    }

    public function getRelatedObject(string $key): string {
		return $this->relatedObjects[$key] ?? throw new \app\core\exceptions\NotFoundException("$key was not found on this entity.");
	}

    public function delete() {
        return $this->getQueryBuilder()->delete()->where([$this->getKeyField() => $this->key()])->run();
    }

     public function truncate() {
        return $this->getQueryBuilder()->delete()->run();
    }

     public function trashed() {
        return $this->getQueryBuilder()->select()->where(['DeletedAt' => 'IS NOT NULL'])->run();
    }

    public function getQueryBuilder(): QueryBuilder {
        return (new QueryBuilder(get_called_class(), $this->getTableName(), $this->getKeyField()));
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