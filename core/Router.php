<?php

/*******************************
 * Bootstrap Router 
 * AUTHOR: RE_WEB
 * @package app\core\Router
*/

namespace app\core;

use app\core\Regex;
use app\core\exceptions\NotFoundException;
use app\controllers;

class Router {

    protected string $method;
    protected array $routes = [];
    protected array $queryPattern;

    public Request $request;
    public Response $response;

    protected const CONTROLLER = 'Controller';

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
        $this->queryPattern = Application::$app->regex->validateRoute();
    }

    protected function checkController() {
        $handler = ucfirst($this->queryPattern[1]).self::CONTROLLER;
        $controller = '\\app\controllers\\'.$handler;
        if (!class_exists($controller)) throw new NotFoundException();
        $currentController = new $controller();
        Application::$app->setController($currentController);
    }

    protected function checkMethod() {
        $method = $this->queryPattern[2] ?? Application::$app->controller->defaultRoute;
        if (!method_exists(Application::$app->controller, $method)) 
            throw new NotFoundException();
        $this->method = $method;
    }

    protected function runMiddlewares() {
        foreach (Application::$app->controller->getMiddlewares() as $middleware) 
            $middleware->execute();
    }

    /** 
     * Resolver for the routing module
     * Middlewares are controller implemented
     * @return callback
    */

    public function resolve() {

        $this->checkController();
        $this->checkMethod();
        $this->runMiddlewares();

        Application::$app->controller->{$this->method}($this->request, $this->response);

    }

}