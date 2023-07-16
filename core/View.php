<?php

/*******************************
 * Bootstrap View
 * Probably should create a more generic way to load assets
 * AUTHOR: RE_WEB
 * @package app\core\View
*/

namespace app\core;

class View {

    protected $includes = '/views/includes/';
    protected $layouts  = '/views/layouts/';

    public function renderView(string $view, array $params = []) {
        $viewContent   = $this->renderOnlyView($view, $params);
        $layoutContent = $this->getLayoutContent($params['title'] ?? 'Not set');
        return preg_replace('/{{content}}/', $viewContent, $layoutContent);
    }

    protected function getLayoutContent(string $title) {
        $layout = Application::$app->controller ? Application::$app->controller->layout : Application::$app->layout;
        ob_start();
            require_once(Application::$ROOT_DIR . $this->includes . 'header.php');
            include_once Application::$ROOT_DIR . $this->layouts . $layout.'.php';
            require_once(Application::$ROOT_DIR . $this->includes . 'footer.php');
        return ob_get_clean();
    }

    protected function renderOnlyView(string $view, array $params = []) {
        foreach ($params as $key => $value) $$key = $value;
        ob_start(); ?>
            <?php include_once Application::$ROOT_DIR . '/views/'.$view.'.php'; ?>
        <?php return ob_get_clean();
    }

}