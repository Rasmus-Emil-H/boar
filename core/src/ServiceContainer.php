<?php

/**
|----------------------------------------------------------------------------
| Bootstrap service container
|----------------------------------------------------------------------------
|
| @author RE_WEB
| @package app\core\src
|
*/


namespace app\core\src;

use \app\core\src\exceptions\InvalidTypeException;

class ServiceContainer {

    private array $services = [];
    private array $instances = [];

    private const INVALID_SERVICE = 'Invalid service';

    public function register(string $name, callable $factory): void {
        $this->services[$name] = $factory;
    }

    public function get(string $name): mixed {
        $this->validateAndBootstrap($name);

        return $this->instances[$name];
    }

    private function validateAndBootstrap(string $name): void {
        if (!isset($this->services[$name])) throw new InvalidTypeException(self::INVALID_SERVICE);
        
        if (!isset($this->instances[$name])) $this->instances[$name] = ($this->services[$name])($this);
    }

}