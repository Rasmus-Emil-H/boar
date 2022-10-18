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

    protected array $routes = [];
    protected Regex $regex;
    protected array $queryPattern;

    public Request $request;
    public Response $response;

    protected const CONTROLLER = 'Controller';

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
        $this->regex = new Regex($this->request->getPath());
        $this->queryPattern = $this->regex->validateRoute();
    }

    public function get($path, $callback) {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['post'][$path] = $callback;
    }

    /** 
     * Resolver for the routing module
     * Middlewares are controller implemented
     * @return callback
    */

    public function resolve() {

        // $path = $this->request->getPath();
        // $method = $this->request->method();
        // $callback = $this->routes[$method][$path] ?? false;

        $callback = true;

        unset($this->queryPattern[0]);

        $handler = ucfirst($this->queryPattern[1]).self::CONTROLLER;

        $controller = '\\app\controllers\\'.$handler;

        if (!class_exists($controller)) $callback = false;
        
        if($callback === false) throw new NotFoundException();

        $currentController = new $controller();
        $method = $this->queryPattern[2] ?? $currentController->defaultRoute;
        if (!method_exists($controller, $method)) $callback = false;

        if($callback === false) throw new NotFoundException();

        Application::$app->setController($currentController);

        foreach ($currentController->getMiddlewares() as $middleware) 
            $middleware->execute();

        $currentController->$method($this->request, $this->response);

    }

}