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
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if($callback === false) {
            $this->response->setStatusCode(404);
            return $this->renderView('404');
        }
        if (is_string($callback)) return $this->renderView($callback);
        if (is_array($callback)) $callback[0] = new $callback[0]();
        return call_user_func($callback);
    }

    public function renderView(string $view, array $params = []) {
        $layoutContent = $this->getLayoutContent();
        $viewContent   = $this->renderOnlyView($view, $params);
        return preg_replace('/{{content}}/', $viewContent, $layoutContent);
    }

    protected function getLayoutContent() {
        ob_start();
            include_once Application::$ROOT_DIR . "/views/layouts/main.php";
        return ob_get_clean();
    }

    protected function renderOnlyView(string $view, array $params = []) {
        foreach ($params as $key => $value) $$key = $value;
        ob_start();
            include_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }

}