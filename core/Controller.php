<?php

/*******************************
 * Bootstrap Controller 
 * AUTHOR: RE_WEB
 * @package app\core\Controller
*/

namespace app\core;

use \app\core\middlewares\AuthMiddleware;

class Controller {

    public string $layout = 'main';

    /**
     * Array of middleware classes
     * @var app\core\middlewares\Middleware[]
    */
    protected array $middlewares = [];

    /**
     * @var string $currentAction 
    */
    public string $action = '';

    public function render(string $view, array $params = array()) {
        return Application::$app->view->renderView($view, $params);
    }

    public function setLayout(string $layout) {
        $this->layout = $layout;
    }

    public function registerMiddleware(AuthMiddleware $middleware) {
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares(): array {
        return $this->middlewares;
    }

}