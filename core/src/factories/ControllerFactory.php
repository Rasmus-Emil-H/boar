<?php

namespace app\core\src\factories;

use \app\core\src\Controller;

class ControllerFactory implements FactoryInterface {

    protected const CONTROLLER = 'Controller';

    private array $arguments;

    public function __construct(array $arguments = []) {
        $this->arguments = $arguments;
    }

    public function create(): Controller {
        $controller = '\\app\controllers\\' . getIndex($this->arguments, 'handler')->scalar . self::CONTROLLER;
        app()->classCheck($controller);
        return new $controller();
    }

}