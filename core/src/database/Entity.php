<?php

/**
|----------------------------------------------------------------------------
| Application entities
|----------------------------------------------------------------------------
| Model extender - This is where models interact with the database
| 
| @author RE_WEB
| @package \app\core\src
|
*/

namespace app\core\src\database;

use \app\core\Application;
use \app\core\src\database\relations\Relations;
use app\core\src\miscellaneous\CoreFunctions;
use \app\core\src\traits\EntityQueryTrait;

abstract class Entity extends Relations {

    use EntityQueryTrait;

    private const INVALID_ENTITY_SAVE   = 'Entity has not yet been properly stored, did you call this method before ->save() ?';
    private const INVALID_ENTITY_STATUS = 'This entity does not have a status';
    private const INVALID_ENTITY_DATA   = 'Data can not be empty';

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
        foreach ($this->additionalConstructorMethods as $method)
            if (method_exists($this, $method))
                $this->data[$method] = $this->{$method}();
    }

    public function __call($name, $arguments) {
        throw new \app\core\src\exceptions\NotFoundException("Invalid non static method method [{$name}]");
    }

    public static function __callStatic($name, $arguments) {
        throw new \app\core\src\exceptions\NotFoundException("Invalid static method [{$name}]");
    }

    public function __get(string $key) {
        return $this->data[$key] ??  new \Exception("Invalid entity key");
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
            if ($this->exists()) return $this->patchEntity();
            if(empty($this->data)) throw new \app\core\src\exceptions\EmptyException();
            return $this->createEntity();
        } catch(\Exception $e) {
            $this->app->addSystemEvent([$e->getMessage()]);
            throw new \app\core\src\exceptions\NotFoundException($e->getMessage());
        }
    }

    public function get(string $key): string|bool {
        return $this->data[$key] ?? false; 
    }

    public function getData(): array {
        return $this->data;
    }

    protected function allowSave(): void {
        if (!$this->exists()) throw new \app\core\src\exceptions\EmptyException(self::INVALID_ENTITY_SAVE);
    }

    protected function setTempProperties(array $entityProperties): void {
        $this->set($entityProperties);
    }

}