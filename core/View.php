<?php

/*******************************
 * Bootstrap View 
 * AUTHOR: RE_WEB
 * @package app\core\View
*/

namespace app\core;

class View {

    public string $title = '';

    public function renderView(string $view, array $params = []) {
        $viewContent   = $this->renderOnlyView($view, $params);
        $layoutContent = $this->getLayoutContent($params['title'] ?? 'Not set');
        return preg_replace('/{{content}}/', $viewContent, $layoutContent);
    }

    protected function getLayoutContent(string $title) {
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