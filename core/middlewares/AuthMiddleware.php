<?php

/*******************************
 * Bootstrap AuthMiddleware 
 * AUTHOR: RE_WEB
 * @package app\core\AuthMiddleware
*/

namespace app\core\middlewares;

use app\core\exceptions\ForbiddenException;
use app\core\Application;

class AuthMiddleware extends Middleware {

    public array $actions = [];

    public function __construct(array $actions = []) {
        $this->actions = $actions;
    }

    public function execute() {
        if (Application::$app->authentication->isGuest()) 
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) 
                throw new ForbiddenException();
    }

}