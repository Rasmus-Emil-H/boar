<?php

/**
 * Bootstrap Router 
 * AUTHOR: RE_WEB
 * @package app\core\Router
*/

namespace app\core;

use app\core\Regex;
use app\core\exceptions\NotFoundException;
use app\core\middlewares\AuthMiddleware;
use app\controllers;

class Router {

    protected string $method;
    protected array $routes = [];
    protected array $queryPattern;

    public Request $request;
    public Response $response;

    protected const CONTROLLER   = 'Controller';
    protected const INDEX_METHOD = 'index';

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
        $this->queryPattern = Application::$app->regex->validateRoute();
    }

    protected function checkController() {
        if (empty($this->queryPattern)) $this->defaultRoute();
        $handler = ucfirst($this->queryPattern[0] ?? '').self::CONTROLLER;
        $controller = '\\app\controllers\\'.$handler;
        if (!class_exists($controller)) throw new NotFoundException();
        $currentController = new $controller();
        Application::$app->setController($currentController);
    }

    public function defaultRoute() {
        $this->location(Application::$defaultRoute);
    }

    public function location(string $location): void {
        header('Location: ' . $location);
        exit;
    }

    protected function checkMethod() {
        $method = $this->queryPattern[1] ?? $this->method = self::INDEX_METHOD;
        if (!method_exists(Application::$app->controller, $method)) throw new NotFoundException();
        $this->method = $method;
    }

    protected function runMiddlewares() {
        foreach (Application::$app->controller->getMiddlewares() as $middleware) $middleware->execute();
    }

    /** 
     * Resolver for the routing module
     * Middlewares are controller implemented
    */

    public function resolve() {

        $this->checkController();
        $this->checkMethod();
        $this->runMiddlewares();

        Application::$app->controller->{$this->method}();

    }

}