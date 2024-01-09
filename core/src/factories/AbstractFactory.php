<?php

/**
|----------------------------------------------------------------------------
| Factory base
|----------------------------------------------------------------------------
|
| @author RE_WEB
| @package core\src\factories
|
*/

namespace app\core\src\factories;

use \app\core\src\miscellaneous\CoreFunctions;

abstract class AbstractFactory {

    public function __construct(
        protected array $arguments = []
    ) {
        
    }
    
    abstract public function create();

    public function getHandler(): string {
        return CoreFunctions::getIndex($this->arguments, 'handler')->scalar;
    }

    public function validateObject(string $class) {
        return CoreFunctions::app()->classCheck($class);
    }

}