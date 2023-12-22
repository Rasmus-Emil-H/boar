<?php

namespace app\core\src\factories;

use \app\core\src\Controller;

class ControllerFactory extends AbstractFactory {

    public function create(): Controller {
        $controllerName = (getIndex($this->arguments, 'handler')->scalar);
        $controller = '\\app\controllers\\' . $controllerName . self::CONTROLLER;
        app()->classCheck($controller);
        return new $controller(app()->getRequest(), app()->getResponse(), app()->getSession());
    }

}