<?php

namespace app\core\src\factories;

class MigrationFactory extends AbstractFactory {

    protected const CONTROLLER = 'Controller';

    public function create(): object {
        $controller = $this->getHandler();
        $this->validateObject($controller);
        return new $controller();
    }

}