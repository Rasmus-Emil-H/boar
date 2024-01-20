<?php

/**
|----------------------------------------------------------------------------
| Application entities
|---------------------------------------------------------------------------
| Model extender - This is where models interact with the database
| 
| @author RE_WEB
| @package core\src
|
*/

namespace app\core\src\database;

use \app\core\Application;
use \app\core\src\database\relations\Relations;
use \app\core\src\database\QueryBuilder;
use \app\core\src\database\table\Table;
use \app\core\src\miscellaneous\CoreFunctions;

abstract class Entity extends Relations {

    private $key;
    protected array $data = [];
    protected array $additionalConstructorMethods = [];
    protected Application $app;

    abstract protected function getKeyField()  : string;
    abstract protected function getTableName() : string;
    
    public function __construct($data = null, ?array $allowedFields = null) {
        $this->set($data, $allowedFields);
        $this->app = CoreFunctions::app();
        if ($this->exists()) $this->checkAdditionalConstructorMethods();
    }

    public function checkAdditionalConstructorMethods() {
        if (empty($this->additionalConstructorMethods)) return;
        foreach ($this->additionalConstructorMethods as $method) $this->data[$method] = $this->{$method}();
    }

    public function __call($name, $arguments) {
        throw new \app\core\src\exceptions\NotFoundException("Invalid non static method method [{$name}]");
    }

    public static function __callStatic($name, $arguments) {
        throw new \app\core\src\exceptions\NotFoundException("Invalid static method [{$name}]");
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

    protected function convertData($data = null, array $allowedFields = null) {
        if (is_object($data) === true) $data = (array)$data;
        if (is_array($data) === true) foreach($data as $key => $value) $data[$key] = is_string($value) && trim($value) === '' ? null : $value;
        if (is_string($data) && trim($data) === '') $data = null;
        if ($allowedFields != null) $data = array_intersect_key($data, array_flip($allowedFields));
        return $data;
    }

    public function set($data = null, array $allowedFields = null): Entity {
        $data = $this->convertData($data, $allowedFields);
        $key = $this->getKeyField();
        if ($data !== null && gettype($data) !== "array") $data = [$key => $data];
        if(isset($data[$key])) {
            $exists = $this->getQueryBuilder()->fetchRow([$key => $data[$key]]);
            if(!empty($exists)) {
                $this->setKey($exists->{$this->getKeyField()});
                $this->setData((array)$exists);
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

    public function setData(array $data) {
        $this->data = $data;
    }

    public function save(bool $addMetaData = true): self {
        if ($addMetaData) $this->addMetaData([$this->data]);
        try {
            if ($this->exists()) {
                $this->getQueryBuilder()->patch($this->data, $this->getKeyField(), $this->key())->run('fetch');
                return $this;
            }
            if(empty($this->data)) throw new \app\core\src\exceptions\EmptyException();
            $this->getQueryBuilder()->create($this->data)->run();
            $this->setKey($this->app->getConnection()->getLastID());
            return $this;
        } catch(\Exception $e) {
            $this->app->addSystemEvent([$e->getMessage()]);
            throw new \app\core\src\exceptions\NotFoundException($e->getMessage());
        }
    }

    public function init() {
		return $this->getQueryBuilder()->initializeNewEntity($this->data);
	}

    public function softDelete(): self {
		$this->set([Table::DELETED_AT_COLUMN => new \DateTime('Y-m-d H:i:s')])->save();
        return $this;
	}

    public function restore(): self {
	    $this->set([Table::DELETED_AT_COLUMN => null])->save();
        return $this;
	}

    public function get(string $key): string|bool {
        return $this->data[$key] ?? false; 
    }

    public function all(): array {
        return (new QueryBuilder(get_called_class(), $this->getTableName(), $this->getKeyField()))->select()->run();
    }

    public function getData(): array {
        return $this->data;
    }

    public function query(): QueryBuilder {
        return (new QueryBuilder(get_called_class(), $this->getTableName(), $this->getKeyField()));
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
        if (empty($data)) throw new \InvalidArgumentException('Data can not be empty');
        (new EntityMetaData())
            ->set([
                'EntityType' => $this->getTableName(), 
                'EntityID' => $this->key() ?? 0,
                'Data' => json_encode($data), 
                'IP' => $this->app->getRequest()->getIP()
            ])
            ->save(addMetaData: false);
        return $this;
    }

    public function getMetaData(): QueryBuilder {
        return (new EntityMetaData())->getQueryBuilder();
    }

    protected function allowSave(): void {
        if ($this->exists()) return;
        throw new \app\core\src\exceptions\EmptyException('Entity has not yet been properly stored, did you call this method before ->save() ?');
    }

    public function setStatus(int $status): self {
        if (!$this->get(Table::STATUS_COLUMN)) throw new \app\core\src\exceptions\ForbiddenException('This entity does not have a status');
        $this->set([Table::STATUS_COLUMN => $status])->save();
        return $this;
    }

    public function hasPermissions(string $action) {
		
	}

}