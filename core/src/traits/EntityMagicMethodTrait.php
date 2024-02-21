<?php

/**
|----------------------------------------------------------------------------
| Magic methods
|----------------------------------------------------------------------------
|
*/

namespace app\core\src\traits;

trait EntityMagicMethodTrait {

    private const INVALID_ENTITY_KEY    = 'Invalid entity key';
    private const INVALID_ENTITY_STATIC_METHOD = 'Invalid static method';
    private const INVALID_ENTITY_METHOD = 'Invalid non static method method';

    public function __call($name, $arguments) {
        throw new \app\core\src\exceptions\NotFoundException(self::INVALID_ENTITY_METHOD . "[{$name}]");
    }

    public static function __callStatic($name, $arguments) {
        throw new \app\core\src\exceptions\NotFoundException(self::INVALID_ENTITY_STATIC_METHOD . " [{$name}]");
    }

    public function __get(string $key) {
        return $this->getData()[$key] ?? new \Exception(self::INVALID_ENTITY_KEY);
    }

    public function __toString() {
        $result = get_class($this)."($this->key):\n";
        foreach ($this->getData() as $key => $value) $result .= "[$key]:$value\n";
        return $result;
    }

}