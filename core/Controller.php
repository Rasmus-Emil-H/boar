<?php

/*******************************
 * Bootstrap Controller 
 * AUTHOR: RE_WEB
 * @package app\core\Controller
*/

namespace app\core;

use \app\core\middlewares\Middleware;

class Controller {

    public string $layout = 'main';

    public const MODEL_PREFIX = '\\app\\models\\';
    
    /**
     * Array of middleware classes
     * @var app\core\middlewares\Middleware[]
    */
    protected array $middlewares = [];

    /**
     * @var string $currentAction 
    */
    public string $action = '';

    public function render(string $view, array $params = []) {
        echo Application::$app->view->renderView($view, $params);
    }

    public function setLayout(string $layout) {
        $this->layout = $layout;
    }

    public function registerMiddleware(Middleware $middleware) {
        $this->middlewares[] = $middleware;
    }   

    public function getMiddlewares(): array {
        return $this->middlewares;
    }

    public function init() {
        $prefix = self::MODEL_PREFIX.ucfirst(Application::$app->request->getPHPInput()).'Model';
        Application::$app->classCheck($prefix);
        $obj = new $prefix();
        Application::$app->response->setResponse(200, 'application/json', ['msg' => $obj->init()]);
    }

}