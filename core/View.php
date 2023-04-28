<?php

/*******************************
 * Bootstrap View
 * Probably should create a more generic way to load assets
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
        ob_start(); ?>
            <style> 
                <?php include_once Application::$ROOT_DIR . "/public/css/bootstrap.css"; ?>
                <?php include_once Application::$ROOT_DIR . "/public/css/main.css"; ?>
            </style>
            <?php include_once Application::$ROOT_DIR . "/views/$view.php"; ?>
            <script> 
                <?php include_once Application::$ROOT_DIR . "/public/js/trip.js"; ?> 
            </script>
        <?php return ob_get_clean();
    }

}