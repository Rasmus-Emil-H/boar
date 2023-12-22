<?php

/**
 * Bootstrap View
 * AUTHOR: RE_WEB
 * @package app\core\View
 */

namespace app\core\src;

use \app\core\src\exceptions\NotFoundException;

class View {

    protected string $partialsDir      = '/views/partials/';
    protected const TPL_FILE_EXTENSION = '.tpl.php';

    public const INVALID_VIEW = 'Invalid view';

    public function renderView() {
        require_once app()->getParentController()->getView(); 
    }

    public function getTemplate(string $template): string {
        $templateFile = app()::$ROOT_DIR . $this->partialsDir . $template . self::TPL_FILE_EXTENSION;
        if (!file_exists($templateFile)) throw new NotFoundException();
        return $templateFile;
    }

    public function render(): void {
        $this->renderView();
    }

}
