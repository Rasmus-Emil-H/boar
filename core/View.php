<?php

/**
 * Bootstrap View
 * AUTHOR: RE_WEB
 * @package app\core\View
*/

namespace app\core;

use app\core\exceptions\NotFoundException;
use app\core\File;

class View {

    protected string $includes    = '/views/includes/';
    protected string $layouts     = '/views/layouts/';
    protected string $partials    = '/views/partials/';
    protected string $viewsFolder = '/views/';
    protected const TPL_FILE_EXTENSION = '.tpl.php';

    protected File $fileHandler;

    public function __construct() {
        $this->fileHandler = new File();
    }

    public function renderView(string $view, array $params = []) {
        $viewContent   = $this->renderOnlyView($view, $params);
        $layoutContent = $this->getLayoutContent();
        return preg_replace('/{{content}}/', $viewContent, $layoutContent);
    }

    protected function getLayoutContent() {
        $layout = Application::$app->controller->layout ?: Application::$app->layout;
        ob_start();
            $this->socketFiles($layout);
        return ob_get_clean();
    }

    protected function socketFiles(string $layout): void {
        $this->fileHandler->requireApplicationFile($this->partials, 'header');
        $this->fileHandler->requireApplicationFile($this->layouts, $layout);
        $this->fileHandler->requireApplicationFile($this->partials, 'footer');
    }

    protected function renderOnlyView(string $view, array $params = []) {
        foreach ($params as $key => $value) $$key = $value;
        ob_start(); ?>
            <?php $this->fileHandler->requireApplicationFile($this->viewsFolder, $view); ?>
        <?php return ob_get_clean();
    }

    /**
    * @return string
    */
    public function getTemplate(string $template) : string {
        $templateFile = Application::$ROOT_DIR . $this->partials . $template . self::TPL_FILE_EXTENSION;
        if (!file_exists($templateFile)) throw new NotFoundException();
        return $templateFile;
    }

}