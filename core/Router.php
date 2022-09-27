<?php

/*******************************
 * Bootstrap Router 
 * AUTHOR: RE_WEB
 * @package app\core\Router
*/

namespace app\core;

use app\core\exceptions\NotFoundException;

class Router {

    protected array $routes = [];

    public Request $request;
    public Response $response;

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }

    public function get($path, $callback) {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve() {
        $path = $this->request->getPath();
        $method = $this->request->method();
        $callback = $this->routes[$method][$path] ?? false;
        if($callback === false) throw new NotFoundException();
        if (is_string($callback)) return $this->renderView($callback);
        if (is_array($callback)) {
            /** @var \app\core\controller\ $controller */
            $controller = new $callback[0]();
            Application::$app->controller = $controller;
            $controller->action = $callback[1];
            $callback[0] = $controller;
            foreach ($controller->getMiddlewares() as $middleware) $middleware->execute();
        }
        return call_user_func($callback, $this->request, $this->response);
    }

    public function renderView(string $view, array $params = []) {
        $layoutContent = $this->getLayoutContent();
        $viewContent   = $this->renderOnlyView($view, $params);
        return preg_replace('/{{content}}/', $viewContent, $layoutContent);
    }

    protected function getLayoutContent() {
        $layout = Application::$app->controller ? Application::$app->controller->layout : Application::$app->layout;
        ob_start();
            include_once Application::$ROOT_DIR . "/views/layouts/$layout.php";
        return ob_get_clean();
    }

    protected function renderOnlyView(string $view, array $params = []) {
        foreach ($params as $key => $value) $$key = $value;
        ob_start();
            include_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }

}