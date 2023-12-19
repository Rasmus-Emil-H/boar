<?php

namespace app\core\factories;

use app\core\Controller;

class ControllerFactory implements FactoryInterface {

    protected const CONTROLLER   = 'Controller';

    private array $arguments;

    public function __construct(array $arguments = []) {
        $this->arguments = $arguments;
    }

    public function create(): Controller {
        $controllerClassName = '\\app\controllers\\' . getIndex($this->arguments, 'handler')->scalar . self::CONTROLLER;
        app()->classCheck($controllerClassName);
        return new $controllerClassName();
    }

}