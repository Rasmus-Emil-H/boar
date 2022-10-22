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
    public Regex $regex;

    protected const CONTROLLER = 'Controller';

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
        $this->regex = new Regex($this->request->getPath());
    }

    public function setQueryPattern(): void {
        $this->queryPattern = $this->regex->validateRoute();
    }

    protected function checkController(): void {
        $handler = ucfirst($this->queryPattern[0] ?? '').self::CONTROLLER;
        $controller = '\\app\controllers\\'.$handler;
        if (!class_exists($controller)) 
            throw new NotFoundException();
        Application::$app->setController(new $controller());
    }

    protected function checkMethod(): void {
        $method = $this->queryPattern[1] ?? Application::$app->controller->defaultRoute;
        if (!method_exists(Application::$app->controller, $method)) 
            throw new NotFoundException();
        $this->method = $method;
    }

    protected function runMiddlewares(): void {
        foreach (Application::$app->controller->getMiddlewares() as $middleware) 
            $middleware->execute();
    }

    /** 
     * Resolver for the routing module
     * Middlewares are controller implemented
     * @return callback
    */

    public function resolve(): void {
        $this->setQueryPattern();
        $this->checkController();
        $this->checkMethod();
        $this->runMiddlewares();
        Application::$app->controller->{$this->method}($this->request, $this->response);
    }

}