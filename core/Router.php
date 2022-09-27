<?php

/*******************************
 * Bootstrap Router 
 * AUTHOR: RE_WEB
 * @package app\core\Router
*/

namespace app\core;

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
        if($callback === false) {
            $this->response->setStatusCode(404);
            return $this->renderView('404');
        }
        if (is_string($callback)) return $this->renderView($callback);
        if (is_array($callback)) {
            Application::$app->controller = new $callback[0]();
            $callback[0] = Application::$app->controller;
        }
        return call_user_func($callback, $this->request, $this->response);
    }

    public function renderView(string $view, array $params = []) {
        $layoutContent = $this->getLayoutContent();
        $viewContent   = $this->renderOnlyView($view, $params);
        return preg_replace('/{{content}}/', $viewContent, $layoutContent);
    }

    protected function getLayoutContent() {
        $layout = Application::$app->controller->layout;
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