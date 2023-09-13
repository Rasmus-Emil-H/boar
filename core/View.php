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

    protected string $includesDir      = '/views/includes/';
    protected string $layoutsDir       = '/views/layouts/';
    protected string $partialsDir      = '/views/partials/';
    protected string $viewsDir         = '/views/';
    protected const TPL_FILE_EXTENSION = '.tpl.php';

    protected File $fileHandler;

    public function __construct() {
        $this->fileHandler = new File();
    }

  public function renderView(string $view, array $data = []): string {
        var_dump($data);
        $currentView   = $this->getTemplateContent($view, $data);
        $currentLayout = $this->getLayoutContent($data);
        return preg_replace('/{{content}}/', $currentView, $currentLayout);
    }

    protected function getLayoutContent(array $data): string {
        $layout = Application::$app->controller->layout ?? Application::$app->layout;
          ob_start();
            $this->fileHandler->requireApplicationFile($this->layoutsDir, $layout);
        return ob_get_clean();
    }

    protected function getTemplateContent(string $view, array $data = []): string {
        ob_start(); ?>
            <?php $this->fileHandler->requireApplicationFile($this->viewsDir, $view, $data); ?>
        <?php return ob_get_clean();
    }

    public function getTemplate(string $template): string {
        $templateFile = Application::$ROOT_DIR . $this->partialsDir . $template . self::TPL_FILE_EXTENSION;
        if (!file_exists($templateFile)) throw new NotFoundException();
        return $templateFile;
    }

}
