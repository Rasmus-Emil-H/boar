<?php

/**
 * Entity base for models
 * @package app\core\database
 * @author RE_WEB
 */

namespace app\core\database;

use \app\core\database\relations\Relations;
use \app\core\database\QueryBuilder;
use \app\core\database\table\Table;

abstract class Entity extends Relations {

    private   $key;
    protected $data = [];

    abstract protected function getKeyField()  : string;
    abstract protected function getTableName() : string;
    
    public function __construct($data = null, ?array $allowedFields = null) {
        $this->set($data, $allowedFields);
    }

    public function __call($name, $arguments) {
        throw new \app\core\exceptions\NotFoundException("Invalid non static method method [{$name}]");
    }

    public static function __callStatic($name, $arguments) {
        throw new \app\core\exceptions\NotFoundException("Invalid static method [{$name}]");
    }

    public function __get(string $key) {
        return $this->data[$key] ?? new \Exception("Invalid entity key");
    }

    public function __toString() {
        $result = get_class($this)."($this->key):\n";
        foreach ($this->data as $key => $value) $result .= "[$key]:$value\n";
        return $result;
    }

    /**
     * @param array $values key:value pairs of values to set
     * @param array|null $allowedFields keys of fields allowed to be altered
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

    protected function setKey(string $key): void {
        $this->key = $key;
    }

    public function key() {
        return $this->key;
    }

    public function exists(): bool {
        return $this->key !== null;
    }

    public function save(bool $addMetaData = true): string {
        if ($addMetaData) $this->addMetaData([$this->data]);
        try {
            if ($this->exists()) {
                $this->getQueryBuilder()->patch($this->data, $this->getKeyField(), $this->key())->run('fetch');
                return $this->data;
            }
            if(empty($this->data)) throw new \app\core\exceptions\EmptyException();
            $this->getQueryBuilder()->create($this->data)->run();
            $this->setKey(app()->connection->getLastID());
            return $this->key;
        } catch(\Exception $e) {
            app()->addSystemEvent([$e->getMessage()]);
            throw new \app\core\exceptions\NotFoundException($e->getMessage());
        }
    }

    public function init() {
		return $this->getQueryBuilder()->new($this->data);
	}

    public function softDelete(): self {
		$this->set([Table::DELETED_AT_COLUMN => new \DateTime('Y-m-d H:i:s')])->save();
        return $this;
	}

    public function restore(): self {
	    $this->set([Table::DELETED_AT_COLUMN => null])->save();
        return $this;
	}

    public function get(string $key) {
        return $this->data[$key] ?? "Invalid key: $key"; 
    }

    public static function all() {
        return (new QueryBuilder(get_called_class(), static::tableName, static::keyID))->select()->run();
    }

    public function getData(): array {
        return $this->data;
    }

    public static function query(): QueryBuilder {
        return (new QueryBuilder(get_called_class(), static::tableName, static::keyID));
    }

    public function delete() {
        return $this->getQueryBuilder()->delete()->where([$this->getKeyField() => $this->key()])->run();
    }

     public function truncate() {
        return $this->getQueryBuilder()->delete()->run();
    }

     public function trashed() {
        return $this->getQueryBuilder()->select()->where([Table::DELETED_AT_COLUMN => 'IS NOT NULL'])->run();
    }

    public function getQueryBuilder(): QueryBuilder {
        return (new QueryBuilder(get_called_class(), $this->getTableName(), $this->getKeyField()));
    }

    public function addMetaData(array $data): self {
        (new EntityMetaData())
            ->set([
                'EntityType' => $this->getTableName(), 
                'EntityID' => $this->key(), 
                'Data' => json_encode($data), 
                'IP' => app()::isCLI() ? php_sapi_name() : app()->request->getIP()
            ])
            ->save(addMetaData: false);
        return $this;
    }

    public function setStatus(int $status): self {
        $this->set([Table::STATUS_COLUMN => $status])->save();
        return $this;
    }

}