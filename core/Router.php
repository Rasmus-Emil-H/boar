<?php

/*******************************
 * Bootstrap Router 
 * AUTHOR: RE_WEB
 * @package app\core\Router
*/

namespace app\core;

use app\core\Regex;

use app\core\exceptions\NotFoundException;

class Router {

    protected array $routes = [];
    protected Regex $pattern;

    public Request $request;
    public Response $response;

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
        $this->pattern = new Regex($this->request->getPath());
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

        var_dump($this->pattern);

        $path = $this->request->getPath();
        $method = $this->request->method();
        $callback = $this->routes[$method][$path] ?? false;

        if($callback === false) 
            throw new NotFoundException();
        if (is_string($callback)) 
            return Application::$app->view->renderView($callback);
        if (is_array($callback)) {
            $controller = new $callback[0]();
            Application::$app->controller = $controller;
            $controller->action = $callback[1];
            $callback[0] = $controller;
            foreach ($controller->getMiddlewares() as $middleware) 
                $middleware->execute();
        }

        return call_user_func($callback, $this->request, $this->response);
    }

}