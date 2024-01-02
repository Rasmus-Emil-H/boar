<?php

namespace app\core\src\factories;

use \app\core\src\Controller;
use \app\core\src\miscellaneous\CoreFunctions;

class ControllerFactory extends AbstractFactory {

    protected const CONTROLLER = 'Controller';

    public function create(): Controller {
        $controller = ('\\app\controllers\\' . $this->getHandler() . self::CONTROLLER);
        $this->validateObject($controller);
        $app = CoreFunctions::app();
        return new $controller($app->getRequest(), $app->getResponse(), $app->getSession());
    }

}