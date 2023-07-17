<?php

/**
 * Bootstrap View
 * AUTHOR: RE_WEB
 * @package app\core\View
*/

namespace app\core;

use app\core\exceptions\NotFoundException;

class View {

    protected string $includes = '/views/includes/';
    protected string $layouts  = '/views/layouts/';
    protected string $partials  = '/views/partials/';

    public function renderView(string $view, array $params = []) {
        $viewContent   = $this->renderOnlyView($view, $params);
        $layoutContent = $this->getLayoutContent($params['title'] ?? 'Not set');
        return preg_replace('/{{content}}/', $viewContent, $layoutContent);
    }

    protected function getLayoutContent(string $title) {
        $layout = Application::$app->controller->layout ?: Application::$app->layout;
        ob_start();
            require_once(Application::$ROOT_DIR . $this->partials . 'header.php');
            include_once Application::$ROOT_DIR . $this->layouts . $layout.'.php';
            require_once(Application::$ROOT_DIR . $this->partials . 'footer.php');
        return ob_get_clean();
    }

    protected function renderOnlyView(string $view, array $params = []) {
        foreach ($params as $key => $value) $$key = $value;
        ob_start(); ?>
            <?php include_once Application::$ROOT_DIR . '/views/'.$view.'.php'; ?>
        <?php return ob_get_clean();
    }

    /**
    * @return string
    */
    public function getTemplate(string $template) : string {
        $templateFile = Application::$ROOT_DIR . $this->partials . $template . ".tpl.php";
        if (!file_exists($templateFile)) throw new NotFoundException();
        return $templateFile;
    }

}